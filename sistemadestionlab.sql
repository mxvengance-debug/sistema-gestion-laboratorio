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


-- DATOS DE PRUEBA

INSERT INTO Usuarios (Nombre, Correo, Password, Rol)
SELECT 'Arturo','arturo@gmail.com','1234','Administrador'
WHERE NOT EXISTS (
    SELECT 1 FROM Usuarios
    WHERE Correo='arturo@gmail.com'
);

INSERT INTO Usuarios (Nombre, Correo, Password, Rol)
SELECT 'Juan Perez','juan@gmail.com','1234','Estudiante'
WHERE NOT EXISTS (
    SELECT 1 FROM Usuarios
    WHERE Correo='juan@gmail.com'
);

INSERT INTO Usuarios (Nombre, Correo, Password, Rol)
SELECT 'Maria Lopez','maria@gmail.com','4567','Estudiante'
WHERE NOT EXISTS (
    SELECT 1 FROM Usuarios
    WHERE Correo='maria@gmail.com'
);

INSERT INTO Componentes (Nombre,Categoria,CantidadDisponible,Estado)
SELECT 'Arduino Uno','Microcontrolador',10,'Disponible'
WHERE NOT EXISTS (
    SELECT 1 FROM Componentes
    WHERE Nombre='Arduino Uno'
);

INSERT INTO Componentes (Nombre,Categoria,CantidadDisponible,Estado)
SELECT 'Multimetro Digital','Medicion',5,'Disponible'
WHERE NOT EXISTS (
    SELECT 1 FROM Componentes
    WHERE Nombre='Multimetro Digital'
);

INSERT INTO Componentes (Nombre,Categoria,CantidadDisponible,Estado)
SELECT 'Sensor Ultrasonico HC-SR04','Sensor',15,'Disponible'
WHERE NOT EXISTS (
    SELECT 1 FROM Componentes
    WHERE Nombre='Sensor Ultrasonico HC-SR04'
);

INSERT INTO Componentes (Nombre,Categoria,CantidadDisponible,Estado)
SELECT 'Protoboard','Accesorio',20,'Disponible'
WHERE NOT EXISTS (
    SELECT 1 FROM Componentes
    WHERE Nombre='Protoboard'
);

GO

SELECT * FROM Componentes;

SELECT * FROM Usuarios;
