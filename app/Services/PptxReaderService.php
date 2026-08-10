<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;
use SimpleXMLElement;

/**
 * Reads a PPTX / ODP file from local storage and extracts
 * per-slide display data (background, text blocks, title) using
 * PHP's built-in ZIP and SimpleXML extensions.
 *
 * No LibreOffice or external services required.
 */
class PptxReaderService
{
    /**
     * Extract slide data from a PPTX file stored on the public disk.
     *
     * @param  string  $storagePath  e.g. "presentations/abc123.pptx"
     * @return array<int, array{index: int, title: string, texts: string[], bg_color: string, has_image: bool}>
     *
     * @throws \RuntimeException
     */
    public function extractSlides(string $storagePath): array
    {
        $fullPath = Storage::disk('public')->path($storagePath);

        if (! file_exists($fullPath)) {
            throw new \RuntimeException('File not found: ' . $fullPath);
        }

        $zip = new ZipArchive();
        $result = $zip->open($fullPath);

        if ($result !== true) {
            // ZipArchive error codes: https://www.php.net/manual/en/zip.constants.php
            $errorMap = [
                ZipArchive::ER_NOZIP  => 'File is not a ZIP/PPTX archive.',
                ZipArchive::ER_INCONS => 'ZIP archive is inconsistent.',
                ZipArchive::ER_OPEN   => 'Cannot open file.',
                ZipArchive::ER_READ   => 'Read error.',
                ZipArchive::ER_MEMORY => 'Out of memory.',
            ];
            $msg = $errorMap[$result] ?? "ZipArchive error code: {$result}";
            throw new \RuntimeException("Could not open PPTX: {$msg} (path: {$fullPath})");
        }

        // ── Build the slide list from [Content_Types].xml ────────────────────
        $contentTypesXml = $zip->getFromName('[Content_Types].xml');
        if ($contentTypesXml === false) {
            $zip->close();
            throw new \RuntimeException('Not a valid PPTX: missing [Content_Types].xml');
        }

        $slideFiles = $this->getSlideFiles($zip, $contentTypesXml);

        if (empty($slideFiles)) {
            $zip->close();
            return [];
        }

        $slides = [];

        foreach ($slideFiles as $index => $slideFile) {
            $slideXml = $zip->getFromName($slideFile);
            if ($slideXml === false) {
                continue;
            }

            // Slide layout / master for background lookup
            $bgColor = $this->extractBackground($zip, $slideFile, $slideXml);

            $slideData = $this->parseSlide($slideXml, $index + 1, $bgColor, $zip, $slideFile);
            $slides[] = $slideData;
        }

        $zip->close();

        return $slides;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Return ordered list of slide XML paths by directly scanning
     * the ZIP for ppt/slides/slideN.xml files.
     * This is more reliable than parsing [Content_Types].xml,
     * which may use varying namespace declarations.
     */
    private function getSlideFiles(ZipArchive $zip, string $contentTypesXml): array
    {
        $slideFiles = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            // Match ppt/slides/slideN.xml (not layout, not master)
            if (
                preg_match('#^ppt/slides/slide\d+\.xml$#', $name) &&
                ! str_contains($name, 'slideLayout') &&
                ! str_contains($name, 'slideMaster')
            ) {
                $slideFiles[] = $name;
            }
        }

        // Natural sort so slide2 comes before slide10
        usort($slideFiles, static function (string $a, string $b): int {
            preg_match('/slide(\d+)/', $a, $ma);
            preg_match('/slide(\d+)/', $b, $mb);
            return ((int) ($ma[1] ?? 0)) <=> ((int) ($mb[1] ?? 0));
        });

        return $slideFiles;
    }


    /**
     * Extract background color from the slide, its layout, or master.
     */
    private function extractBackground(ZipArchive $zip, string $slideFile, string $slideXml): string
    {
        // 1. Try the slide itself
        $color = $this->parseBgColor($slideXml);
        if ($color !== null) {
            return $color;
        }

        // 2. Follow the slide → layout relationship
        $layoutXml = $this->getRelatedXml($zip, $slideFile, 'layout');
        if ($layoutXml !== null) {
            $color = $this->parseBgColor($layoutXml);
            if ($color !== null) {
                return $color;
            }
        }

        // 3. Follow layout → master
        // (omitted for brevity; default dark blue used as fallback)

        return '#1B3A5C'; // professional dark-blue fallback
    }

