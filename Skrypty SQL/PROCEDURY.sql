USE MULTIWYSZUKIWARKA
GO
CREATE OR ALTER PROCEDURE up_DodajZgloszenie
	@Tresc VARCHAR(500),
	@DataZgloszenia DATE,
	@E_mail VARCHAR(50) OUTPUT,
	@output_message NVARCHAR(255) OUTPUT	
AS
BEGIN 
	SET NOCOUNT ON;
	
	INSERT INTO tbl_zgloszenie(Tresc, DataZgloszenia,E_mail)
	VALUES (@Tresc, @DataZgloszenia,@E_mail);

	SET @output_message = @E_mail + ' dziêkuje za dodanie zgloszenia';
END;
GO
CREATE OR ALTER PROCEDURE up_SzczegolObiektu
    @IdObiektu INT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @DzienTygodnia INT = DATEPART(WEEKDAY, GETDATE()); 

    SELECT 
        Z.NazwaZdjecia,
        O.NazwaObiektu AS 'Nazwa obiektu', 
        O.NazwUlicy AS 'Nazwa ulicy',
        O.NumerUlicy AS 'Numer ulicy', 
        O.Opis AS 'Opis',
        O.SredniaOcena AS 'Œrednia ocena',
        O.Telefon AS 'Telefon', 
        O.E_mail AS 'E-mail',
        CASE WHEN SB.DlaDzieci = 1 THEN 'Tak' ELSE 'Nie' END AS 'Dostosowane do dzieci',
        CASE WHEN SB.Ogrodek = 1 THEN 'Tak' ELSE 'Nie' END AS 'Czy jest ogórdek?',
        CASE WHEN SB.StrefaPalacza = 1 THEN 'Tak' ELSE 'Nie' END AS 'Czy jest strefa palacza?',
        CASE WHEN SB.WpuszczanieZwierzat = 1 THEN 'Tak' ELSE 'Nie' END AS 'Czy wpuszczaj¹ zwierzêta?',
        CASE WHEN SB.DlaNiepelnosprawnych = 1 THEN 'Tak' ELSE 'Nie' END AS 'Czy dostosowane do niepe³nosprawnych?',
        CASE WHEN SB.StrefaDzieci = 1 THEN 'Tak' ELSE 'Nie' END AS 'Strefa dla dzieci?',
        GOS.Wartosc AS 'Godzina otwarcia',
        GZ.Wartosc AS 'Godzina zamkniêcia',
        SB.IdDarmowegoWejscia AS 'Darmowe wejœcie'
    FROM 
        tbl_obiekt AS O
        LEFT JOIN tbl_szczegoly_obiektu AS SB ON O.IdObiektu = SB.IdObiektu
        LEFT JOIN tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
        LEFT JOIN tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
        LEFT JOIN tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
        LEFT JOIN tbl_godzina AS GOS ON PH.IdGodzinyOtwarcia = GOS.IdGodziny
        LEFT JOIN tbl_godzina AS GZ ON PH.IdGodzinyZamkniecia = GZ.IdGodziny
        LEFT JOIN tbl_dzien_tygodnia AS ZMW ON SB.IdDarmowegoWejscia = ZMW.IdDniaTygodnia
    WHERE 
        O.IdObiektu = @IdObiektu
        AND 
		CAST(H.DataWarznosci AS DATE) >= CAST(GETDATE() AS DATE)AND 
		PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE());
END;
GO
CREATE OR ALTER PROCEDURE up_DodajUzytkownika
	@login VARCHAR(15),
	@haslo VARCHAR(20),
	@imie VARCHAR(25) OUTPUT,
	@nazwisko VARCHAR(25) OUTPUT,
	@e_mail VARCHAR(50),
	@telefon CHAR(15),
	@data_urodzenia DATE,
	@output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
	SET NOCOUNT ON;
	IF NOT EXISTS (SELECT 1 FROM tbl_typ_konta WHERE IdTypKonta = 3)
	BEGIN
		PRINT('Nie mo¿na dodaæ nowego konta. Brak odpowiedniego typu konta');
		RETURN;
	END;
	INSERT INTO tbl_uzytkownik (Imie,Nazwisko,E_mail,Telefon,DataUrodzenia)
	VALUES (@imie,@nazwisko,@e_mail,@telefon,@data_urodzenia);
	SET @output_message = 'Dodano nowego u¿ytkownika ' + 'Imiê: ' + @imie 
		+ ' Nazwisko:' + @nazwisko;
	DECLARE @IdUzytkownika INT;
    SELECT @IdUzytkownika = SCOPE_IDENTITY();
	INSERT INTO tbl_konto(Login,Haslo,IdTypKonta,IdUzytkownika) 
		VALUES (@login, @haslo,3,@IdUzytkownika);
	IF @@ROWCOUNT = 0
	BEGIN 
		PRINT ('Nie uda³o siê dodaæ nowego konta.');
		RETURN;
	END;
	
END;
GO
CREATE OR ALTER PROCEDURE up_ZmianaHasla
    @e_mail VARCHAR(50),
    @NoweHaslo VARCHAR(20),
    @output_message NVARCHAR(225) OUTPUT
AS
BEGIN 
    SET NOCOUNT ON;

    DECLARE @Id_Uzytkownika INT = (SELECT IdUzytkownik FROM tbl_uzytkownik WHERE E_mail = @e_mail);
    
    IF @Id_Uzytkownika IS NULL
    BEGIN
        SET @output_message = 'Nie znaleziono u¿ytkownika o podanym e-mailu.';
        RETURN;
    END

    UPDATE tbl_konto 
    SET Haslo = @NoweHaslo
    WHERE IdUzytkownika = @Id_Uzytkownika;

    IF @@ROWCOUNT > 0
    BEGIN
        SET @output_message = 'Zmieniono has³o.';
    END
    ELSE
    BEGIN
        SET @output_message = 'Nie uda³o siê zmieniæ has³a.';
    END
