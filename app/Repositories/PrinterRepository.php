<?php

namespace App\Repositories;

use App\Models\Printer;
use Illuminate\Database\Eloquent\Collection;

class PrinterRepository
{
    public function all()
    {
        return Printer::all();
    }

    public function updateOrCreate(array $where, array $data)
    {
        return Printer::updateOrCreate($where, $data);
    }

    public function find(string $id): ?Printer
    {
        return Printer::find($id);
    }

    public function delete(Printer $printer): void
    {
        $printer->delete();
    }
}