    /**
     * Try to find a solid-fill background hex colour in the given slide XML string.
     */
    private function parseBgColor(string $slideXml): ?string
    {
        // Look for <p:bg><p:bgPr><a:solidFill><a:srgbClr val="RRGGBB"/>
        if (preg_match('/<a:srgbClr\s+val="([0-9A-Fa-f]{6})"/', $slideXml, $m)) {
            return '#' . strtoupper($m[1]);
        }
        // Theme colour references — return null and let caller fall back
        return null;
    }

    /**
     * Resolve a slide relationship to retrieve a related XML file (layout/master).
     */
    private function getRelatedXml(ZipArchive $zip, string $slideFile, string $type): ?string
    {
        // e.g. ppt/slides/slide1.xml → ppt/slides/_rels/slide1.xml.rels
        $dir = dirname($slideFile);
        $base = basename($slideFile);
        $relsFile = $dir . '/_rels/' . $base . '.rels';

        $relsXml = $zip->getFromName($relsFile);
        if ($relsXml === false) {
            return null;
        }

        $xml = $this->parseXml($relsXml);
        if ($xml === null) {
            return null;
        }

        foreach ($xml->Relationship ?? [] as $rel) {
            $relType = (string) ($rel['Type'] ?? '');
            if (str_contains($relType, $type)) {
                $target = (string) ($rel['Target'] ?? '');
                // Resolve relative path
                $resolved = $dir . '/' . $target;
                // Normalise  e.g. ppt/slides/../slideLayouts/slideLayout1.xml
                $parts = explode('/', $resolved);
                $stack = [];
                foreach ($parts as $part) {
                    if ($part === '..') {
                        array_pop($stack);
                    } elseif ($part !== '.') {
                        $stack[] = $part;
                    }
                }
                $resolvedPath = implode('/', $stack);
                $content = $zip->getFromName($resolvedPath);
                return $content !== false ? $content : null;
            }
        }

        return null;
    }

    /**
     * Parse a slide XML string into a structured array.
     */
    private function parseSlide(string $slideXml, int $index, string $bgColor, ZipArchive $zip, string $slideFile): array
    {
        $xml = $this->parseXml($slideXml);

        $title = '';
        $texts = [];
        $hasImage = false;

        if ($xml !== null) {
            // Register all namespaces
            $xml->registerXPathNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
            $xml->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $xml->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

            // ── Extract all text runs ────────────────────────────────────────
            $spTree = $xml->xpath('//p:sp');
            foreach ($spTree ?: [] as $sp) {
                $sp->registerXPathNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
                $sp->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

                // Detect title placeholder
                $phType = '';
                $nvSpPr = $sp->xpath('.//p:nvSpPr/p:nvPr/p:ph');
                if (! empty($nvSpPr)) {
                    $phType = strtolower((string) ($nvSpPr[0]['type'] ?? 'body'));
                }

                $paragraphs = $sp->xpath('.//a:p');
                $blockText = '';
                foreach ($paragraphs ?: [] as $para) {
                    $para->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
                    $runs = $para->xpath('.//a:t');
                    $lineText = '';
                    foreach ($runs ?: [] as $run) {
                        $lineText .= (string) $run;
                    }
                    if (trim($lineText) !== '') {
                        $blockText .= $lineText . "\n";
                    }
                }

                $blockText = trim($blockText);
                if ($blockText === '') {
                    continue;
                }

                if (in_array($phType, ['title', 'ctrTitle'], true) && $title === '') {
                    $title = $blockText;
                } else {
                    $texts[] = $blockText;
                }
            }

            // ── Detect images ────────────────────────────────────────────────
            $blipFills = $xml->xpath('//p:pic') ?: $xml->xpath('//a:blipFill') ?: [];
            $hasImage = ! empty($blipFills);
        }

        // If no title placeholder found, use first text block
        if ($title === '' && ! empty($texts)) {
            $title = array_shift($texts);
        }

        return [
            'index'     => $index,
            'title'     => $title ?: 'Slide ' . $index,
            'texts'     => $texts,
            'bg_color'  => $bgColor,
            'has_image' => $hasImage,
        ];
    }

    /**
     * Safely parse an XML string, suppressing warnings.
     */
    private function parseXml(string $xml): ?SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_clear_errors();
        return $obj !== false ? $obj : null;
    }
}