END;
GO
CREATE OR ALTER PROCEDURE up_AktualizujUzytkownika
	@login VARCHAR(15) = NULL,
	@imie VARCHAR(25) = NULL,
	@nazwisko VARCHAR(25) = NULL,
	@e_mail VARCHAR(50) = NULL,
	@telefon CHAR(15) = NULL,
	@data_urodzenia DATE = NULL,
	@IdUzytkownik INT,
	@output_message NVARCHAR(255) OUTPUT
AS
BEGIN
	SET NOCOUNT ON;
	IF NOT EXISTS (SELECT 1 FROM tbl_uzytkownik WHERE IdUzytkownik = @IdUzytkownik)
	BEGIN 
		SET @output_message = 'Nie odnaleziono u¿ytkownika';
		RETURN;
	END;

	IF @login IS NOT NULL
	BEGIN
		UPDATE tbl_konto
		SET Login = @login
		WHERE IdUzytkownika = @IdUzytkownik;
	END;
	UPDATE tbl_uzytkownik
	SET
		Imie = ISNULL(@imie, Imie),
		Nazwisko = ISNULL(@nazwisko, Nazwisko),
		E_mail = ISNULL(@e_mail, E_mail),
		Telefon = ISNULL(@telefon, Telefon),
		DataUrodzenia = ISNULL(@data_urodzenia, DataUrodzenia)
	WHERE IdUzytkownik = @IdUzytkownik;
	SET @output_message = 'Zaktualizowano dane u¿ytkownika ' + CONVERT(NVARCHAR(10), @IdUzytkownik);
END;
GO
CREATE OR ALTER PROCEDURE up_UsunUzytkownika
    @IdUzytkownika INT,
    @output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
    SET NOCOUNT ON;
    IF NOT EXISTS (SELECT 1 FROM tbl_uzytkownik WHERE IdUzytkownik = @IdUzytkownika)
    BEGIN 
        SET @output_message = 'Nie odnaleziono u¿ytkownika';
        RETURN;
    END;

    DELETE FROM tbl_opinia
    WHERE IdUzytkownika = @IdUzytkownika;

    DELETE FROM tbl_wystawiona_ocena
    WHERE IdUzytkownika = @IdUzytkownika;

    DELETE FROM tbl_ulubione
    WHERE IdUzytkownika = @IdUzytkownika;

    DELETE FROM tbl_konto
    WHERE IdUzytkownika = @IdUzytkownika;

    DELETE FROM tbl_uzytkownik WHERE IdUzytkownik = @IdUzytkownika;

    SET @output_message = 'Usuniêto u¿ytkownika ' + CONVERT(NVARCHAR(10), @IdUzytkownika) + ' wraz z jego list¹ ulubionych oraz opini¹ i wystawion¹ ocen¹';
END;
GO
CREATE OR ALTER PROCEDURE up_UsunOrganizatora
    @IdOrganizatora INT,
    @output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_organizator WHERE IdOrganizatora = @IdOrganizatora)
    BEGIN
        SET @output_message = 'Nie odnaleziono organizatora';
        RETURN;
    END;

    IF EXISTS (SELECT 1 FROM tbl_wydarzenie WHERE IdOrganizatora = @IdOrganizatora)
    BEGIN
        SET @output_message = 'Nie mo¿na usun¹æ organizatora, poniewa¿ jest powi¹zany z wydarzeniem';
        RETURN;
    END;

    DELETE FROM tbl_organizator
    WHERE IdOrganizatora = @IdOrganizatora;

    SET @output_message = 'Usuniêto organizatora';
END;
GO
CREATE OR ALTER PROCEDURE up_DodajOrganizatora
	@Nazwa VARCHAR(100),
	@Nip VARCHAR(13),
	@Telefon CHAR(15),
	@E_mail VARCHAR(50),
	@Imie VARCHAR(25),
	@Nazwisko VARCHAR(25),
	@Stanowisko VARCHAR(35),
	@output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
	SET NOCOUNT ON;
	INSERT INTO tbl_osoba(Imie,Nazwisko,Stanowisko) VALUES
		(@Imie,@Nazwisko,@Stanowisko)
	DECLARE @IdOsoby INT;
	SELECT @IdOsoby = MAX(IdOsoba) FROM tbl_osoba;
	
	INSERT INTO tbl_organizator(Nazwa, Nip, IdOsoby, Telefon, E_mail)
	VALUES (@Nazwa, @Nip, @IdOsoby, @Telefon, @E_mail);

	SET @output_message = 'Dodano nowego organizatora';
END;
GO
CREATE OR ALTER PROCEDURE up_UsunOpinie
	@IdOpinii INT,
	@output_message NVARCHAR(255) OUTPUT	
AS
BEGIN 
	SET NOCOUNT ON;

	IF NOT EXISTS (SELECT 1 FROM tbl_opinia WHERE IdOpini = @IdOpinii)
	BEGIN
		SET @output_message = 'Nie odnaleziono opinii';
		RETURN;
	END;
	DELETE FROM tbl_opinia
	WHERE IdOpini = @IdOpinii;

	SET @output_message = 'Usuniêto opiniê';
END;
GO
CREATE OR ALTER PROCEDURE up_UsunOcene
    @IdWystawionejOceny INT,
    @output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_wystawiona_ocena WHERE IdWystawionejOceny = @IdWystawionejOceny)
    BEGIN
        SET @output_message = 'Nie odnaleziono oceny';
        RETURN;
    END;
    DELETE FROM tbl_wystawiona_ocena
    WHERE IdWystawionejOceny = @IdWystawionejOceny;

    SET @output_message = 'Usuniêto ocenê';
END;
GO
CREATE OR ALTER PROCEDURE up_UsunZgloszenia
	@IdZgloszenia INT OUTPUT, 
	@output_message NVARCHAR(255) OUTPUT	
AS
BEGIN 
	SET NOCOUNT ON;

	IF NOT EXISTS (SELECT 1 FROM tbl_zgloszenie WHERE IdZgloszenia = @IdZgloszenia)
	BEGIN
		SET @output_message = 'Nie odnaleziono zg³oszenia';
		RETURN;
	END;

	DELETE FROM tbl_zgloszenie
	WHERE IdZgloszenia = @IdZgloszenia;

	SET @output_message = 'Usuniêto zg³oszenie';
