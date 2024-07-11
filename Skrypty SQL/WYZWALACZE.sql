USE MULTIWYSZUKIWARKA
GO
CREATE OR ALTER TRIGGER trg_AktualizacjaSreniejOceny
ON tbl_wystawiona_ocena
AFTER INSERT, DELETE, UPDATE
AS
BEGIN
    DECLARE @IdObiektu INT;

    SELECT @IdObiektu = IdObiektu
    FROM inserted;

    UPDATE tbl_obiekt
    SET SredniaOcena = (
        SELECT AVG(Wartosc) 
        FROM tbl_ocena 
        WHERE IdObiektu = @IdObiektu
    )
    WHERE IdObiektu = @IdObiektu;
END;
