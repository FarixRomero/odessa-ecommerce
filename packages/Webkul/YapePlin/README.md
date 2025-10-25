# Yape / Plin Payment Method for Bagisto

Método de pago personalizado para Bagisto que permite a los clientes realizar pagos mediante Yape o Plin subiendo el comprobante de depósito.

## Características

- ✅ Método de pago personalizado para Yape/Plin
- ✅ Subida de comprobante de pago (JPG, PNG, PDF)
- ✅ Panel de administración para revisar comprobantes
- ✅ Aprobación/Rechazo de comprobantes
- ✅ Actualización automática del estado del pedido
- ✅ Soporte multiidioma (Español)

## Instalación

El paquete ya está instalado y configurado en este proyecto.

### Pasos realizados:

1. ✅ Estructura de paquete creada en `packages/Webkul/YapePlin/`
2. ✅ Configuración registrada en `config/concord.php`
3. ✅ Autoload configurado en `composer.json`
4. ✅ Migración ejecutada para tabla `yapeplin_receipts`

## Configuración

### 1. Activar el método de pago

1. Ir al panel de administración
2. Navegar a **Configuración > Ventas > Métodos de Pago**
3. Buscar **Yape / Plin**
4. Activar el método y configurar:
   - **Título**: Nombre que verán los clientes
   - **Descripción**: Descripción breve del método
   - **Instrucciones de Pago**: Detalles sobre cómo realizar el pago
   - **Número de Cuenta**: Tu número de Yape/Plin
   - **Titular de la Cuenta**: Nombre del titular
   - **Estado**: Activo
   - **Orden de clasificación**: Orden de aparición

### 2. Configurar permisos de almacenamiento

Asegúrate de que el directorio `storage/app/public/receipts/yapeplin` tenga permisos de escritura:

```bash
mkdir -p storage/app/public/receipts/yapeplin
chmod -R 775 storage/app/public/receipts/yapeplin
```

## Uso

### Para Clientes

1. Seleccionar productos y proceder al checkout
2. Elegir **Yape / Plin** como método de pago
3. Ver las instrucciones de pago y datos de la cuenta
4. Realizar el pago mediante Yape o Plin
5. Subir el comprobante de pago (imagen o PDF)
6. Completar el pedido
7. Esperar la verificación del administrador

### Para Administradores

1. Acceder al panel de administración
2. Ir a la sección de comprobantes Yape/Plin (una vez configurado el menú)
3. Ver listado de comprobantes pendientes
4. Hacer clic en "Ver detalles" para revisar el comprobante
5. Aprobar o rechazar el comprobante:
   - **Aprobar**: El pedido pasa a estado "Processing"
   - **Rechazar**: El pedido pasa a estado "Cancelled"
6. Opcionalmente agregar notas sobre la decisión

### Rutas del Administrador

- **Listado de comprobantes**: `/admin/yapeplin/receipts`
- **Ver comprobante**: `/admin/yapeplin/receipts/{id}`
- **Aprobar**: POST `/admin/yapeplin/receipts/{id}/approve`
- **Rechazar**: POST `/admin/yapeplin/receipts/{id}/reject`

## Estructura del Paquete

```
packages/Webkul/YapePlin/
├── composer.json
├── src/
│   ├── Config/
│   │   ├── paymentmethods.php      # Configuración del método de pago
│   │   └── system.php               # Configuración del sistema
│   ├── Contracts/
│   │   └── Receipt.php              # Contrato del modelo
│   ├── Database/
│   │   └── Migrations/
│   │       └── 2025_01_25_000001_create_yapeplin_receipts_table.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/
│   │       │   └── ReceiptController.php   # Controlador admin
│   │       └── Shop/
│   │           └── PaymentController.php   # Controlador tienda
│   ├── Models/
│   │   ├── Receipt.php              # Modelo de comprobantes
│   │   └── ReceiptProxy.php         # Proxy del modelo
│   ├── Payment/
│   │   └── YapePlin.php             # Clase principal del método de pago
│   ├── Providers/
│   │   ├── ModuleServiceProvider.php    # Registro Concord
│   │   └── YapePlinServiceProvider.php  # Service Provider principal
│   ├── Repositories/
│   │   └── ReceiptRepository.php    # Repositorio de comprobantes
│   ├── Resources/
│   │   ├── lang/
│   │   │   └── es/
│   │   │       └── app.php          # Traducciones en español
│   │   └── views/
│   │       ├── admin/
│   │       │   ├── layouts/
│   │       │   │   └── menu.blade.php
│   │       │   └── receipts/
│   │       │       ├── index.blade.php   # Listado admin
│   │       │       └── show.blade.php    # Detalle admin
│   │       └── checkout/
│   │           └── payment.blade.php     # Vista de pago
│   └── Routes/
│       ├── admin-routes.php         # Rutas admin
│       └── shop-routes.php          # Rutas tienda
└── README.md
```

## Base de Datos

### Tabla: yapeplin_receipts

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único del comprobante |
| order_id | int unsigned | ID del pedido relacionado |
| receipt_path | varchar(255) | Ruta del archivo del comprobante |
| original_filename | varchar(255) | Nombre original del archivo |
| status | enum | Estado: pending, approved, rejected |
| admin_notes | text | Notas del administrador |
| verified_at | timestamp | Fecha de verificación |
| verified_by | int unsigned | ID del admin que verificó |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de actualización |

## Personalización

### Modificar validaciones de archivo

Editar `packages/Webkul/YapePlin/src/Resources/views/checkout/payment.blade.php`:

```javascript
// Cambiar tamaño máximo (en bytes)
const maxSize = 10 * 1024 * 1024; // 10MB

// Cambiar tipos permitidos
const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
```

### Agregar notificaciones por email

Puedes agregar listeners para eventos de aprobación/rechazo en el `YapePlinServiceProvider`:

```php
Event::listen('yapeplin.receipt.approved', function($receipt) {
    // Enviar email de aprobación
});

Event::listen('yapeplin.receipt.rejected', function($receipt) {
    // Enviar email de rechazo
});
```

## Solución de Problemas

### El método de pago no aparece en el checkout

1. Verificar que esté activado en la configuración
2. Limpiar cachés: `php artisan optimize:clear`
3. Verificar que el `YapePlinServiceProvider` esté registrado

### Error al subir comprobantes

1. Verificar permisos de `storage/app/public/receipts/yapeplin`
2. Verificar que el enlace simbólico esté creado: `php artisan storage:link`
3. Revisar límites de upload en `php.ini`:
   - `upload_max_filesize = 10M`
   - `post_max_size = 10M`

### No se pueden ver los comprobantes en el admin

1. Verificar que las rutas estén cargadas
2. Verificar permisos del administrador
3. Limpiar caché de rutas: `php artisan route:clear`

## Licencia

MIT

## Autor

Creado para el proyecto Odessa E-commerce
