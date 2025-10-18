<?php

namespace App\Services;

use App\Models\Printer;
use App\Repositories\PrinterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    public function __construct(
        private PrinterRepository $repo
    ) {}

    /**
     * Restituisce tutte le stampanti dal database.
     */
    public function getAll()
    {
        return $this->repo->all();
    }

    /**
     * Salva (sovrascrivendo) tutte le configurazioni stampanti.
     *
     * Usa una transazione atomica per garantire consistenza:
     * - Truncate elimina tutte le righe esistenti
     * - Ogni stampante viene reinserita ex-novo
     */
    public function saveAll(array $printers): void
    {
        DB::transaction(function () use ($printers) {
            Log::info('Salvataggio configurazioni stampanti', [
                'count' => count($printers)
            ]);

            // 1️⃣ Pulisce la tabella (operazione atomica)
            Printer::truncate();

            // 2️⃣ Inserisce nuove configurazioni
            foreach ($printers as $printer) {
                // Forza coerenza dati (Laravel ignora campi extra)
                $this->repo->create([
                    'id' => $printer['id'] ?? uuid_create(UUID_TYPE_RANDOM),
                    'name' => $printer['name'] ?? 'Unknown printer',
                    'model' => $printer['model'] ?? null,
                    'header' => $printer['header'] ?? '',
                    'address' => $printer['address'] ?? '',
                    'city' => $printer['city'] ?? '',
                    'phone' => $printer['phone'] ?? '',
                    'vat' => $printer['vat'] ?? '',
                    'printer_port' => $printer['printer_port'] ?? '',
                ]);
            }

            Log::info('Configurazioni stampanti salvate con successo.');
        });
    }
}