END;
GO
CREATE OR ALTER PROCEDURE up_UsunWydarzenie
    @IdWydarzenia INT,
    @output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_wydarzenie WHERE IdWydarzenia = @IdWydarzenia)
    BEGIN
        SET @output_message = 'Nie odnaleziono wydarzenia';
        RETURN;
    END;

    BEGIN TRY
        
        BEGIN TRANSACTION;

        DELETE FROM tbl_wydarzenie WHERE IdWydarzenia = @IdWydarzenia;

        DELETE FROM tbl_organizator WHERE IdOrganizatora IN (SELECT IdOrganizatora FROM tbl_wydarzenie WHERE IdWydarzenia = @IdWydarzenia);
        
        COMMIT TRANSACTION;

        SET @output_message = 'Usuniêto wydarzenie i powi¹zane rekordy';
    END TRY
    BEGIN CATCH
        
        ROLLBACK TRANSACTION;
        SET @output_message = 'Wyst¹pi³ b³¹d podczas usuwania wydarzenia';
    END CATCH;
END;
GO
CREATE OR ALTER PROCEDURE up_DodajWydarzenie
    @DataWydarzenia DATE,
    @IdTypWydarzenia TINYINT,
    @NazwaWydarzenia VARCHAR(50),
    @IdOrganizatora INT,
    @IdObiektu INT,
    @Informacje VARCHAR(300),
    @output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
    SET NOCOUNT ON;

    INSERT INTO tbl_wydarzenie (DataWydarzenia, IdTypWydarzenia, NazwaWydarzenia, IdOrganizatora, IdObiektu, Informacje)
    VALUES (@DataWydarzenia, @IdTypWydarzenia, @NazwaWydarzenia, @IdOrganizatora, @IdObiektu, @Informacje);

    IF @@ERROR <> 0
    BEGIN
        SET @output_message = 'Wyst¹pi³ b³¹d podczas dodawania wydarzenia';
        RETURN;
    END

    SET @output_message = 'Dodano wydarzenie';
END;
GO
CREATE OR ALTER PROCEDURE up_DodajPracownika
	@login VARCHAR(15),
	@haslo VARCHAR(20),
	@imie VARCHAR(25) OUTPUT,
	@nazwisko VARCHAR(25) OUTPUT,
	@e_mail VARCHAR(50),
	@telefon CHAR(15),
	@data_urodzenia DATE,
	@output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
	SET NOCOUNT ON;
	IF NOT EXISTS (SELECT 1 FROM tbl_typ_konta WHERE IdTypKonta = 2)
	BEGIN
		PRINT('Nie mo¿na dodaæ nowego konta. Brak odpowiedniego typu konta');
		RETURN;
	END;
	INSERT INTO tbl_uzytkownik (Imie,Nazwisko,E_mail,Telefon,DataUrodzenia)
	VALUES (@imie,@nazwisko,@e_mail,@telefon,@data_urodzenia);
	SET @output_message = 'Dodano nowego u¿ytkownika ' + 'Imiê: ' + @imie 
		+ ' Nazwisko:' + @nazwisko;
	DECLARE @IdUzytkownika INT;
    SELECT @IdUzytkownika = SCOPE_IDENTITY();
	INSERT INTO tbl_konto(Login,Haslo,IdTypKonta,IdUzytkownika) 
		VALUES (@login, @haslo,2,@IdUzytkownika);
	IF @@ROWCOUNT = 0
	BEGIN 
		PRINT ('Nie uda³o siê dodaæ nowego konta.');
		RETURN;
	END;
	
END;
GO

CREATE OR ALTER PROCEDURE up_AktualizujPub(
    @IdPub INT,
    @Imie VARCHAR(25) = NULL,
    @Nazwisko VARCHAR(25) = NULL,
    @Stanowisko VARCHAR(35) = NULL,
    @NazwaObiektu VARCHAR(30) = NULL,
    @NazwaUlicy VARCHAR(30) = NULL,
    @NumerUlicy VARCHAR(15) = NULL,
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT = NULL,
    @Opis VARCHAR(500) = NULL,
    @NumerLokalu INT = NULL,
    @Ogrodek BIT = NULL,
    @StrefaPalacza BIT = NULL,
    @StrefaDzieci BIT = NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT = NULL,
    @output_message NVARCHAR(255) OUTPUT
)
AS
BEGIN
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_pub WHERE IdPubu = @IdPub)
    BEGIN
        SET @output_message = 'Pub z IdPubu = ' + CAST(@IdPub AS NVARCHAR(10)) + ' nie istnieje.';
        RETURN;
    END

    DECLARE @IdOsoba INT;

    SELECT @IdOsoba = IdOsoba
    FROM tbl_obiekt
    WHERE IdObiektu = (SELECT IdObiektu FROM tbl_pub WHERE IdPubu = @IdPub);

    UPDATE tbl_osoba
    SET Imie = ISNULL(@Imie, Imie),
        Nazwisko = ISNULL(@Nazwisko, Nazwisko),
        Stanowisko = ISNULL(@Stanowisko, Stanowisko)
    WHERE IdOsoba = @IdOsoba;

    UPDATE tbl_obiekt
    SET NazwaObiektu = ISNULL(@NazwaObiektu, NazwaObiektu),
        NazwUlicy = ISNULL(@NazwaUlicy, NazwUlicy),
        NumerUlicy = ISNULL(@NumerUlicy, NumerUlicy),
        Telefon = ISNULL(@Telefon, Telefon),
        E_mail = ISNULL(@E_mail, E_mail),
        SredniaOcena = ISNULL(@SredniaOcena, SredniaOcena),
        Opis = ISNULL(@Opis, Opis)
    WHERE IdObiektu = (SELECT IdObiektu FROM tbl_pub WHERE IdPubu = @IdPub);

    UPDATE tbl_pub
    SET NumerLokalu = ISNULL(@NumerLokalu, NumerLokalu)
    WHERE IdObiektu = (SELECT IdObiektu FROM tbl_pub WHERE IdPubu = @IdPub);

    UPDATE tbl_szczegoly_obiektu
    SET Ogrodek = ISNULL(@Ogrodek, Ogrodek),
        StrefaPalacza = ISNULL(@StrefaPalacza, StrefaPalacza),
        StrefaDzieci = ISNULL(@StrefaDzieci, StrefaDzieci),
        WpuszczanieZwierzat = ISNULL(@WpuszczanieZwierzat, WpuszczanieZwierzat),
        DlaNiepelnosprawnych = ISNULL(@DlaNiepelnosprawnych, DlaNiepelnosprawnych),
        DlaDzieci = ISNULL(@DlaDzieci, DlaDzieci),
        IdDarmowegoWejscia = ISNULL(@IdDarmowegoWejscia, IdDarmowegoWejscia)
    WHERE IdObiektu = (SELECT IdObiektu FROM tbl_pub WHERE IdPubu = @IdPub);

    SET @output_message = 'Zaktualizowano pub';
