<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    /**
     * Endpoint chiamato da Tauri:
     * GET /api/updates/{target}/{current_version}
     *
     * Esempio:
     *   /api/updates/windows/0.7.0
     *   /api/updates/linux/0.7.0
     *   /api/updates/darwin/0.7.0
     */
    public function check(Request $request, string $target, string $currentVersion)
    {
        try {
            // 🔹 Configurazione da config/update.php
            $latest   = config('update.latest');
            $baseUrl  = rtrim(config('update.base_url'), '/');
            $pattern  = data_get(config('update.platforms'), "{$target}.pattern");

            if (!$pattern) {
                Log::warning("[Updater] Unsupported platform: {$target}");
                return response()->json(['error' => 'Unsupported platform'], 400);
            }

            // 🔹 Nessun aggiornamento disponibile
            if (version_compare($currentVersion, $latest, '>=')) {
                Log::info("[Updater] {$target} v{$currentVersion} already up to date (latest: {$latest})");
                return response('', 204);
            }

            // 🔹 Costruisci percorso file e firma
            $file = str_replace(':version', $latest, $pattern);
            $diskPath = "updates/{$file}";
            $sigPath  = public_path("storage/updates/{$file}.sig");

            if (!Storage::disk('public')->exists($diskPath)) {
                Log::error("[Updater] Missing file: {$diskPath}");
                return response()->json(['error' => 'Update file not found'], 404);
            }

            if (!file_exists($sigPath)) {
                Log::error("[Updater] Missing signature: {$sigPath}");
                return response()->json(['error' => 'Signature not found'], 404);
            }

            $url = "{$baseUrl}/{$file}";
            $signature = trim(file_get_contents($sigPath));

            // 🔹 Costruisci chiave piattaforma corretta (es. windows-x86_64)
            $arch = $this->resolveArchitecture($target);
            $platformKey = "{$target}-{$arch}";

            // 🔹 Costruisci risposta JSON conforme a Tauri v2
            $response = [
                'version'   => $latest,
                'notes'     => config('update.notes', 'Bugfix e miglioramenti'),
                'pub_date'  => now()->toIso8601String(),
                'platforms' => [
                    $platformKey => [
                        'signature' => $signature,
                        'url'       => $url,
                    ],
                ],
            ];

            Log::info("[Updater] Serving update JSON", $response);

            return response()->json($response, 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Throwable $e) {
            Log::error("[Updater] Exception: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Risolve l'architettura predefinita per ogni piattaforma
     */
    private function resolveArchitecture(string $target): string
    {
        return match ($target) {
            'windows' => 'x86_64',
            'linux'   => 'x86_64',
            'darwin'  => 'x86_64', // usa 'aarch64' se buildi per Apple Silicon
            default   => 'x86_64',
        };
    }
}
