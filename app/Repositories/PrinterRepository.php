<?php

namespace App\Repositories;

use App\Models\Printer;
use Illuminate\Database\Eloquent\Collection;

class PrinterRepository
{
    public function all(): Collection
    {
        return Printer::all();
    }

    public function find(string $id): ?Printer
    {
        return Printer::find($id);
    }

    public function create(array $data): Printer
    {
        return Printer::create($data);
    }

    public function update(Printer $printer, array $data): Printer
    {
        $printer->update($data);
        return $printer;
    }

    public function delete(Printer $printer): void
    {
        $printer->delete();
    }
}