END;
GO
CREATE OR ALTER PROCEDURE up_AktualizujHarmonogram
	@IdPozycjiHarmonogram INT,
	@IdGodzinyOtwarcia TINYINT  =null,
	@IdGodzinyZamkniecia TINYINT  = null,
	@DataUtworzenia DATE = null,
	@DataWarznosci DATE = null,
	@output_message NVARCHAR(255) OUTPUT
AS
BEGIN
	SET NOCOUNT ON;
	IF NOT EXISTS (SELECT 1 FROM tbl_pozycja_harmonogramu WHERE IdPozycjiHarmonogramu = @IdPozycjiHarmonogram)
	BEGIN 
		SET @output_message =  'Nie odnaleziono';
		RETURN;
	END;
	UPDATE tbl_pozycja_harmonogramu
	SET 
		IdGodzinyOtwarcia = ISNULL(@IdGodzinyOtwarcia,IdGodzinyOtwarcia),
		IdGodzinyZamkniecia = ISNULL(@IdGodzinyZamkniecia,IdGodzinyZamkniecia)
	WHERE
		IdPozycjiHarmonogramu = @IdPozycjiHarmonogram;

	DECLARE @IdHarmonogramu INT = (SELECT IdHarmonogramu FROM tbl_pozycja_harmonogramu where IdPozycjiHarmonogramu = @IdPozycjiHarmonogram);
	
	UPDATE tbl_harmonogram 
	SET 
		DataUtworzenia = ISNULL(@DataUtworzenia,DataUtworzenia),
		DataWarznosci = ISNULL(@DataWarznosci,DataWarznosci)
	WHERE IdHarmonogramu = @IdHarmonogramu;
END;
GO
CREATE OR ALTER PROCEDURE up_DodajUlubione
	
	@IdUzytkownika INT,
	@IdObiektu INT,
	@DataUtworzenia DATE,
	@output_message NVARCHAR(255) OUTPUT	
AS
BEGIN 
	SET NOCOUNT ON;
	IF NOT EXISTS (SELECT 1 FROM tbl_uzytkownik WHERE IdUzytkownik = @IdUzytkownika)
	BEGIN
		PRINT('Nie odnaleziono u¿ytkownika ');
		RETURN;
	END;
	IF NOT EXISTS (SELECT 1 FROM tbl_obiekt WHERE IdObiektu= @IdObiektu)
	BEGIN
		PRINT('Nie odnaleziono obiektu ');
		RETURN;
	END;
	INSERT INTO tbl_ulubione (IdUzytkownika,IdObiektu,DataUtworzenia)
	VALUES (@IdUzytkownika,@IdObiektu,@DataUtworzenia);
	SET @output_message = 'Dodano nowe ulubione';
END;
GO
CREATE OR ALTER PROCEDURE up_UsunUlubione
	@IdUlubionych INT,
	@output_message NVARCHAR(255) OUTPUT	
AS
BEGIN
	SET NOCOUNT ON;
	IF NOT EXISTS (SELECT 1 FROM tbl_ulubione WHERE IdUlubionych = @IdUlubionych)
	BEGIN
		PRINT('Nie odnaleziono');
		RETURN;
	END;

	DELETE FROM tbl_ulubione
	WHERE IdUlubionych = @IdUlubionych;
	SET @output_message = 'Usuniêtu ulubionych';
END;
GO
CREATE OR ALTER PROCEDURE up_DodajOcene
    @Ocena TINYINT,
    @IdObiektu INT,
    @IdUzytkownika INT,
    @DataWystawienia DATE	
AS
BEGIN 
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_uzytkownik WHERE IdUzytkownik = @IdUzytkownika)
    BEGIN
        RAISERROR('Nie odnaleziono u¿ytkownika', 16, 1);
        RETURN;
    END;

    IF NOT EXISTS (SELECT 1 FROM tbl_obiekt WHERE IdObiektu = @IdObiektu)
    BEGIN
        RAISERROR('Nie odnaleziono obiektu', 16, 1);
        RETURN;
    END;
    IF @Ocena NOT BETWEEN 1 AND 5
    BEGIN
        RAISERROR('Nieprawid³owa ocena', 16, 1);
        RETURN;
    END;

    INSERT INTO tbl_wystawiona_ocena (IdOceny, IdObiektu, IdUzytkownika, DataWystawienia)
    VALUES (@Ocena, @IdObiektu, @IdUzytkownika, @DataWystawienia);

END;
GO
CREATE OR ALTER PROCEDURE up_DodajOpinie
	@TrescOpinii VARCHAR(200),
	@IdUzytkownika INT,
	@IdObiektu INT,
	@DataWystawienia DATE,
	@output_message NVARCHAR(255) OUTPUT	
AS
BEGIN 
	SET NOCOUNT ON;

	IF NOT EXISTS (SELECT 1 FROM tbl_uzytkownik WHERE IdUzytkownik = @IdUzytkownika)
	BEGIN
		SET @output_message = 'Nie odnaleziono u¿ytkownika';
		RETURN;
	END;

	IF NOT EXISTS (SELECT 1 FROM tbl_obiekt WHERE IdObiektu = @IdObiektu)
	BEGIN
		SET @output_message = 'Nie odnaleziono obiektu';
		RETURN;
	END;


	INSERT INTO tbl_opinia (Tresc, IdUzytkownika, IdObiektu, DataWystawienia)
	VALUES (@TrescOpinii, @IdUzytkownika, @IdObiektu, @DataWystawienia);

	SET @output_message = 'Dodano now¹ opiniê';
