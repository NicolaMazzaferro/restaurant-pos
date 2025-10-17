<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    /**
     * Endpoint chiamato da Tauri:
     * GET /api/updates/{target}/{current_version}
     *
     * Esempio: /api/updates/windows/0.8.0
     */
    public function check(Request $request, string $target, string $currentVersion)
    {
        $latest   = config('update.latest');
        $baseUrl  = rtrim(config('update.base_url'), '/');
        $pattern  = data_get(config('update.platforms'), "{$target}.pattern");

        if (!$pattern) {
            return response()->json(['error' => 'Unsupported platform'], 400);
        }

        if (version_compare($currentVersion, $latest, '>=')) {
            return response('', 204); // nessun update
        }

        $file = str_replace(':version', $latest, $pattern);
        $diskPath = "updates/{$file}";
        $sigPath  = public_path("storage/updates/{$file}.sig");

        if (!Storage::disk('public')->exists("updates/{$file}")) {
            return response()->json(['error' => 'Update file not found'], 404);
        }
        if (!file_exists($sigPath)) {
            return response()->json(['error' => 'Signature not found'], 404);
        }

        return response()->json([
            'version' => $latest,
            'notes' => "Bugfix e miglioramenti",
            'pub_date' => now()->toIso8601String(),
            'platforms' => [
                'windows' => [
                    'signature' => $signature,
                    'url' => $url,
                ],
            ],
        ]);
    }
}
