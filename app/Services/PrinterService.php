<?php

namespace App\Services;

use App\Models\Printer;
use App\Repositories\PrinterRepository;
use Illuminate\Support\Facades\DB;

class PrinterService
{
    public function __construct(
        private PrinterRepository $repo
    ) {}

    public function getAll()
    {
        return $this->repo->all();
    }


    public function saveAll(array $printers): void
    {
        DB::transaction(function () use ($printers) {
            // Cancella tutto e riscrive (semplice per MVP)
            Printer::truncate();

            foreach ($printers as $printer) {
                $this->repo->create($printer);
            }
        });
    }
}