END;
GO
CREATE OR ALTER PROCEDURE up_EdycjaOpini
	@IdOpinii INT,
	@NowaTresc VARCHAR(500),
	@output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
	SET NOCOUNT ON;
	UPDATE tbl_opinia
	SET Tresc = @NowaTresc
	WHERE IdOpini = @IdOpinii;

	SET @output_message = 'Zaktualizowano opiniê';
END;
GO
CREATE OR ALTER PROCEDURE up_EdytujOcene
    @IdWystawionejOceny INT,
    @NowaOcen TINYINT,
    @output_message NVARCHAR(255) OUTPUT
AS
BEGIN 
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_ocena WHERE IdOceny = @NowaOcen)
    BEGIN
        SET @output_message = 'Nie odnaleziono wartoœci oceny';
        RETURN;
    END;

    UPDATE tbl_wystawiona_ocena
    SET IdOceny = @NowaOcen
    WHERE IdWystawionejOceny = @IdWystawionejOceny;

    IF @@ROWCOUNT = 0 
    BEGIN
        SET @output_message = 'Nie dokonano zmian w ocenie';
        RETURN;
    END;

    SET @output_message = 'Zaktualizowano ocenê';
END;
GO
CREATE OR ALTER PROCEDURE up_UsunWystawionaOcena
	@IdWystawioneOcena INT,
	@output_message NVARCHAR(255) OUTPUT	
AS
BEGIN 
	SET NOCOUNT ON;
	IF NOT EXISTS (SELECT 1 FROM tbl_wystawiona_ocena WHERE IdWystawionejOceny = @IdWystawioneOcena)
	BEGIN
		SET @output_message ='Nie odnaleziono';
		RETURN;
	END;
	DELETE FROM tbl_wystawiona_ocena
	WHERE IdWystawionejOceny =@IdWystawioneOcena;
	SET @output_message ='Usuniêto wystwion¹ ocene ';
END;
GO
CREATE OR ALTER PROCEDURE up_AktualizujMiejsceKulturowe(
    @IdMiejscaKulturowego INT,
    @Imie VARCHAR(25) = NULL,
    @Nazwisko VARCHAR(25) = NULL,
    @Stanowisko VARCHAR(35) = NULL,
    @NazwaObiektu VARCHAR(30) = NULL,
    @NazwaUlicy VARCHAR(30) = NULL,
    @NumerUlicy VARCHAR(15) = NULL,
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT = NULL,
    @Opis VARCHAR(500) = NULL,
    @Ogrodek BIT = NULL,
    @StrefaPalacza BIT = NULL,
    @StrefaDzieci BIT = NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT = NULL,
    @output_message NVARCHAR(255) OUTPUT
)
AS
BEGIN
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_miejsce_kulturowe WHERE IdMiejscaKulturowego = @IdMiejscaKulturowego)
    BEGIN
        SET @output_message = 'Restauracja  = ' + CAST(@IdMiejscaKulturowego AS NVARCHAR(10)) + ' nie istnieje.';
        RETURN;
    END

    DECLARE @IdOsoba INT;

    SELECT @IdOsoba = IdOsoba
    FROM tbl_obiekt
    WHERE IdObiektu = (SELECT IdObiektu FROM tbl_miejsce_kulturowe WHERE IdMiejscaKulturowego = @IdMiejscaKulturowego);

    UPDATE tbl_osoba
    SET Imie = ISNULL(@Imie, Imie),
        Nazwisko = ISNULL(@Nazwisko, Nazwisko),
        Stanowisko = ISNULL(@Stanowisko, Stanowisko)
    WHERE IdOsoba = @IdOsoba;

    UPDATE tbl_obiekt
    SET NazwaObiektu = ISNULL(@NazwaObiektu, NazwaObiektu),
        NazwUlicy = ISNULL(@NazwaUlicy, NazwUlicy),
        NumerUlicy = ISNULL(@NumerUlicy, NumerUlicy),
        Telefon = ISNULL(@Telefon, Telefon),
        E_mail = ISNULL(@E_mail, E_mail),
        SredniaOcena = ISNULL(@SredniaOcena, SredniaOcena),
        Opis = ISNULL(@Opis, Opis)
    WHERE IdObiektu = (SELECT IdObiektu from tbl_miejsce_kulturowe WHERE IdMiejscaKulturowego = @IdMiejscaKulturowego);

   
    UPDATE tbl_szczegoly_obiektu
    SET Ogrodek = ISNULL(@Ogrodek, Ogrodek),
        StrefaPalacza = ISNULL(@StrefaPalacza, StrefaPalacza),
        StrefaDzieci = ISNULL(@StrefaDzieci, StrefaDzieci),
        WpuszczanieZwierzat = ISNULL(@WpuszczanieZwierzat, WpuszczanieZwierzat),
        DlaNiepelnosprawnych = ISNULL(@DlaNiepelnosprawnych, DlaNiepelnosprawnych),
        DlaDzieci = ISNULL(@DlaDzieci, DlaDzieci),
        IdDarmowegoWejscia = ISNULL(@IdDarmowegoWejscia, IdDarmowegoWejscia)
    WHERE IdObiektu = (SELECT IdObiektu FROM tbl_miejsce_kulturowe WHERE IdMiejscaKulturowego = @IdMiejscaKulturowego);

    SET @output_message = 'Zaktualizowano miejsce kulturowe';
