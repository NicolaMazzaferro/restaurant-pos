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
        $latest = '0.7.1';

        if (version_compare($currentVersion, $latest, '>=')) {
            // in v2, dynamic server: 204 quando non c'è update
            return response('', 204);
        }

        // scegli il file in base alla piattaforma/architettura se vuoi
        $file = "gestionale-a-villetta_{$latest}_x64-setup.nsis.zip"; // <-- artefatto updater (zip), non l'.exe
        $sig  = "{$file}.sig";

        $url = asset("storage/updates/{$file}");
        $signature = trim(file_get_contents(public_path("storage/updates/{$sig}")));

        return response()->json([
            "version"   => $latest,
            "url"       => $url,
            "signature" => $signature,
            "notes"     => "Bugfix & migliorie"
        ]);
    }

}
