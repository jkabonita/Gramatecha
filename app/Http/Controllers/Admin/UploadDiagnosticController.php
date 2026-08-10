<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadDiagnosticController extends Controller
{
    /**
     * Run upload diagnostics to identify bottlenecks.
     */
    public function diagnose(Request $request)
    {
        $diagnostics = [];
        
        // 1. Check PHP configuration
        $diagnostics['php_config'] = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_input_time' => ini_get('max_input_time'),
        ];
        
        // 2. Test disk write speed
        $startTime = microtime(true);
        $testData = str_repeat('x', 10 * 1024 * 1024); // 10MB of data
        $testPath = 'test_write_speed_' . time() . '.tmp';
        Storage::disk('public')->put($testPath, $testData);
        $writeTime = round((microtime(true) - $startTime) * 1000, 2);
        Storage::disk('public')->delete($testPath);
        
        $diagnostics['disk_performance'] = [
            'write_speed_10mb' => $writeTime . ' ms',
            'write_speed_mbps' => round((10 / $writeTime) * 1000, 2) . ' MB/s',
            'storage_path' => Storage::disk('public')->path(''),
            'disk_free_space' => round(disk_free_space(Storage::disk('public')->path('')) / 1024 / 1024 / 1024, 2) . ' GB',
        ];
        
        // 3. Check storage configuration
        $diagnostics['storage_config'] = [
            'default_disk' => config('filesystems.default'),
            'public_driver' => config('filesystems.disks.public.driver'),
            'public_root' => config('filesystems.disks.public.root'),
        ];
        
        // 4. Performance recommendations
        $recommendations = [];
        
        if ($writeTime > 500) {
            $recommendations[] = 'CRITICAL: Disk write speed is very slow (' . $writeTime . 'ms for 10MB). Consider using SSD or checking antivirus settings.';
        } elseif ($writeTime > 200) {
            $recommendations[] = 'WARNING: Disk write speed is slow (' . $writeTime . 'ms for 10MB). Consider optimizing storage.';
        } else {
            $recommendations[] = 'GOOD: Disk write speed is acceptable (' . $writeTime . 'ms for 10MB).';
        }
        
        if (ini_get('max_execution_time') < 300) {
            $recommendations[] = 'Consider increasing max_execution_time to 600 seconds for large uploads.';
        }
        
        $diagnostics['recommendations'] = $recommendations;
        
        return response()->json($diagnostics);
    }
}