END;
GO
CREATE OR ALTER PROCEDURE up_AktualizujMural
(
    @IdMuralu INT,
    @Imie VARCHAR(25) = NULL,
    @Nazwisko VARCHAR(25) = NULL,
    @Stanowisko VARCHAR(35) = NULL,
    @NazwaObiektu VARCHAR(30) = NULL,
    @NazwaUlicy VARCHAR(30) = NULL,
    @NumerUlicy VARCHAR(15) = NULL,
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT = NULL,
    @Opis VARCHAR(500) = NULL,
    @Historia VARCHAR(300) = NULL,
    @OpisDotarcia VARCHAR(500) = NULL,
    @Ogrodek BIT = NULL,
    @StrefaPalacza BIT = NULL,
    @StrefaDzieci BIT = NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT = NULL,
    @output_message NVARCHAR(255) = NULL OUTPUT
)
AS
BEGIN
    SET NOCOUNT ON;

    IF NOT EXISTS (SELECT 1 FROM tbl_mural WHERE IdMuralu = @IdMuralu)
    BEGIN
        SET @output_message = 'Mural o IdMuralu = ' + CAST(@IdMuralu AS NVARCHAR(10)) + ' nie istnieje.';
        RETURN;
    END

    BEGIN TRY
        DECLARE @IdOsoba INT;

        SELECT @IdOsoba = IdOsoba
        FROM tbl_obiekt
        WHERE IdObiektu = (SELECT IdObiektu FROM tbl_mural WHERE IdMuralu = @IdMuralu);

        UPDATE tbl_osoba
        SET Imie = ISNULL(@Imie, Imie),
            Nazwisko = ISNULL(@Nazwisko, Nazwisko),
            Stanowisko = ISNULL(@Stanowisko, Stanowisko)
        WHERE IdOsoba = @IdOsoba;

        UPDATE tbl_obiekt
        SET NazwaObiektu = ISNULL(@NazwaObiektu, NazwaObiektu),
            NazwUlicy = ISNULL(@NazwaUlicy, NazwUlicy),
            NumerUlicy = ISNULL(@NumerUlicy, NumerUlicy),
            Telefon = ISNULL(@Telefon, Telefon),
            E_mail = ISNULL(@E_mail, E_mail),
            SredniaOcena = ISNULL(@SredniaOcena, SredniaOcena),
            Opis = ISNULL(@Opis, Opis)
        WHERE IdObiektu = (SELECT IdObiektu from tbl_mural WHERE IdMuralu = @IdMuralu);
		UPDATE tbl_mural 
		SET Historia = ISNULL(@Historia, Historia),
            OpisDotarcia = ISNULL(@OpisDotarcia, OpisDotarcia)
		WHERE IdMuralu = @IdMuralu;
        UPDATE tbl_szczegoly_obiektu
        SET Ogrodek = ISNULL(@Ogrodek, Ogrodek),
            StrefaPalacza = ISNULL(@StrefaPalacza, StrefaPalacza),
            StrefaDzieci = ISNULL(@StrefaDzieci, StrefaDzieci),
            WpuszczanieZwierzat = ISNULL(@WpuszczanieZwierzat, WpuszczanieZwierzat),
            DlaNiepelnosprawnych = ISNULL(@DlaNiepelnosprawnych, DlaNiepelnosprawnych),
            DlaDzieci = ISNULL(@DlaDzieci, DlaDzieci),
            IdDarmowegoWejscia = ISNULL(@IdDarmowegoWejscia, IdDarmowegoWejscia)
        WHERE IdObiektu = (SELECT IdObiektu FROM tbl_mural WHERE IdMuralu = @IdMuralu);

        SET @output_message = 'Zaktualizowano mural o IdMuralu = ' + CAST(@IdMuralu AS NVARCHAR(10));
    END TRY
    BEGIN CATCH
        SET @output_message = ERROR_MESSAGE();
    END CATCH
END;
GO
CREATE OR ALTER PROCEDURE up_DodajPub
    @Imie VARCHAR(25),
    @Nazwisko VARCHAR(25),
    @Stanowisko VARCHAR(35),
    @NazwaObiektu VARCHAR(30),
    @NazwaUlicy VARCHAR(30),
    @NumerUlicy VARCHAR(15),
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT NULL,
    @Opis VARCHAR(500)  NULL,
    @NumerLokalu INT =NULL,
    @Ogrodek BIT  = NULL,
    @StrefaPalacza BIT  = NULL,
    @StrefaDzieci BIT =  NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT  =  NULL
AS
BEGIN 
    SET NOCOUNT ON;

    BEGIN TRANSACTION;

    BEGIN TRY
        INSERT INTO tbl_osoba (Imie, Nazwisko, Stanowisko)
        VALUES (@Imie, @Nazwisko, @Stanowisko);

        DECLARE @IdOsoby INT;
        SELECT @IdOsoby = SCOPE_IDENTITY();

        INSERT INTO tbl_obiekt (NazwaObiektu, NazwUlicy, NumerUlicy, Telefon, E_mail, SredniaOcena, Opis, IdTypObiektu, IdOsoba)
        VALUES (@NazwaObiektu, @NazwaUlicy, @NumerUlicy, @Telefon, @E_mail, @SredniaOcena, @Opis, 6, @IdOsoby);

        DECLARE @IdObiektu INT;
        SELECT @IdObiektu = SCOPE_IDENTITY();

        INSERT INTO tbl_pub (NumerLokalu, IdObiektu)
        VALUES (@NumerLokalu, @IdObiektu);

        INSERT INTO tbl_szczegoly_obiektu (Ogrodek, StrefaPalacza, StrefaDzieci, WpuszczanieZwierzat, DlaNiepelnosprawnych, DlaDzieci, IdDarmowegoWejscia, IdObiektu)
        VALUES (@Ogrodek, @StrefaPalacza, @StrefaDzieci, @WpuszczanieZwierzat, @DlaNiepelnosprawnych, @DlaDzieci, @IdDarmowegoWejscia, @IdObiektu);

        COMMIT TRANSACTION;
    

    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        
    END CATCH
END;
GO
CREATE OR ALTER PROCEDURE up_UsunObiekt
    @IdObiektu INT,
    @output_message NVARCHAR(255) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @IDHARMONOGRAMU INT;
    IF NOT EXISTS (SELECT 1 FROM tbl_obiekt WHERE IdObiektu = @IdObiektu)
    BEGIN 
        SET @output_message = 'Nie odnaleziono obiektu';
        RETURN;
    END;
    DELETE FROM tbl_pozycja_harmonogramu WHERE IdHarmonogramu IN (SELECT IdHarmonogramu FROM tbl_harmonogram WHERE IdObiektu = @IdObiektu);
    DELETE FROM tbl_harmonogram WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_wydarzenie WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_opinia WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_wystawiona_ocena WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_ulubione WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_szczegoly_obiektu WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_miejsce_kulturowe WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_mural WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_restauracja WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_pub WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_zdjecie WHERE IdObiektu = @IdObiektu;
    DELETE FROM tbl_obiekt WHERE IdObiektu = @IdObiektu;

    SET @output_message = 'Usuniêto obiekt';
