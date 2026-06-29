--CREAR BASE DE DATOS

IF DB_ID('LaboratorioDB') IS NULL
BEGIN
    CREATE DATABASE LaboratorioDB;
END
GO

USE LaboratorioDB;
GO


-- TABLA USUARIOS


IF OBJECT_ID('Usuarios', 'U') IS NULL
BEGIN
    CREATE TABLE Usuarios(
        IdUsuario INT IDENTITY(1,1) PRIMARY KEY,
        Nombre VARCHAR(100) NOT NULL,
        Correo VARCHAR(100) UNIQUE,
        Password VARCHAR(100),
        Rol VARCHAR(20)
    );
END
GO


-- TABLA COMPONENTES


IF OBJECT_ID('Componentes', 'U') IS NULL
BEGIN
    CREATE TABLE Componentes(
        IdComponente INT IDENTITY(1,1) PRIMARY KEY,
        Nombre VARCHAR(100) NOT NULL,
        Categoria VARCHAR(50),
        CantidadDisponible INT,
        Estado VARCHAR(20)
    );
END
GO


-- TABLA PRESTAMOS


IF OBJECT_ID('Prestamos', 'U') IS NULL
BEGIN
    CREATE TABLE Prestamos(
        IdPrestamo INT IDENTITY(1,1) PRIMARY KEY,
        IdUsuario INT,
        FechaPrestamo DATE,
        FechaLimite DATE,
        Estado VARCHAR(20),

        FOREIGN KEY (IdUsuario)
        REFERENCES Usuarios(IdUsuario)
    );
END
GO


-- TABLA DETALLEPRESTAMO


IF OBJECT_ID('DetallePrestamo', 'U') IS NULL
BEGIN
    CREATE TABLE DetallePrestamo(
        IdDetalle INT IDENTITY(1,1) PRIMARY KEY,
        IdPrestamo INT,
        IdComponente INT,
        Cantidad INT,

        FOREIGN KEY (IdPrestamo)
        REFERENCES Prestamos(IdPrestamo),

        FOREIGN KEY (IdComponente)
        REFERENCES Componentes(IdComponente)
    );
END
GO


-- TABLA DEVOLUCIONES


IF OBJECT_ID('Devoluciones', 'U') IS NULL
BEGIN
    CREATE TABLE Devoluciones(
        IdDevolucion INT IDENTITY(1,1) PRIMARY KEY,
        IdPrestamo INT,
        FechaDevolucion DATE,
        Observaciones VARCHAR(200),

        FOREIGN KEY (IdPrestamo)
        REFERENCES Prestamos(IdPrestamo)
    );
END
GO


-- TABLA MANTENIMIENTOS

IF OBJECT_ID('Mantenimientos', 'U') IS NULL
BEGIN
    CREATE TABLE Mantenimientos(
        IdMantenimiento INT IDENTITY(1,1) PRIMARY KEY,
        IdComponente INT,
        Fecha DATE,
        Descripcion VARCHAR(200),

        FOREIGN KEY (IdComponente)
        REFERENCES Componentes(IdComponente)
    );
END
GO



--DATOS DE PRUEBA-- 

INSERT INTO Usuarios (Nombre, Correo, Password, Rol)
VALUES
('Arturo Trujano','arturo@laboratorio.com','1234','Administrador'),
('Ana Martinez','ana@laboratorio.com','5678','Profesor'),
('Carlos Hernandez','carlos@laboratorio.com','4321','Estudiante'),
('Sofia Lopez','sofia@laboratorio.com','8765','Estudiante'),
('Miguel Ramirez','miguel@laboratorio.com','1111','Tecnico');
GO

SELECT * FROM Usuarios;


INSERT INTO Componentes (Nombre, Categoria, CantidadDisponible, Estado)
VALUES
('Arduino Uno R3','Microcontrolador',10,'Disponible'),
('ESP32 DevKit V1','Microcontrolador',8,'Disponible'),
('Sensor Ultrasonico HC-SR04','Sensor',15,'Disponible'),
('Multimetro Digital','Medicion',6,'Disponible'),
('Osciloscopio Digital','Equipo',2,'Disponible'),
('Fuente de Alimentacion 30V','Equipo',4,'Disponible'),
('Protoboard 830 Puntos','Accesorio',20,'Disponible'),
('Resistencia 220 Ohms','Componente',200,'Disponible'),
('Capacitor 100uF','Componente',120,'Disponible'),
('Cautin 60W','Herramienta',5,'Disponible');
GO

SELECT * FROM Componentes;


INSERT INTO Prestamos (IdUsuario, FechaPrestamo, FechaLimite, Estado)
VALUES
(3,'2026-06-26','2026-07-03','Activo'),
(4,'2026-06-25','2026-07-02','Activo'),
(2,'2026-06-24','2026-07-01','Devuelto'),
(5,'2026-06-23','2026-06-30','Activo');
GO

SELECT * FROM Prestamos;

INSERT INTO DetallePrestamo (IdPrestamo, IdComponente, Cantidad)
VALUES
(1,1,2),   -- Carlos pidió 2 Arduino Uno R3
(1,7,1),   -- Carlos pidió 1 Protoboard

(2,2,1),   -- Sofía pidió 1 ESP32
(2,3,2),   -- Sofía pidió 2 Sensores HC-SR04

(3,4,1),   -- Ana pidió 1 Multímetro

(4,6,1),   -- Miguel pidió 1 Fuente de Alimentación
(4,10,1);  -- Miguel pidió 1 Cautín
GO

SELECT * FROM DetallePrestamo;


INSERT INTO Mantenimientos (IdComponente, Fecha, Descripcion)
VALUES
(1, '2026-06-20', 'Cambio de cable USB y limpieza general'),
(3, '2026-06-18', 'Calibración del sensor ultrasónico'),
(6, '2026-06-15', 'Revisión de voltaje de salida'),
(9, '2026-06-12', 'Cambio de batería y prueba de funcionamiento');
GO

SELECT * FROM Mantenimientos;

INSERT INTO Devoluciones (IdPrestamo, FechaDevolucion, Observaciones)
VALUES
(1, '2026-06-24', 'Componentes entregados completos y en buen estado'),
(3, '2026-06-19', 'Se devolvió el componente sin observaciones'),
(4, '2026-06-17', 'Se realizó limpieza antes de la devolución');
GO

SELECT * FROM Devoluciones;
