# Sistema de Gestión de Laboratorio

**Instituto Politécnico Nacional**
**Materia:** Sistemas de Informacion Web
**Integrantes del Equipo:**
* Rojas Cervantes Octavio
* Davila Maya Azarel Jahdai
* Trujano Rmaos Arturo
  
---

## 1. Planteamiento del Problema
Para los estudiantes, la gestión eficiente del tiempo es fundamental; la pérdida de unos pocos minutos puede afectar significativamente el desarrollo y conclusión de una práctica. Aunque la digitalización ha optimizado procesos en áreas como bibliotecas y otros centros de investigación, la Escuela Superior de Ingeniería Mecánica y Eléctrica (ESIME) Unidad Culhuacán aún requiere modernizar la administración de sus laboratorios.

 Implementar un sistema digital para el préstamo de componentes no solo reduciría los tiempos de espera de los alumnos, sino que también dotaría a los encargados de una herramienta precisa para el control de inventario, permitiéndoles conocer en tiempo real las unidades disponibles, el material faltante y el equipo que se encuentra fuera de servicio.

 ## 2.Modelos de Servicio Cloud
Para el despliegue del ecosistema web del Gestor de Laboratorio, se analizó la viabilidad de los tres modelos principales de computación en la nube (IaaS, PaaS y SaaS). El objetivo fue encontrar el entorno más eficiente para alojar nuestra capa de presentación (Frontend en PHP) y nuestra capa lógica (API Backend en C#), manteniendo la conexión hacia nuestra infraestructura física local (On-premise).
Tras la evaluación, se determinó que el modelo ideal para este proyecto es PaaS (Plataforma como Servicio):
La elección de PaaS (Platform as a Service): Al utilizar una plataforma como servicio, el proveedor de la nube administra toda la infraestructura subyacente. Esto nos permite desplegar directamente nuestro código de las vistas en PHP y los binarios de la API en C# en un entorno listo para ejecutar. El modelo PaaS nos brinda elasticidad automática en caso de que múltiples estudiantes soliciten componentes al mismo tiempo, garantizando alta disponibilidad sin requerir mantenimiento del servidor por parte de nuestro equipo.
El Frontend y la API operarán bajo un esquema PaaS en la nube pública, mientras que nuestra base de datos (LaboratorioDB) y el gestor de colas (IBM MQ) se mantendrán en un esquema On-premise seguro, consolidando la arquitectura híbrida planteada en el diseño de red.

Diagramas

<img width="905" height="603" alt="image" src="https://github.com/user-attachments/assets/db8af0f2-4faf-4c2c-ad2c-0af22f81049c" />
<img width="921" height="267" alt="image" src="https://github.com/user-attachments/assets/693ea7ae-4766-4290-9567-8f3ebe096e63" />
<img width="919" height="374" alt="image" src="https://github.com/user-attachments/assets/9cd2aef5-1e14-48d0-abac-b69c0ce91340" />
<img width="391" height="813" alt="image" src="https://github.com/user-attachments/assets/a603ecf0-73bb-43bd-973a-b9a692666928" />


## 2. Arquitectura del Proyecto (Despliegue)
<img width="883" height="263" alt="image" src="https://github.com/user-attachments/assets/66520c77-ec03-4694-8e6b-3e2fee08f83e" />




## 3. Base de Datos
**Gestor:** Microsoft SQL Server
* 📄 **[Clic aquí para ver](sistemadestionlab.sql)**

### 3.1. Reglas de Negocio y Máscaras de Datos
Para garantizar la seguridad y la integridad visual en la capa de presentación (Frontend) antes de que los datos interactúen con el servidor, se aplican las siguientes reglas de formato:

| Tabla | Campo | Máscara / Regla de Formato | Validación en Capa de Negocio |
| :--- | :--- | :--- | :--- |
| **Usuarios** | Password | `********` | El texto plano nunca se expone en la interfaz. Cifrado en la base de datos y enmascarado visualmente. |
| **Usuarios** | Correo | `*@alumno.ipn.mx` | Validación estricta de dominio. Solo se permite el registro con cuentas institucionales. |
| **Componentes**| Nombre | `Capitalización` | Formateo automático: la primera letra de cada palabra se convierte a mayúscula (ej. *Arduino Uno*). |

### 3.2. Diccionario de Datos y Mapeo Objeto-Relacional (ORM)
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

### 3.3. Implementación en la API (Modelo de Clase)
Para demostrar la integración de estas tablas en nuestra arquitectura, el siguiente fragmento muestra cómo la base de datos se transforma en un objeto manipulable dentro del ecosistema de C# (Mapeo ORM):

### 3.3. Implementación en la API (Modelo de Clase)
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

## 4. Arquitectura Avanzada: Manejo de Colas y Asincronía
Como parte de la investigación y escalabilidad del sistema **LaboratorioDB**, se propone una arquitectura orientada a eventos utilizando tecnología de colas de mensajes (como IBM MQ) para integrarse con bases de datos relacionales de grado industrial (como IBM DB2). Esto evita cuellos de botella cuando el sistema recibe peticiones masivas.

### 4.1. Estructura de Datos en Memoria (Modelo FIFO)
Para evitar el bloqueo de la base de datos, las solicitudes de préstamos no se insertan directamente, sino que pasan por un búfer intermedio.

<img width="948" height="303" alt="Imagen1" src="https://github.com/user-attachments/assets/c933302b-7471-4205-86b6-4c2b6bbf1e27" />


* **Enqueue (Productor):** La API Backend inserta los mensajes en formato JSON en el extremo inicial (Tail) de la estructura.
* **Dequeue (Consumidor):** Un servicio en segundo plano extrae los mensajes por el extremo final (Head) respetando estrictamente el orden de llegada (*First-In, First-Out*).

### 4.2. Flujo Asíncrono de Eventos (Secuencia UML)
El siguiente diagrama de secuencia UML 2.0 demuestra el desacoplamiento temporal de los procesos.

<img width="833" height="338" alt="Imagen2" src="https://github.com/user-attachments/assets/dc3e6b9a-4e12-4b45-b68e-810bc95344cd" />

1. **Desacoplamiento (Fire-and-forget):** Se utiliza notación de mensaje asíncrono (flecha de punta abierta) para demostrar que la API encola el mensaje y responde al usuario inmediatamente (HTTP 202), sin esperar a la base de datos.
2. **Servicio Background (Listener):** El servicio que ejecuta la transacción (INSERT) hacia la base de datos se mantiene inactivo hasta que la cola dispara una notificación o evento.

### 4.3. Topología de Infraestructura (Modelo Híbrido: Nube a On-premise)
Para garantizar tanto la accesibilidad fluida del sistema como la seguridad absoluta del inventario físico, el proyecto **LaboratorioDB** implementa una arquitectura de red híbrida:

* **Capa Pública (Nube):** El portal web de interacción (Frontend) se despliega en un entorno Cloud. Esto permite que los usuarios puedan consultar el catálogo de componentes y solicitar préstamos o mantenimientos desde cualquier dispositivo móvil o red externa.
* **Capa Privada y Segura (On-premise):** La base de datos relacional y el *Message Broker* (gestor de colas) residen físicamente en los servidores locales dentro de las instalaciones del laboratorio. 

**Justificación del Flujo de Comunicación:** 
Las peticiones viajan desde la nube hasta la red local (*Cloud to On-premise*) mediante la inyección asíncrona de mensajes. El *Listener* (nuestro servicio en C#), que opera de forma local, extrae los eventos de la cola y ejecuta las transacciones críticas. Este diseño aísla y protege la base de datos, evitando que esté expuesta directamente a internet, mitigando ataques directos y centralizando el procesamiento pesado en el hardware del laboratorio.

## 5. Documentación de la API (Endpoints RESTful)

Para la intercomunicación entre la base de datos SQL Server y las interfaces de usuario, se desarrolló una API RESTful nativa en PHP. Esta capa abstrae la lógica de acceso a datos y expone la información estrictamente en formato `application/json` con codificación UTF-8.

### 5.1. Arquitectura de Conexión
La API utiliza un módulo centralizado (`conexion.php`) que maneja la autenticación hacia **LaboratorioDB** mediante los controladores de `sqlsrv`. En caso de interrupción con el motor de base de datos, la API está configurada para interceptar el fallo y devolver un código de estado HTTP 500 (Internal Server Error) empaquetado en un JSON seguro, evitando exponer la traza del error en pantalla.

### 5.2. Catálogo de Endpoints (Lectura)
Los siguientes servicios exponen los datos transaccionales y de catálogo. Se implementaron sentencias `INNER JOIN` a nivel de base de datos para resolver las llaves foráneas y entregar al cliente los nombres legibles en lugar de identificadores abstractos.

| Endpoint | Método | Descripción |
| :--- | :---: | :--- |
| `/usuarios.php` | GET | Retorna el catálogo de usuarios. Por seguridad, excluye el hash de contraseñas. |
| `/componentes.php` | GET | Expone el inventario actual, incluyendo categorías y existencias. |
| `/prestamos.php` | GET | Lista el historial de préstamos, cruzando el `IdUsuario` para mostrar el nombre del solicitante. |
| `/devoluciones.php` | GET | Retorna el registro de devoluciones con sus respectivas observaciones y el nombre del usuario vinculado al préstamo original. |
| `/mantenimientos.php` | GET | Lista las bitácoras de reparación, cruzando el `IdComponente` para mostrar el nombre de la pieza afectada. |


Manual de Usuario y Funcionamiento del Sistema (GUI)
El Gestor de Laboratorio cuenta con una interfaz gráfica de usuario (GUI) limpia, minimalista y libre de distracciones, diseñada para que los alumnos y profesores puedan operar el sistema de manera intuitiva. A continuación, se documenta el flujo principal de funcionamiento mediante evidencias de la aplicación en ejecución.
Autenticación y Control de Acceso
El ciclo de uso comienza en el módulo de seguridad. El sistema presenta una pantalla de inicio de sesión donde el usuario debe ingresar sus credenciales institucionales (correo electrónico y contraseña)
<img width="1125" height="599" alt="image" src="https://github.com/user-attachments/assets/d8249f8d-8a1e-447c-a9da-43ba5ec96305" />
Una vez que la capa lógica valida las credenciales contra la base de datos, el sistema despliega una notificación de éxito ("Inicio de sesión correcto"), confirmando la identidad del usuario (por ejemplo, el Administrador) antes de redirigirlo al entorno de trabajo.  
<img width="1125" height="596" alt="image" src="https://github.com/user-attachments/assets/824bdf34-c57c-4baf-b77b-649c3ba4f3bc" />
Para el control de inventario, el módulo de Préstamos despliega una bitácora en tiempo real. Esta vista tabular permite a los encargados del laboratorio auditar rápidamente qué identificador de usuario solicitó material, la fecha de emisión del préstamo, la fecha límite de entrega y el estado actual de la transacción (Activo o Devuelto).
<img width="1125" height="603" alt="image" src="https://github.com/user-attachments/assets/dbfa4711-d945-4cc5-84ce-cd708ce48bfc" />
Esta es una vista de depuración (debug) para la fase de pruebas locales en el servidor; para el pase a producción, esa columna se oculta en el Frontend por políticas de seguridad
<img width="1125" height="648" alt="image" src="https://github.com/user-attachments/assets/1dbc51f2-c237-44dc-98ff-0df952d6d6db" />

