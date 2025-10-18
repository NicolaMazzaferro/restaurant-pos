<?php

namespace App\Services;

use App\Repositories\PrinterRepository;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    public function __construct(
        private PrinterRepository $repo
    ) {}

    /**
     * Restituisce tutte le stampanti.
     */
    public function getAll()
    {
        return $this->repo->all();
    }

    /**
     * Salva o aggiorna tutte le stampanti una per una.
     *
     * Nessuna transazione globale: ogni record è indipendente.
     */
    public function saveAll(array $printers): void
    {
        foreach ($printers as $printer) {
            try {
                $this->repo->updateOrCreate(['id' => $printer['id']], $printer);
            } catch (\Throwable $e) {
                Log::error('Errore salvataggio stampante', [
                    'printer' => $printer['name'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function find(string $id)
    {
        return $this->repo->find($id);
    }

    public function delete($printer): void
    {
        $this->repo->delete($printer);
    }

}