END;
GO
CREATE OR ALTER PROCEDURE up_DodajHarmonogram	
	@DataUtworzenia DATE,
	@DataWarznosci DATE,
	@IdGodzinyOtwarcia TINYINT = NULL,
	@IdGodzinyZamkniecia TINYINT =NULL,
	@IdDniaTygodnia TINYINT = NULL,
	 @output_message NVARCHAR(255) OUTPUT	
AS
BEGIN 
	DECLARE @IdObiektu INT;
	SELECT @IdObiektu = (SELECT MAX(IdObiektu) FROM tbl_obiekt);

	INSERT INTO tbl_harmonogram (DataUtworzenia,DataWarznosci,IdObiektu)
	VALUES (@DataUtworzenia, @DataWarznosci, @IdObiektu);

	DECLARE @IdHarmonogramu INT;
	SELECT @IdHarmonogramu =  SCOPE_IDENTITY();

	INSERT INTO tbl_pozycja_harmonogramu (IdHarmonogramu,IdGodzinyOtwarcia,IdGodzinyZamkniecia,IdDniaTygodnia)
	VALUES (@IdHarmonogramu,@IdGodzinyOtwarcia,@IdGodzinyZamkniecia,@IdDniaTygodnia);

	SET @output_message = 'Dodano';
END;
GO
CREATE OR ALTER PROCEDURE up_DodajRestauracje
    @Imie VARCHAR(25),
    @Nazwisko VARCHAR(25),
    @Stanowisko VARCHAR(35),
    @NazwaObiektu VARCHAR(30),
    @NazwaUlicy VARCHAR(30),
    @NumerUlicy VARCHAR(15),
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT NULL,
    @Opis VARCHAR(500)  NULL,
    @NumerLokalu INT =NULL,
	@IdTypuKuchni TINYINT,
    @Ogrodek BIT  = NULL,
    @StrefaPalacza BIT  = NULL,
    @StrefaDzieci BIT =  NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT  =  NULL
AS
BEGIN 
    SET NOCOUNT ON;

    BEGIN TRANSACTION;

    BEGIN TRY
        INSERT INTO tbl_osoba (Imie, Nazwisko, Stanowisko)
        VALUES (@Imie, @Nazwisko, @Stanowisko);

        DECLARE @IdOsoby INT;
        SELECT @IdOsoby = SCOPE_IDENTITY();

        INSERT INTO tbl_obiekt (NazwaObiektu, NazwUlicy, NumerUlicy, Telefon, E_mail, SredniaOcena, Opis, IdTypObiektu, IdOsoba)
        VALUES (@NazwaObiektu, @NazwaUlicy, @NumerUlicy, @Telefon, @E_mail, @SredniaOcena, @Opis, 6, @IdOsoby);

        DECLARE @IdObiektu INT;
        SELECT @IdObiektu = SCOPE_IDENTITY();

        INSERT INTO tbl_restauracja(IdObiektu,IdTypuKuchni)
		VALUES (@IdObiektu, @IdTypuKuchni);

        INSERT INTO tbl_szczegoly_obiektu (Ogrodek, StrefaPalacza, StrefaDzieci, WpuszczanieZwierzat, DlaNiepelnosprawnych, DlaDzieci, IdDarmowegoWejscia, IdObiektu)
        VALUES (@Ogrodek, @StrefaPalacza, @StrefaDzieci, @WpuszczanieZwierzat, @DlaNiepelnosprawnych, @DlaDzieci, @IdDarmowegoWejscia, @IdObiektu);

        COMMIT TRANSACTION;
    

    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        
    END CATCH
END;
GO
CREATE OR ALTER PROCEDURE up_AktualizujRestauracje 
    @IdRestauracji INT,
    @Imie VARCHAR(25) = NULL,
    @Nazwisko VARCHAR(25) = NULL,
    @Stanowisko VARCHAR(35) = NULL,
    @NazwaObiektu VARCHAR(30) = NULL,
    @NazwaUlicy VARCHAR(30) = NULL,
    @NumerUlicy VARCHAR(15) = NULL,
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT = NULL,
    @Opis VARCHAR(500) = NULL,
    @NumerLokalu INT = NULL,
    @Ogrodek BIT = NULL,
    @IdTypuKuchni TINYINT = NULL,
    @StrefaPalacza BIT = NULL,
    @StrefaDzieci BIT = NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT = NULL

