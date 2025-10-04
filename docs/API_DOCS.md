# API DOCS (MVP Cassa)

Base URL: `http://localhost:8080`

## Auth
POST /api/auth/login
- body: `{ "email": "admin@example.com", "password": "password" }`
- 200:  `{ "token": "xxxxx", "token_type": "Bearer" }`
- 401:  `{ message: "Invalid credentials" }`

## Products
GET    /api/products
POST   /api/products
GET    /api/products/{id}
PUT    /api/products/{id}
DELETE /api/products/{id}

###### Create example:
- `{ "name":"Margherita","price":6.5,"stock":100,"category_id":1 }`

## Orders
GET  /api/orders
POST /api/orders
GET  /api/orders/{id}

###### Create (paid) example:
``` 
{
  "type":"in_store",
  "payment_method":"cash",
  "items":[
    {"product_id":1,"quantity":2},
    {"product_id":2,"quantity":1}
  ]
}
```

- 201: 
``` 
{
    "data": {
        "id": 12,
        "status": "paid",
        "type": "in_store",
        "total": 21.0,
        "items": [ ... ],
        "receipt": { "total": 21.0, "payment_method": "cash", "issued_at": "..." }
    }
}
```




GET  /api/orders/{id}/receipt/pdf