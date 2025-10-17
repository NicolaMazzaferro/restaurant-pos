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
        // 🔹 1. Imposta la versione più recente disponibile
        $latest = '0.8.1'; // <-- aggiorna ogni volta che pubblichi una nuova build

        // 🔹 2. Se l’utente è già aggiornato → nessun update
        if (version_compare($currentVersion, $latest, '>=')) {
            return response()->json([]);
        }

        // 🔹 3. Nome file in base alla piattaforma
        // target può essere: windows, linux, darwin (macOS)
        $file = match ($target) {
            'windows' => "Gestionale-A-Villetta_{$latest}_x64-setup.exe",
            'linux'   => "Gestionale-A-Villetta_{$latest}_amd64.AppImage",
            'darwin'  => "Gestionale-A-Villetta_{$latest}.dmg",
            default   => null,
        };

        if (!$file || !Storage::disk('public')->exists("updates/{$file}")) {
            return response()->json(['error' => 'Update file not found'], 404);
        }

        // 🔹 4. Costruisci URL pubblico e leggi firma
        $url = asset("storage/updates/{$file}");
        $sigPath = public_path("storage/updates/{$file}.sig");

        if (!file_exists($sigPath)) {
            return response()->json(['error' => 'Signature not found'], 404);
        }

        $signature = trim(file_get_contents($sigPath));

        // 🔹 5. Risposta compatibile con Tauri Updater
        return response()->json([
            'version' => $latest,
            'notes' => "Aggiornamento automatico disponibile!\n\n- Migliorata la stabilità\n- Correzione bug minori\n- Aggiunto supporto stampa Hydra",
            'pub_date' => now()->toIso8601String(),
            'platforms' => [
                $target => [
                    'signature' => $signature,
                    'url' => $url,
                ],
            ],
        ]);
    }
}
