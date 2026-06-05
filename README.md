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
(sistemadestionlab.sql)
  
  **Tabla: Usuarios**
| Campo | Tipo de Dato | Llave / Regla | Descripción |
| :--- | :--- | :--- | :--- |
| **IdUsuario** | INT | **PK** (Identity) | Identificador único del usuario. |
| Nombre | VARCHAR(100) | NOT NULL | Nombre completo del usuario. |
| Correo | VARCHAR(100) | UNIQUE | Correo electrónico de acceso. |
| Password | VARCHAR(100) | NULL | Contraseña del sistema. |
| Rol | VARCHAR(20) | NULL | Administrador o Estudiante. |

**Tabla: Componentes**
| Campo | Tipo de Dato | Llave / Regla | Descripción |
| :--- | :--- | :--- | :--- |
| **IdComponente** | INT | **PK** (Identity) | Identificador del componente. |
| Nombre | VARCHAR(100) | NOT NULL | Nombre del equipo. |
| Categoria | VARCHAR(50) | NULL | Clasificación del material. |
| CantidadDisponible| INT | NULL | Unidades en existencia. |
| Estado | VARCHAR(20) | NULL | Condición física o disponibilidad. |

**Tabla: Prestamos**
| Campo | Tipo de Dato | Llave / Regla | Descripción |
| :--- | :--- | :--- | :--- |
| **IdPrestamo** | INT | **PK** (Identity) | Folio único del préstamo. |
| IdUsuario | INT | **FK** | Conecta con Usuarios(IdUsuario). |
| FechaPrestamo | DATE | NULL | Día de entrega del material. |
| FechaLimite | DATE | NULL | Día máximo para regresar. |
| Estado | VARCHAR(20) | NULL | Estatus actual del ticket. |

**Tabla: DetallePrestamo**
| Campo | Tipo de Dato | Llave / Regla | Descripción |
| :--- | :--- | :--- | :--- |
| **IdDetalle** | INT | **PK** (Identity) | Identificador de la partida. |
| IdPrestamo | INT | **FK** | Conecta con Prestamos. |
| IdComponente | INT | **FK** | Conecta con Componentes. |
| Cantidad | INT | NULL | Número de piezas prestadas. |

**Tabla: Devoluciones**
| Campo | Tipo de Dato | Llave / Regla | Descripción |
| :--- | :--- | :--- | :--- |
| **IdDevolucion** | INT | **PK** (Identity) | Folio de la devolución. |
| IdPrestamo | INT | **FK** | Conecta con Prestamos. |
| FechaDevolucion | DATE | NULL | Día de entrega física. |
| Observaciones | VARCHAR(200)| NULL | Notas adicionales de entrega. |

**Tabla: Mantenimientos**
| Campo | Tipo de Dato | Llave / Regla | Descripción |
| :--- | :--- | :--- | :--- |
| **IdMantenimiento**| INT | **PK** (Identity) | Folio del servicio técnico. |
| IdComponente | INT | **FK** | Conecta con Componentes. |
| Fecha | DATE | NULL | Día del diagnóstico. |
| Descripcion | VARCHAR(200)| NULL | Detalle de la reparación. |
