## Sobre el proyecto

Es un mini sistema que contiene usuarios, estos se clasifican en clientes y empleados.

Los clientes pueden realizar compras, donde cada compra está compuesta por productos.

### Puntos resaltantes del proyecto:

- Se agregó el paquete **jeremykenedy/laravel-roles** para manejar fácilmente los roles en el sistema

- Se agregó el paquete **tymon/jwt-auth** para generar un token cada vez que el usuario se autentica

- Se agregó el modelo Client, tiene relación 1 a 1 con Users. Cuando se crea un cliente, primero se crea un Usuario.

- Se agregó el modelo Employee, tiene relación 1 a 1 con Users. Cuando se crea un empleado, primero se crea un Usuario.

- Se agregó el modelo Product

- Se agregó el modelo Purchase

- Purchase tiene una relación de uno a muchos con Client, ya que un cliente puede realizar muchas compras, pero una compra pertenece a un cliente. Además, Purchase tiene relación muchos a muchos con productos, ya que un producto puede estar en muchas compras y una compra contiene muchos productos, esto se maneja a través de una tabla relación llamada product_purchase.

- Cuando se crea un Producto por medio del método **store**, se hace uso de Transacciones. Incluso, el guardar la imagen está dentro del bloque de transacción, si esta llegase a fallar, se hace un Rollback y por ende el registro no se verá reflejado en la BD, ni siquiera como un SoftDelete.

- Se agregó el paquete **gumlet/php-image-resize**, se utiliza cuando se carga un producto por medio del método store. Se conserva la imagen original y una copia de tamaño 300x300px

- Se importa productos por medio de un archivo de excel. Se utiliza el paquete **maatwebsite/excel** y un import creado

- Se exporta productos usando el paquete anterior

- Cuando se Exportan los productos, este dispara un evento llamado **ExportProductsEvent** y est ees manejado por el listener **ExportProductsListener**

- Cuando se Exportan los productos, se envía un email al administrador del sitio

- Gracias al paquete **barryvdh/laravel-dompdf** se genera un PDF a partir de los datos del producto
