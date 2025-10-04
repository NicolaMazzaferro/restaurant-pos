# DATA MODEL

## Entità
- **Users**: {id, name, email, password, role}
- **Categories**: {id, name}
- **Products**: {id, name, category_id, price, stock}
- **Orders**: {id, user_id, status, type, total}
- **OrderItems**: {id, order_id, product_id, quantity, price, subtotal}
- **Receipts**: {id, order_id, total, payment_method, issued_at}

## Relazioni
- Category 1—N Product
- User 1—N Order
- Order 1—N OrderItem
- Order 1—1 Receipt
- Product 1—N OrderItem

## Note
- `price`/`subtotal`/`total` sono DECIMAL(10,2).
- `quantity` e `stock` sono unsigned integer (no negativi).
- Stati ordine: `open | paid | cancelled`.
- Tipi: `in_store | takeaway`.
- Payment: `cash | card | other`.
