USE master;
GO
CREATE DATABASE MULTIWYSZUKIWARKA
ON
PRIMARY(
    NAME = MULTIWYSZUKIWARKA_dat1,
    FILENAME = 'D:\Licencjat\Baza danych\wyszukiwarka1.mdf',
    SIZE = 100MB,
    MAXSIZE = 1000MB,
    FILEGROWTH = 10MB
)
LOG ON
(
    NAME = MULTIWYSZUKIWARKA_log1,
    FILENAME = 'D:\Licencjat\Baza danych\wyszukiwarkalog1.ldf',
    SIZE = 50MB,
    MAXSIZE = 100MB,
    FILEGROWTH = 10%
);
GO


