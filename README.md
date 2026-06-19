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
* 📄 **[Clic aquí para ver el script DDL y DML (con Rollback) de LaboratorioDB](pon-aqui-el-nombre-de-tu-archivo.sql)**

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
