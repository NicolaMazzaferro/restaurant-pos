# ARCHITECTURE

## API-first
Solo API JSON. Nessuna Blade per UI (eccetto layout PDF). Frontend (POS) potrà essere SPA esterna.

## Layers
- **Controller (API)**: input HTTP -> chiama Service.
- **Service**: transazioni e logica (calcolo totale ordine, creazione receipt).
- **Repository**: accesso dati (Eloquent). Interfacce per testing/sostituibilità.
- **Models/Enums**: dominio (Products, Orders, Items, Receipts).

## Flusso ordine
1. `OrderController@store` valida il payload.
2. `OrderService::create()` apre **transaction**:
   - crea `Order`
   - per ogni item: `OrderItem` e (opzionale) decrementa stock
   - calcola `total` e aggiorna `Order`
   - se c'è `payment_method`, setta `status=PAID` e crea `Receipt`
3. ritorna `OrderResource` con `items` e `receipt`.

## PDF
- `ReceiptController@pdf` genera **courtesy receipt** (non fiscale) con DomPDF.

## Stampa fiscale (futuro)
- `PrintAgentAdapterInterface` + `NullPrintAgentAdapter` (stub).
- In futuro: `HttpPrintAgentAdapter` che invia JSON a servizio C# locale.
