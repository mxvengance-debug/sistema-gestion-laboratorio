# Sistema de Gestión de Laboratorio

**Instituto Politécnico Nacional**
**Materia:** Sistemas de Informacion Web
**Integrantes del Equipo:**
* Rojas Cervantes Octavio
* Davila Maya Azarel Jahdai
* Trujano Rmaos Arturo
  

## Planteamiento del Problema
Para los estudiantes, la gestión eficiente del tiempo es fundamental; la pérdida de unos pocos minutos puede afectar significativamente el desarrollo y conclusión de una práctica. Aunque la digitalización ha optimizado procesos en áreas como bibliotecas y otros centros de investigación, la Escuela Superior de Ingeniería Mecánica y Eléctrica (ESIME) Unidad Culhuacán aún requiere modernizar la administración de sus laboratorios.

 Implementar un sistema digital para el préstamo de componentes no solo reduciría los tiempos de espera de los alumnos, sino que también dotaría a los encargados de una herramienta precisa para el control de inventario, permitiéndoles conocer en tiempo real las unidades disponibles, el material faltante y el equipo que se encuentra fuera de servicio.

 ## Modelos de Servicio Cloud
Para el despliegue del ecosistema web del Gestor de Laboratorio, se analizó la viabilidad de los tres modelos principales de computación en la nube (IaaS, PaaS y SaaS). El objetivo fue encontrar el entorno más eficiente para alojar nuestra capa de presentación (Frontend en PHP) y nuestra capa lógica (API Backend en C#), manteniendo la conexión hacia nuestra infraestructura física local (On-premise).
Tras la evaluación, se determinó que el modelo ideal para este proyecto es PaaS (Plataforma como Servicio):
La elección de PaaS (Platform as a Service): Al utilizar una plataforma como servicio, el proveedor de la nube administra toda la infraestructura subyacente. Esto nos permite desplegar directamente nuestro código de las vistas en PHP y los binarios de la API en C# en un entorno listo para ejecutar. El modelo PaaS nos brinda elasticidad automática en caso de que múltiples estudiantes soliciten componentes al mismo tiempo, garantizando alta disponibilidad sin requerir mantenimiento del servidor por parte de nuestro equipo.
El Frontend y la API operarán bajo un esquema PaaS en la nube pública, mientras que nuestra base de datos (LaboratorioDB) y el gestor de colas (IBM MQ) se mantendrán en un esquema On-premise seguro, consolidando la arquitectura híbrida planteada en el diseño de red.

## Diagramas

<img width="1491" height="1055" alt="WhatsApp Image 2026-07-11 at 5 40 10 PM" src="https://github.com/user-attachments/assets/3b5de226-f423-4e2d-b25f-2af26bc259fa" />
<img width="1024" height="297" alt="13597157-49d3-404f-b0a8-27448489861d" src="https://github.com/user-attachments/assets/d8021fd7-1d2f-4e3a-a6f2-7efee5b217a9" />
<img width="1024" height="883" alt="image" src="https://github.com/user-attachments/assets/06766973-7663-4517-b27f-e7e20c3aae6e" />
<img width="1024" height="1536" alt="WhatsApp Image 2026-07-08 at 1 52 25 PM" src="https://github.com/user-attachments/assets/95fded00-2dcc-4b04-a67d-128632ccd802" />
<img width="1293" height="814" alt="0e5dabbe-e011-462c-bd16-49e8bb9a7599" src="https://github.com/user-attachments/assets/453ba063-4bab-4c0b-bd62-324abf77387e" />


## Base de Datos
**Gestor:** Microsoft SQL Server
* 📄 **[Clic aquí para ver](sistemadestionlab.sql)**

### 3Reglas de Negocio y Máscaras de Datos
Para garantizar la seguridad y la integridad visual en la capa de presentación (Frontend) antes de que los datos interactúen con el servidor, se aplican las siguientes reglas de formato:

| Tabla | Campo | Máscara / Regla de Formato | Validación en Capa de Negocio |
| :--- | :--- | :--- | :--- |
| **Usuarios** | Password | `********` | El texto plano nunca se expone en la interfaz. Cifrado en la base de datos y enmascarado visualmente. |
| **Usuarios** | Correo | `*@alumno.ipn.mx` | Validación estricta de dominio. Solo se permite el registro con cuentas institucionales. |
| **Componentes**| Nombre | `Capitalización` | Formateo automático: la primera letra de cada palabra se convierte a mayúscula (ej. *Arduino Uno*). |

### Diccionario de Datos y Mapeo Objeto-Relacional (ORM)
A continuación se detalla la estructura física completa implementada en SQL Server y su equivalente lógico en las clases del código Backend en C#. Se han incorporado **Campos de Auditoría** para garantizar la trazabilidad de todos los movimientos dentro del laboratorio.

**Tabla: Usuarios**
| Campo SQL | Tipo SQL | Regla / Llave | Variable en C# | Tipo C# | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **IdUsuario** | INT | **PK** (Identity) | `IdUsuario` | `int` | Identificador único del usuario. |
| Nombre | VARCHAR(100) | NOT NULL | `NombreCompleto` | `string` | Nombre del estudiante o admin. |
| Correo | VARCHAR(100) | UNIQUE | `CorreoInstitucional` | `string` | Correo de acceso del IPN. |
| Password | VARCHAR(100) | NOT NULL | `PasswordHash` | `string` | Contraseña de autenticación. |
| Rol | VARCHAR(20) | NOT NULL | `RolAcceso` | `string` | Nivel de permisos (Administrador / Estudiante). |
| *FechaCreacion* | DATETIME | DEFAULT GETDATE() | `FechaRegistro` | `DateTime` | **Auditoría:** Momento exacto del alta en el sistema. |

**Tabla: Componentes**
| Campo SQL | Tipo SQL | Regla / Llave | Variable en C# | Tipo C# | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **IdComponente** | INT | **PK** (Identity) | `IdComponente` | `int` | Folio interno del material o equipo. |
| Nombre | VARCHAR(100) | NOT NULL | `NombreEquipo` | `string` | Descripción o nombre del componente. |
| Cantidad | INT | NOT NULL | `StockDisponible` | `int` | Unidades físicas disponibles en el laboratorio. |
| *FechaCreacion* | DATETIME | DEFAULT GETDATE() | `FechaAltaIngreso` | `DateTime` | **Auditoría:** Día en que se compró o registró el material. |
| *UsuarioModificador*| INT | **FK** | `UltimoEditorId` | `int` | **Auditoría:** ID del administrador que modificó el stock. |
| *FechaModificacion* | DATETIME | NULL | `UltimaActualizacion` | `DateTime` | **Auditoría:** Última vez que cambió el inventario. |

**Tabla: Prestamos**
| Campo SQL | Tipo SQL | Regla / Llave | Variable en C# | Tipo C# | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **IdPrestamo** | INT | **PK** (Identity) | `IdPrestamo` | `int` | Folio único del ticket de préstamo generado. |
| IdUsuario | INT | **FK** | `IdUsuarioSolicitante` | `int` | Conecta con Usuarios(IdUsuario). Indica quién pide. |
| FechaPrestamo | DATE | NOT NULL | `FechaSalida` | `DateTime` | Día en que se entregó físicamente el equipo. |
| Estado | VARCHAR(20) | NOT NULL | `EstatusTicket` | `string` | Estado actual: Activo, Atrasado, Concluido. |
| *FechaCreacion* | DATETIME | DEFAULT GETDATE() | `TimestampCreacion` | `DateTime` | **Auditoría:** Registro al segundo exacto de la petición. |
| *UsuarioCreador* | INT | **FK** | `AdminAutorizador` | `int` | **Auditoría:** Administrador que aprobó la salida. |

**Tabla: DetallePrestamo**
| Campo SQL | Tipo SQL | Regla / Llave | Variable en C# | Tipo C# | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **IdDetalle** | INT | **PK** (Identity) | `IdDetalle` | `int` | Identificador único de la partida o renglón del ticket. |
| IdPrestamo | INT | **FK** | `IdPrestamoId` | `int` | Relación con el folio del encabezado del préstamo. |
| IdComponente | INT | **FK** | `IdComponenteId` | `int` | Relación con el componente específico solicitado. |
| Cantidad | INT | NOT NULL | `CantidadPrestad` | `int` | Número de piezas solicitadas de este componente. |

**Tabla: Devoluciones**
| Campo SQL | Tipo SQL | Regla / Llave | Variable en C# | Tipo C# | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **IdDevolucion** | INT | **PK** (Identity) | `IdDevolucion` | `int` | Folio único de la recepción del material. |
| IdPrestamo | INT | **FK** | `IdPrestamoAsociado`| `int` | Vínculo con el préstamo original que se está cerrando. |
| FechaDevolucion| DATE | NOT NULL | `FechaEntregaFisica`| `DateTime` | Día en que el alumno regresó los componentes. |
| Observaciones | VARCHAR(200) | NULL | `NotasDevolucion` | `string` | Comentarios sobre el estado físico del material devuelto. |
| *FechaCreacion* | DATETIME | DEFAULT GETDATE() | `TimestampRegistro` | `DateTime` | **Auditoría:** Registro en sistema de la transacción. |
| *UsuarioReceptor*| INT | **FK** | `AdminRecibioId` | `int` | **Auditoría:** ID del administrador que validó el retorno. |

**Tabla: Mantenimientos**
| Campo SQL | Tipo SQL | Regla / Llave | Variable en C# | Tipo C# | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **IdMantenimiento**| INT | **PK** (Identity) | `IdMantenimiento` | `int` | Folio único del reporte de servicio técnico. |
| IdComponente | INT | **FK** | `IdComponenteDañado`| `int` | Vínculo con el material que entró a reparación/diagnóstico. |
| Fecha | DATE | NOT NULL | `FechaDiagnostico` | `DateTime` | Día en que se programó o realizó la revisión técnica. |
| Descripcion | VARCHAR(200) | NOT NULL | `DetalleReparacion` | `string` | Explicación de la falla o del mantenimiento preventivo. |
| *FechaCreacion* | DATETIME | DEFAULT GETDATE() | `TimestampAlta` | `DateTime` | **Auditoría:** Fecha en la que se levantó el reporte. |
| *UsuarioRegistra*| INT | **FK** | `TecnicoResponsable`| `int` | **Auditoría:** ID del administrador que documentó la falla. |


### Implementación en la API (Modelo de Clase)
Para demostrar la integración de estas tablas en nuestra arquitectura, el siguiente fragmento muestra cómo la base de datos se transforma en un objeto manipulable dentro del ecosistema de C# (Mapeo ORM):

```csharp
// Modelo representativo de la tabla Prestamos en el Backend
public class PrestamoModel 
{
    public int IdPrestamo { get; set; }
    public int IdUsuarioSolicitante { get; set; }
    public DateTime FechaSalida { get; set; }
    public string EstatusTicket { get; set; }
    
    // Control de Auditoría
    public DateTime TimestampCreacion { get; set; }
    public int AdminAutorizador { get; set; }
}
```

## Arquitectura Avanzada: Manejo de Colas y Asincronía
Como parte de la investigación y escalabilidad del sistema **LaboratorioDB**, se propone una arquitectura orientada a eventos utilizando tecnología de colas de mensajes (como IBM MQ) para integrarse con bases de datos relacionales de grado industrial (como IBM DB2). Esto evita cuellos de botella cuando el sistema recibe peticiones masivas.

### Estructura de Datos en Memoria (Modelo FIFO)
Para evitar el bloqueo de la base de datos, las solicitudes de préstamos no se insertan directamente, sino que pasan por un búfer intermedio.

<img width="1024" height="297" alt="13597157-49d3-404f-b0a8-27448489861d" src="https://github.com/user-attachments/assets/7f709766-e97e-4202-b415-e85b058663dd" />


* **Enqueue (Productor):** La API Backend inserta los mensajes en formato JSON en el extremo inicial (Tail) de la estructura.
* **Dequeue (Consumidor):** Un servicio en segundo plano extrae los mensajes por el extremo final (Head) respetando estrictamente el orden de llegada (*First-In, First-Out*).

### Flujo Asíncrono de Eventos (Secuencia UML)
El siguiente diagrama de secuencia UML 2.0 demuestra el desacoplamiento temporal de los procesos.

<img width="1024" height="883" alt="image" src="https://github.com/user-attachments/assets/bf4473b8-9076-4a7e-b0c1-e4010439a4a1" />

1. **Desacoplamiento (Fire-and-forget):** Se utiliza notación de mensaje asíncrono (flecha de punta abierta) para demostrar que la API encola el mensaje y responde al usuario inmediatamente (HTTP 202), sin esperar a la base de datos.
2. **Servicio Background (Listener):** El servicio que ejecuta la transacción (INSERT) hacia la base de datos se mantiene inactivo hasta que la cola dispara una notificación o evento.

### Topología de Infraestructura (Modelo Híbrido: Nube a On-premise)
Para garantizar tanto la accesibilidad fluida del sistema como la seguridad absoluta del inventario físico, el proyecto **LaboratorioDB** implementa una arquitectura de red híbrida:

* **Capa Pública (Nube):** El portal web de interacción (Frontend) se despliega en un entorno Cloud. Esto permite que los usuarios puedan consultar el catálogo de componentes y solicitar préstamos o mantenimientos desde cualquier dispositivo móvil o red externa.
* **Capa Privada y Segura (On-premise):** La base de datos relacional y el *Message Broker* (gestor de colas) residen físicamente en los servidores locales dentro de las instalaciones del laboratorio. 

**Justificación del Flujo de Comunicación:** 
Las peticiones viajan desde la nube hasta la red local (*Cloud to On-premise*) mediante la inyección asíncrona de mensajes. El *Listener* (nuestro servicio en C#), que opera de forma local, extrae los eventos de la cola y ejecuta las transacciones críticas. Este diseño aísla y protege la base de datos, evitando que esté expuesta directamente a internet, mitigando ataques directos y centralizando el procesamiento pesado en el hardware del laboratorio.

## 📚 Documentación de la API (Swagger / OpenAPI 3.0)

El backend de este proyecto expone una API RESTful nativa desarrollada en PHP para la consulta de información de **LaboratorioDB**. La documentación oficial, las rutas, modelos y códigos de estado están estandarizados mediante la especificación OpenAPI 3.0.3.

### Servidor de Desarrollo (Local)
Para ejecutar y probar los endpoints en tu entorno local (ej. mediante XAMPP en Windows 11), el servidor base está configurado en la siguiente URL:
`http://localhost/LaboratorioWeb`

### Catálogo de Endpoints

| Método | Endpoint | Descripción | Parámetros | Respuestas |
| :---: | :--- | :--- | :---: | :--- |
| **GET** | `/api/usuarios.php` | Obtiene el catálogo de usuarios registrados. | Ninguno | `200 OK` (Array JSON)<br>`500 Internal Error` |
| **GET** | `/api/componentes.php` | Devuelve el inventario de materiales y su estado. | Ninguno | `200 OK` (Array JSON)<br>`500 Internal Error` |
| **GET** | `/api/prestamos.php` | Lista el historial de préstamos vinculados a usuarios. | Ninguno | `200 OK` (Array JSON)<br>`500 Internal Error` |
| **GET** | `/api/devoluciones.php` | Lista devoluciones y observaciones del material. | Ninguno | `200 OK` (Array JSON)<br>`500 Internal Error` |
| **GET** | `/api/mantenimientos.php`| Muestra la bitácora de componentes en revisión. | Ninguno | `200 OK` (Array JSON)<br>`500 Internal Error` |

### Estructura de Datos (Schemas)

Todas las respuestas exitosas devuelven un arreglo de objetos en formato `application/json`. A continuación se muestra la estructura y los tipos de datos expuestos para los principales módulos de la aplicación:

**Objeto Usuario**
```json
{
  "IdUsuario": 1,
  "Nombre": "Arturo Trujano",
  "Correo": "arturo@laboratorio.com",
  "Rol": "Administrador"
}
```
Objeto Componente (Inventario)
```json
{
  "IdComponente": 1,
  "Nombre": "Arduino Uno R3",
  "Categoria": "Microcontrolador",
  "CantidadDisponible": 10,
  "Estado": "Disponible"
}
```
Objeto Préstamo (Transacción)
```json
{
  "IdPrestamo": 1,
  "Usuario": "Carlos Hernandez",
  "FechaPrestamo": "2026-06-26",
  "FechaLimite": "2026-07-03",
  "Estado": "Activo"
}
```

## Documentación de la API (Endpoints RESTful)

Para la intercomunicación entre la base de datos SQL Server y las interfaces de usuario, se desarrolló una API RESTful nativa en PHP. Esta capa abstrae la lógica de acceso a datos y expone la información estrictamente en formato `application/json` con codificación UTF-8.

### Arquitectura de Conexión
La API utiliza un módulo centralizado (`conexion.php`) que maneja la autenticación hacia **LaboratorioDB** mediante los controladores de `sqlsrv`. En caso de interrupción con el motor de base de datos, la API está configurada para interceptar el fallo y devolver un código de estado HTTP 500 (Internal Server Error) empaquetado en un JSON seguro, evitando exponer la traza del error en pantalla.

### Catálogo de Endpoints (Lectura)
Los siguientes servicios exponen los datos transaccionales y de catálogo. Se implementaron sentencias `INNER JOIN` a nivel de base de datos para resolver las llaves foráneas y entregar al cliente los nombres legibles en lugar de identificadores abstractos.

| Endpoint | Método | Descripción |
| :--- | :---: | :--- |
| `/usuarios.php` | GET | Retorna el catálogo de usuarios. Por seguridad, excluye el hash de contraseñas. |
| `/componentes.php` | GET | Expone el inventario actual, incluyendo categorías y existencias. |
| `/prestamos.php` | GET | Lista el historial de préstamos, cruzando el `IdUsuario` para mostrar el nombre del solicitante. |
| `/devoluciones.php` | GET | Retorna el registro de devoluciones con sus respectivas observaciones y el nombre del usuario vinculado al préstamo original. |
| `/mantenimientos.php` | GET | Lista las bitácoras de reparación, cruzando el `IdComponente` para mostrar el nombre de la pieza afectada. |


## Manual de Usuario y Funcionamiento del Sistema (GUI)

Este documento detalla el uso y navegación del Sistema de Gestión de Laboratorio (desarrollado para la ESIME Culhuacán). El sistema permite administrar usuarios, el inventario de componentes electrónicos, y llevar un control detallado de préstamos, devoluciones y mantenimientos.

1. Acceso al Sistema (Inicio de Sesión)

<img width="1600" height="842" alt="WhatsApp Image 2026-07-12 at 9 19 44 PM" src="https://github.com/user-attachments/assets/da1a92aa-8061-42f6-b6fa-c3328d629064" />


Para ingresar a la plataforma, debes autenticarte en la pantalla de inicio:

Correo electrónico: Ingresa tu dirección de correo registrada (por ejemplo, ejemplo@laboratorio.com).

Contraseña: Introduce tu clave de acceso.

Haz clic en el botón azul "Iniciar sesión".

Nota: Si los datos son correctos, el sistema mostrará una pantalla de confirmación con el mensaje "Bienvenido [Tu Nombre]" y te redirigirá automáticamente al panel principal.

<img width="1600" height="838" alt="WhatsApp Image 2026-07-12 at 9 20 02 PM" src="https://github.com/user-attachments/assets/55d1d12b-501d-4cdb-aa5b-d84ab236b2cc" />

2. Panel Principal (Menú de Navegación)

<img width="1600" height="764" alt="WhatsApp Image 2026-07-12 at 9 22 53 PM" src="https://github.com/user-attachments/assets/6e0bbaad-95b0-4aaa-b647-26e41dd733da" />

Una vez dentro, el sistema confirmará la conexión a la base de datos (mostrando el mensaje "Conexión exitosa con SQL Server") y presentará el menú principal. Desde aquí, puedes acceder a los cinco módulos fundamentales del sistema haciendo clic en sus respectivos botones azules:

Usuarios

Componentes

Préstamos

Devoluciones

Mantenimientos

3. Módulos del Sistema
Cada botón del panel principal te llevará a una tabla detallada con los registros correspondientes. En todas las vistas, encontrarás un enlace en la parte inferior izquierda que dice "← Volver al inicio" para regresar rápidamente al menú principal.

3.1. Lista de Usuarios

<img width="1600" height="782" alt="WhatsApp Image 2026-07-12 at 9 20 30 PM" src="https://github.com/user-attachments/assets/adce5dde-071e-45e1-8f17-b35a88ea9029" />

Este módulo muestra el personal y los estudiantes registrados en la base de datos.

Campos visibles:

ID: Número de identificación único.

Nombre: Nombre completo del usuario.

Correo: Dirección de contacto.

Password: Contraseña (se muestra encriptada por razones de seguridad).

Rol: Nivel de acceso en el sistema (Administrador, Profesor, Estudiante, Técnico).

3.2. Lista de Componentes

<img width="1600" height="789" alt="WhatsApp Image 2026-07-12 at 9 21 51 PM" src="https://github.com/user-attachments/assets/5197825b-9add-4bf6-bbfb-fa798972b6db" />

Aquí puedes consultar el inventario de piezas, herramientas y equipos disponibles en el laboratorio (como Arduinos, multímetros, resistencias, etc.).

Campos visibles:

ID: Identificador del componente.

Nombre: Descripción del artículo (ej. Arduino Uno R3, Osciloscopio Digital).

Categoría: Clasificación del artículo (Microcontrolador, Sensor, Equipo, Componente, etc.).

Cantidad: Unidades existentes en el inventario.

Estado: Disponibilidad actual (ej. Disponible).

3.3. Lista de Préstamos

<img width="1600" height="791" alt="WhatsApp Image 2026-07-12 at 9 20 17 PM" src="https://github.com/user-attachments/assets/26398f1a-bcde-4475-8fc2-b97cdb0c843f" />

Módulo diseñado para auditar el historial de materiales solicitados por los usuarios.

Campos visibles:

ID: Número de folio del préstamo.

ID Usuario: Número que vincula el préstamo con la persona que lo solicitó.

Fecha Préstamo: Día en que se entregó el material.

Fecha Límite: Día máximo para retornar el material.

Estado: Estatus actual del trámite (ej. Activo o Devuelto).

3.4. Lista de Devoluciones

<img width="1600" height="788" alt="WhatsApp Image 2026-07-12 at 9 22 00 PM" src="https://github.com/user-attachments/assets/3dafda31-3809-4e97-b326-afa4e0dc62e4" />

Control de las entregas de material que previamente estaba en calidad de préstamo.

Campos visibles:

ID Devolución: Folio único del retorno.

ID Préstamo: Folio del préstamo original asociado.

Fecha de Devolución: Día exacto en que se regresó el artículo.

Observaciones: Notas sobre las condiciones en las que se entregó el material (ej. Entregados completos y en buen estado, Se realizó limpieza).

3.5. Lista de Mantenimientos

<img width="1600" height="785" alt="WhatsApp Image 2026-07-12 at 9 22 06 PM" src="https://github.com/user-attachments/assets/2dd61506-f33b-4f40-9485-e4e239574de7" />

Registro del historial de reparaciones, calibraciones o revisiones del equipo del laboratorio.

Campos visibles:

ID: Identificador del registro de mantenimiento.

ID Componente: Número de la pieza o equipo que recibió el servicio.

Fecha: Día en que se realizó la intervención.

Descripción: Detalle técnico del trabajo realizado (ej. Cambio de cable USB, Calibración del sensor, Revisión de voltaje).

