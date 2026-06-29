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


## 2. Arquitectura del Proyecto (Despliegue)
<img width="883" height="263" alt="image" src="https://github.com/user-attachments/assets/66520c77-ec03-4694-8e6b-3e2fee08f83e" />




## 3. Base de Datos
**Gestor:** Microsoft SQL Server
* 📄 **[Clic aquí para ver el script DDL y DML (con Rollback) de LaboratorioDB](sistemadestionlab.sql)**

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