AS
BEGIN
    SET NOCOUNT ON;
    IF NOT EXISTS (SELECT 1 FROM tbl_restauracja WHERE IdRestauracji = @IdRestauracji)
    BEGIN
        print ( 'Restauracja o id: ' + CAST(@IdRestauracji AS NVARCHAR(10)) + ' nie istnieje.');
        RETURN;
    END
    DECLARE @IdObiektu INT, @IdOsoba INT;

    SELECT @IdObiektu = IdObiektu
    FROM tbl_restauracja
    WHERE IdRestauracji = @IdRestauracji;

    SELECT @IdOsoba = IdOsoba
    FROM tbl_obiekt
    WHERE IdObiektu = @IdObiektu;

    UPDATE tbl_osoba
    SET 
        Imie = ISNULL(@Imie, Imie),
        Nazwisko = ISNULL(@Nazwisko, Nazwisko),
        Stanowisko = ISNULL(@Stanowisko, Stanowisko)
    WHERE IdOsoba = @IdOsoba;

    UPDATE tbl_obiekt
    SET 
        NazwaObiektu = ISNULL(@NazwaObiektu, NazwaObiektu),
        NazwUlicy = ISNULL(@NazwaUlicy, NazwUlicy),
        NumerUlicy = ISNULL(@NumerUlicy, NumerUlicy),
        Telefon = ISNULL(@Telefon, Telefon),
        E_mail = ISNULL(@E_mail, E_mail),
        SredniaOcena = ISNULL(@SredniaOcena, SredniaOcena),
        Opis = ISNULL(@Opis, Opis)
    WHERE IdObiektu = @IdObiektu;

    UPDATE tbl_restauracja
    SET 
        NumerLokalu = ISNULL(@NumerLokalu, NumerLokalu),
        IdTypuKuchni = ISNULL(@IdTypuKuchni, IdTypuKuchni)
    WHERE IdRestauracji = @IdRestauracji;

    UPDATE tbl_szczegoly_obiektu
    SET 
        Ogrodek = ISNULL(@Ogrodek, Ogrodek),
        StrefaPalacza = ISNULL(@StrefaPalacza, StrefaPalacza),
        StrefaDzieci = ISNULL(@StrefaDzieci, StrefaDzieci),
        WpuszczanieZwierzat = ISNULL(@WpuszczanieZwierzat, WpuszczanieZwierzat),
        DlaNiepelnosprawnych = ISNULL(@DlaNiepelnosprawnych, DlaNiepelnosprawnych),
        DlaDzieci = ISNULL(@DlaDzieci, DlaDzieci),
        IdDarmowegoWejscia = ISNULL(@IdDarmowegoWejscia, IdDarmowegoWejscia)
    WHERE IdObiektu = @IdObiektu;

  
END;
GO
CREATE OR ALTER PROCEDURE up_DodajMural
    @Imie VARCHAR(25),
    @Nazwisko VARCHAR(25),
    @Stanowisko VARCHAR(35),
    @NazwaObiektu VARCHAR(30),
    @NazwaUlicy VARCHAR(30),
    @NumerUlicy VARCHAR(15),
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT NULL,
    @Opis VARCHAR(500)=  NULL,
	@OpisDotarcia VARCHAR(500)= NULL,
	@Historia VARCHAR(300)= NULL,
    @Ogrodek BIT  = NULL,
    @StrefaPalacza BIT  = NULL,
    @StrefaDzieci BIT =  NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT  =  NULL
AS
BEGIN 
    SET NOCOUNT ON;

    BEGIN TRANSACTION;

    BEGIN TRY
        INSERT INTO tbl_osoba (Imie, Nazwisko, Stanowisko)
        VALUES (@Imie, @Nazwisko, @Stanowisko);

        DECLARE @IdOsoby INT;
        SELECT @IdOsoby = SCOPE_IDENTITY();

        INSERT INTO tbl_obiekt (NazwaObiektu, NazwUlicy, NumerUlicy, Telefon, E_mail, SredniaOcena, Opis, IdTypObiektu, IdOsoba)
        VALUES (@NazwaObiektu, @NazwaUlicy, @NumerUlicy, @Telefon, @E_mail, @SredniaOcena, @Opis, 6, @IdOsoby);

        DECLARE @IdObiektu INT;
        SELECT @IdObiektu = SCOPE_IDENTITY();

        INSERT INTO tbl_mural(IdObiektu,OpisDotarcia,Historia)
		VALUES (@IdObiektu,@OpisDotarcia, @Historia);

        INSERT INTO tbl_szczegoly_obiektu (Ogrodek, StrefaPalacza, StrefaDzieci, WpuszczanieZwierzat, DlaNiepelnosprawnych, DlaDzieci, IdDarmowegoWejscia, IdObiektu)
        VALUES (@Ogrodek, @StrefaPalacza, @StrefaDzieci, @WpuszczanieZwierzat, @DlaNiepelnosprawnych, @DlaDzieci, @IdDarmowegoWejscia, @IdObiektu);

        COMMIT TRANSACTION;
    

    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        
    END CATCH
END;
GO
CREATE OR ALTER PROCEDURE up_DodajMiejsceKulturowe
    @Imie VARCHAR(25),
    @IdTypObiektu TINYINT,
    @Nazwisko VARCHAR(25),
    @Stanowisko VARCHAR(35),
    @NazwaObiektu VARCHAR(30),
    @NazwaUlicy VARCHAR(30),
    @NumerUlicy VARCHAR(15),
    @Telefon CHAR(15) = NULL,
    @E_mail VARCHAR(30) = NULL,
    @SredniaOcena FLOAT = NULL,
    @Opis VARCHAR(500) = NULL,
    @NumerLokalu INT = NULL,
    @Ogrodek BIT = NULL,
    @StrefaPalacza BIT = NULL,
    @StrefaDzieci BIT = NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @IdDarmowegoWejscia TINYINT = NULL
  
AS
BEGIN 
    SET NOCOUNT ON;

        INSERT INTO tbl_osoba (Imie, Nazwisko, Stanowisko)
        VALUES (@Imie, @Nazwisko, @Stanowisko);

        DECLARE @IdOsoby INT;
        SELECT @IdOsoby = SCOPE_IDENTITY();

        INSERT INTO tbl_obiekt (NazwaObiektu, NazwUlicy, NumerUlicy, Telefon, E_mail, SredniaOcena, Opis, IdTypObiektu, IdOsoba)
        VALUES (@NazwaObiektu, @NazwaUlicy, @NumerUlicy, @Telefon, @E_mail, @SredniaOcena, @Opis, @IdTypObiektu, @IdOsoby);

        DECLARE @IdObiektu INT;
        SELECT @IdObiektu = SCOPE_IDENTITY();

        INSERT INTO tbl_miejsce_kulturowe (IdObiektu)
        VALUES (@IdObiektu);

        INSERT INTO tbl_szczegoly_obiektu (Ogrodek, StrefaPalacza, StrefaDzieci, WpuszczanieZwierzat, DlaNiepelnosprawnych, DlaDzieci, IdDarmowegoWejscia, IdObiektu)
        VALUES (@Ogrodek, @StrefaPalacza, @StrefaDzieci, @WpuszczanieZwierzat, @DlaNiepelnosprawnych, @DlaDzieci, @IdDarmowegoWejscia, @IdObiektu);
 
    
END;
GO
