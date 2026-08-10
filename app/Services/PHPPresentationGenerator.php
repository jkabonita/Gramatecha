<?php

namespace App\Services;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Slide\SlideLayout;

class PHPPresentationGenerator
{
    /**
     * Generate a sample presentation with title and content.
     *
     * @param  string  $title
     * @param  string|null  $description
     * @param  array  $slides  Array of slides with 'title' and 'content'
     * @return PhpPresentation
     */
    public function generatePresentation(
        string $title,
        ?string $description = null,
        array $slides = []
    ): PhpPresentation {
        $presentation = new PhpPresentation();
        
        // Set document properties
        $presentation->getDocumentProperties()
            ->setCreator('Gramatecha')
            ->setTitle($title)
            ->setDescription($description ?? '')
            ->setSubject('Generated Presentation')
            ->setKeywords('presentation, gramatecha, slides');

        // Remove default slide
        $presentation->removeSlideByIndex(0);

        // Add slides
        if (empty($slides)) {
            // Create a default title slide
            $this->createTitleSlide($presentation, $title, $description);
        } else {
            foreach ($slides as $index => $slideData) {
                if ($index === 0) {
                    // First slide is always the title slide
                    $this->createTitleSlide(
                        $presentation,
                        $slideData['title'] ?? $title,
                        $slideData['content'] ?? $description
                    );
                } else {
                    // Content slides
                    $this->createContentSlide(
                        $presentation,
                        $slideData['title'] ?? "Slide $index",
                        $slideData['content'] ?? ''
                    );
                }
            }
        }

        return $presentation;
    }

    /**
     * Create a title slide.
     */
    protected function createTitleSlide(
        PhpPresentation $presentation,
        string $title,
        ?string $subtitle = null
    ): void {
        $slide = $presentation->createSlide();
        $slide->setSlideLayout(SlideLayout::TITLE_SLIDE);

        // Add title
        $titleShape = $slide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(800)
            ->setOffsetX(100)
            ->setOffsetY(150);
        
        $titleShape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $titleRun = $titleShape->createTextRun($title);
        $titleRun->getFont()
            ->setBold(true)
            ->setSize(44)
            ->setColor(new Color('FF2C3E50'));

        // Add subtitle if provided
        if ($subtitle) {
            $subtitleShape = $slide->createRichTextShape()
                ->setHeight(100)
                ->setWidth(800)
                ->setOffsetX(100)
                ->setOffsetY(300);
            
            $subtitleShape->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $subtitleRun = $subtitleShape->createTextRun($subtitle);
            $subtitleRun->getFont()
                ->setSize(24)
                ->setColor(new Color('FF7F8C8D'));
        }
    }

    /**
     * Create a content slide with title and body.
     */
    protected function createContentSlide(
        PhpPresentation $presentation,
        string $title,
        string $content
    ): void {
        $slide = $presentation->createSlide();
        $slide->setSlideLayout(SlideLayout::TITLE_CONTENT);

        // Add title
        $titleShape = $slide->createRichTextShape()
            ->setHeight(60)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(50);
        
        $titleRun = $titleShape->createTextRun($title);
        $titleRun->getFont()
            ->setBold(true)
            ->setSize(32)
            ->setColor(new Color('FF2C3E50'));

        // Add content
        $contentShape = $slide->createRichTextShape()
            ->setHeight(400)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(150);
        
        $contentRun = $contentShape->createTextRun($content);
        $contentRun->getFont()
            ->setSize(18)
            ->setColor(new Color('FF34495E'));
    }

    /**
     * Save presentation to a specific format (PowerPoint2007 or ODPresentation).
     *
     * @param  PhpPresentation  $presentation
     * @param  string  $filePath  Full path including filename
     * @param  string  $format  'PowerPoint2007' or 'ODPresentation'
     * @return void
     *
     * @throws \Exception
     */
    public function savePresentation(
        PhpPresentation $presentation,
        string $filePath,
        string $format = 'PowerPoint2007'
    ): void {
        $writer = IOFactory::createWriter($presentation, $format);
        $writer->save($filePath);
    }

    /**
     * Get the file extension for a specific format.
     *
     * @param  string  $format
     * @return string
     */
    public function getFileExtension(string $format): string
    {
        return match ($format) {
            'PowerPoint2007' => 'pptx',
            'ODPresentation' => 'odp',
            default => 'pptx',
        };
    }
}
