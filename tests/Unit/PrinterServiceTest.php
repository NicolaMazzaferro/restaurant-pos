<?php

use App\Models\Printer;
use App\Repositories\PrinterRepository;
use App\Services\PrinterService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit Test - PrinterService
 *
 * Testa la logica di business indipendentemente dal controller:
 * - salvataggio in transazione
 * - reset del database
 */

uses(RefreshDatabase::class);

it('saves all printers correctly using service', function () {
    $service = new PrinterService(new PrinterRepository());

    $printers = [
        [
            'id' => (string) Str::uuid(),
            'name' => 'Test 1',
            'model' => 'bixolon',
            'header' => 'Header 1',
            'address' => 'Via Uno',
            'city' => 'Roma',
            'phone' => '111111',
            'vat' => 'IT1234567890',
            'printer_port' => '\\\\.\\COM1',
        ],
        [
            'id' => (string) Str::uuid(),
            'name' => 'Test 2',
            'model' => 'epson',
            'header' => 'Header 2',
            'address' => 'Via Due',
            'city' => 'Milano',
            'phone' => '222222',
            'vat' => 'IT9876543210',
            'printer_port' => '192.168.1.10:9100',
        ],
    ];

    $service->saveAll($printers);

    expect(Printer::count())->toBe(2);
    expect(Printer::first()->name)->toBe('Test 1');
});

it('replaces all printers on saveAll', function () {
    $service = new PrinterService(new PrinterRepository());

    // Prima inseriamo 1 stampante
    $service->saveAll([[
        'id' => (string) Str::uuid(),
        'name' => 'Old Printer',
        'model' => 'bixolon',
    ]]);

    expect(Printer::count())->toBe(1);

    // Ora chiamiamo saveAll con 2 nuove
    $service->saveAll([
        ['id' => (string) Str::uuid(), 'name' => 'New 1'],
        ['id' => (string) Str::uuid(), 'name' => 'New 2'],
    ]);

    expect(Printer::count())->toBe(2);
    expect(Printer::pluck('name')->toArray())->toContain('New 1', 'New 2');
});
