# API Endpoint: Random Contact Data

## Descripción
Este endpoint obtiene un contacto aleatorio de GoHighLevel junto con toda su información relacionada (oportunidades, pagos, suscripciones y transacciones).

## Endpoints Disponibles

### 1. API Route (Requiere autenticación)
```
GET /api/random-contact-data
```

### 2. Web Route (Para testing, requiere estar logueado en admin)
```
GET /admin/test/random-contact-data
```

## Respuesta de Ejemplo

```json
{
  "success": true,
  "data": {
    "contact": {
      "id": "contact_id",
      "email": "email@example.com",
      "firstName": "John",
      "lastName": "Doe",
      "phone": "+1234567890",
      "tags": ["creetelo_mensual"],
      "locationId": "location_id",
      // ... otros campos del contacto
    },
    "opportunities": [
      {
        "id": "opportunity_id",
        "contactId": "contact_id",
        "status": "open",
        "value": 100.00,
        // ... otros campos de oportunidad
      }
    ],
    "payments": [
      {
        "id": "payment_id",
        "contactId": "contact_id",
        "amount": 100.00,
        "status": "completed",
        // ... otros campos de pago
      }
    ],
    "subscriptions": [
      {
        "id": "subscription_id",
        "contactId": "contact_id",
        "status": "active",
        "amount": 100.00,
        // ... otros campos de suscripción
      }
    ],
    "transactions": [
      {
        "id": "transaction_id",
        "contactId": "contact_id",
        "amount": 100.00,
        "type": "payment",
        // ... otros campos de transacción
      }
    ]
  },
  "message": "Random contact data retrieved successfully"
}
```

## Manejo de Errores

Si ocurre un error, la respuesta será:

```json
{
  "error": "Descripción del error",
  "message": "Mensaje detallado del error"
}
```

### Códigos de Estado Posibles:
- `200`: Éxito
- `401`: Token de autenticación inválido
- `404`: No se encontraron contactos
- `500`: Error interno del servidor

## Funcionalidades Implementadas

1. **Verificación automática de token**: El endpoint verifica si el token de GoHighLevel es válido
2. **Renovación automática de token**: Si el token ha expirado, intenta renovarlo automáticamente
3. **Selección aleatoria**: Obtiene un contacto aleatorio de la lista de contactos disponibles
4. **Filtrado por contacto**: Obtiene solo los datos relacionados al contacto seleccionado
5. **Manejo de errores**: Cada servicio maneja sus errores de forma independiente
6. **Respuesta estructurada**: Retorna todos los datos en un formato JSON organizado

## Servicios Utilizados

- `Contacts`: Para obtener la lista de contactos
- `Opportunity`: Para obtener oportunidades del contacto
- `Payments`: Para obtener pagos/transacciones del contacto
- `Subscriptions`: Para obtener suscripciones del contacto
- `Transactions`: Para obtener transacciones específicas del contacto
- `GoHighLevel`: Para verificar y renovar tokens de autenticación

## Notas de Implementación

- El endpoint primero obtiene todos los contactos y luego selecciona uno aleatorio
- Cada tipo de dato (opportunities, payments, etc.) se obtiene de forma independiente
- Si algún servicio falla, se incluye un mensaje de error en la respuesta pero el endpoint continúa
- Los filtros por contactId se implementaron en los servicios para mejorar la eficiencia
