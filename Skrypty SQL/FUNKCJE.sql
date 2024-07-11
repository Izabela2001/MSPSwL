USE MULTIWYSZUKIWARKA
GO
CREATE OR ALTER FUNCTION uf_WyswietlWydarzeniaWTerminie()
RETURNS TABLE
AS
RETURN
(
    SELECT W.DataWydarzenia AS 'Data wydarzenia', 
		   TW.NazwaTypu AS 'Rodzaj wydarzenia', 
		   W.NazwaWydarzenia AS 'Nazwa wydarzenia',
		   O.Nazwa AS 'Nazwa organizatora', 
		   OB.NazwaObiektu AS 'Nazwa obiektu', 
		   W.Informacje AS 'Informacje'
	FROM tbl_wydarzenie AS W
	INNER JOIN tbl_typ_wydarzenia AS TW ON W.IdTypWydarzenia = TW.IdTypuWydarzenia
	INNER JOIN tbl_organizator AS O ON W.IdOrganizatora = O.IdOrganizatora
	INNER JOIN tbl_obiekt AS OB ON W.IdObiektu = OB.IdObiektu
    WHERE
        DataWydarzenia >= GETDATE()
);
GO
CREATE OR ALTER FUNCTION uf_WyszukajWydarzenia
(
    @DataOd DATE = NULL,
    @DataDo DATE = NULL,
    @IdTypWydarzenia TINYINT = NULL
)
RETURNS TABLE
AS
RETURN
(
    SELECT W.DataWydarzenia AS 'Data wydarzenia', 
           TW.NazwaTypu AS 'Rodzaj wydarzenia', 
           W.NazwaWydarzenia AS 'Nazwa wydarzenia',
           O.Nazwa AS 'Nazwa organizatora', 
           OB.NazwaObiektu AS 'Nazwa obiektu', 
           W.Informacje AS 'Informacje'
    FROM tbl_wydarzenie AS W
    INNER JOIN tbl_typ_wydarzenia AS TW ON W.IdTypWydarzenia = TW.IdTypuWydarzenia
    INNER JOIN tbl_organizator AS O ON W.IdOrganizatora = O.IdOrganizatora
    INNER JOIN tbl_obiekt AS OB ON W.IdObiektu = OB.IdObiektu
    WHERE (@DataOd IS NULL OR @DataDo IS NULL OR W.DataWydarzenia BETWEEN @DataOd AND @DataDo)
      AND (@IdTypWydarzenia IS NULL OR W.IdTypWydarzenia = @IdTypWydarzenia)
);
GO
CREATE OR ALTER FUNCTION uf_WyszukajObiekty
(
	@TypObiektuId INT = NULL,
    @Ogrodek BIT = NULL,
    @StrefaPalacza BIT = NULL,
    @StrefaDzieci BIT = NULL,
    @WpuszczanieZwierzat BIT = NULL,
    @DlaNiepelnosprawnych BIT = NULL,
    @DlaDzieci BIT = NULL,
    @GodzinaOtwarcia VARCHAR(5) = NULL,
    @GodzinaZamkniecia VARCHAR(5) = NULL
)
RETURNS TABLE
AS
RETURN 
(
    SELECT 
        O.IdObiektu AS 'Idobiektu', 
        O.NazwaObiektu AS 'Nazwa obiektu', 
        O.NazwUlicy AS 'Nazwa ulicy',
        O.NumerUlicy AS 'Numer ulicy',
        O.Telefon AS 'Telefon', 
        O.E_mail AS 'E-mail', 
        O.SredniaOcena AS 'Œrednia ocena',
        O.Opis AS 'Opis', 
        TOB.NazwaTypu AS 'Rodzaj obiektu', 
        GOS.Wartosc AS 'Godzina Otwarcia',
        GZ.Wartosc AS 'Godzina zamkniêcia',
        Z.NazwaZdjecia AS 'Nazwa zdjêcia'
    FROM 
        tbl_obiekt AS O
    INNER JOIN 
        tbl_typ_obiektu AS TOB ON O.IdTypObiektu = TOB.IdTypObiektu
    LEFT JOIN 
        tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
    LEFT JOIN 
        tbl_szczegoly_obiektu AS SO ON O.IdObiektu = SO.IdObiektu
    LEFT JOIN 
        tbl_harmonogram AS HO ON O.IdObiektu = HO.IdObiektu
	LEFT JOIN 
		tbl_pozycja_harmonogramu AS PH ON HO.IdHarmonogramu = PH.IdHarmonogramu
    LEFT JOIN 
        tbl_godzina AS GOS ON PH.IdGodzinyOtwarcia = GOS.IdGodziny
    LEFT JOIN 
        tbl_godzina AS GZ ON PH.IdGodzinyZamkniecia = GZ.IdGodziny
    WHERE 
        (@TypObiektuId IS NULL OR O.IdTypObiektu = @TypObiektuId)
        AND (@Ogrodek IS NULL OR SO.Ogrodek = @Ogrodek)
        AND (@StrefaPalacza IS NULL OR SO.StrefaPalacza = @StrefaPalacza)
        AND (@StrefaDzieci IS NULL OR SO.StrefaDzieci = @StrefaDzieci)
        AND (@WpuszczanieZwierzat IS NULL OR SO.WpuszczanieZwierzat = @WpuszczanieZwierzat)
        AND (@DlaNiepelnosprawnych IS NULL OR SO.DlaNiepelnosprawnych = @DlaNiepelnosprawnych)
        AND (@DlaDzieci IS NULL OR SO.DlaDzieci = @DlaDzieci)
        AND (
            (@GodzinaOtwarcia IS NULL AND @GodzinaZamkniecia IS NULL) 
            OR (GOS.Wartosc <= @GodzinaOtwarcia AND GZ.Wartosc >= @GodzinaZamkniecia)
        )
		AND
		(CAST(HO.DataWarznosci AS DATE) <= CAST(GETDATE() AS DATE))AND 
		PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE()));
GO
CREATE OR ALTER FUNCTION uf_DarmoweAtrakacje()
RETURNS TABLE
AS
RETURN 
(
    SELECT 
        O.NazwaObiektu AS 'Nazwa obiektu',
        O.NazwUlicy AS 'Nazwa ulicy',
        O.NumerUlicy AS 'Numer ulicy',
        O.Telefon AS 'Telefon',
        O.E_mail AS 'E-mail',
        O.SredniaOcena AS 'Œrednia ocena',
        O.Opis AS 'Opis',
        TB.NazwaTypu AS 'Rodzaj obiektu',
        GOS.Wartosc AS 'Godzina otwarcia',
        GZ.Wartosc AS 'Godzina zamkniêcia',
        DT_DarmoweWejscie.NazwaDniaTygodnia AS 'Darmowe wejœcie',
        Z.NazwaZdjecia AS 'NazwaZdjecia'
    FROM 
        tbl_obiekt AS O
    INNER JOIN 
        tbl_typ_obiektu AS TB ON O.IdTypObiektu = TB.IdTypObiektu
    INNER JOIN 
        tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
    INNER JOIN 
        tbl_szczegoly_obiektu AS SO ON O.IdObiektu = SO.IdObiektu
    LEFT JOIN 
        tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
	LEFT JOIN 
		tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
    LEFT JOIN 
        tbl_dzien_tygodnia AS DT_DarmoweWejscie ON SO.IdDarmowegoWejscia = DT_DarmoweWejscie.IdDniaTygodnia
    LEFT JOIN 
        tbl_godzina AS GOS ON PH.IdGodzinyOtwarcia = GOS.IdGodziny
    LEFT JOIN 
        tbl_godzina AS GZ ON PH.IdGodzinyZamkniecia = GZ.IdGodziny
    WHERE 
        SO.IdDarmowegoWejscia IS NOT NULL AND 
		CAST(H.DataWarznosci AS DATE) <= CAST(GETDATE() AS DATE)AND 
	PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE())
);
GO
CREATE OR ALTER FUNCTION uf_FilterDarmowych(
    @IdDarmowegoWejscia TINYINT = NULL
)
RETURNS TABLE
AS
RETURN 
(
    SELECT 
        O.NazwaObiektu AS 'Nazwa obiektu',
        O.NazwUlicy AS 'Nazwa ulicy',
        O.NumerUlicy AS 'Numer ulicy',
        O.Telefon AS 'Telefon',
        O.E_mail AS 'E-mail',
        O.SredniaOcena AS 'Œrednia ocena',
        O.Opis AS 'Opis',
        TB.NazwaTypu AS 'Rodzaj obiektu',
        GOS.Wartosc AS 'Godzina otwarcia',
        GZ.Wartosc AS 'Godzina zamkniêcia',
        DT_DarmoweWejscie.NazwaDniaTygodnia AS 'Darmowe wejœcie',
        Z.NazwaZdjecia AS 'NazwaZdjecia'
    FROM 
        tbl_obiekt AS O
    INNER JOIN 
        tbl_typ_obiektu AS TB ON O.IdTypObiektu = TB.IdTypObiektu
    INNER JOIN 
        tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
    INNER JOIN 
        tbl_szczegoly_obiektu AS SO ON O.IdObiektu = SO.IdObiektu
    LEFT JOIN 
        tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
    LEFT JOIN 
        tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
    LEFT JOIN 
        tbl_dzien_tygodnia AS DT_DarmoweWejscie ON SO.IdDarmowegoWejscia = DT_DarmoweWejscie.IdDniaTygodnia
    LEFT JOIN 
        tbl_godzina AS GOS ON PH.IdGodzinyOtwarcia = GOS.IdGodziny
    LEFT JOIN 
        tbl_godzina AS GZ ON PH.IdGodzinyZamkniecia = GZ.IdGodziny
    WHERE 
        SO.IdDarmowegoWejscia = @IdDarmowegoWejscia AND
		CAST(H.DataWarznosci AS DATE) <= CAST(GETDATE() AS DATE)AND 
	PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE())
);
GO
CREATE OR ALTER FUNCTION uf_OpinieObiektu(
	@IdObiektu INT
	)
RETURNS TABLE
AS
RETURN
(
	SELECT OB.IdObiektu AS 'Idobiektu', O.Tresc AS 'Tresc', K.Login AS 'Login',
			O.DataWystawienia AS 'Data wystawienia'
	FROM tbl_opinia as O
	INNER JOIN tbl_obiekt AS OB ON O.IdObiektu = OB.IdObiektu
	INNER JOIN tbl_uzytkownik AS U ON O.IdUzytkownika = U.IdUzytkownik
	LEFT JOIN tbl_konto AS K ON U.IdUzytkownik = K.IdUzytkownika
	WHERE O.IdObiektu = @IdObiektu
	);
GO
CREATE OR ALTER FUNCTION uf_WystawioneOceny(
	@Idobiektu INT
	)
RETURNS TABLE
AS
RETURN
(	
	SELECT WO.IdOceny AS 'Wystawiona ocena', WO.DataWystawienia AS 'Data wystawienia',
		k.Login AS 'Login', O.IdObiektu AS 'Idobiektu'
	FROM tbl_wystawiona_ocena AS WO 
	INNER JOIN tbl_obiekt AS O ON WO.IdObiektu = O.IdObiektu
	INNER JOIN tbl_uzytkownik AS U ON WO.IdUzytkownika = U.IdUzytkownik
	LEFT JOIN tbl_konto AS K ON U.IdUzytkownik = K.IdUzytkownika
	WHERE WO.IdObiektu = @Idobiektu
	);
GO
CREATE OR ALTER FUNCTION uf_WyszukajUzytkownika(
	@Login varchar(25),
	@haslo varchar(25)
)
RETURNS TABLE
AS
RETURN
(
    SELECT K.IdKonta AS 'IdKonta', K.IdUzytkownika AS 'IdUzytkownika',
	K.IdTypKonta AS 'IdTypKonta'
    FROM tbl_konto AS k
    JOIN tbl_uzytkownik AS u ON k.IdUzytkownika = u.IdUzytkownik
	WHERE K.Login = @Login AND K.Haslo = @haslo
   
);
GO
CREATE OR ALTER FUNCTION dbo.uf_SprawdzenieLoginu
(
    @Login VARCHAR(15)
)
RETURNS TABLE
AS
RETURN
(
    SELECT COUNT(*) AS Liczba
    FROM tbl_konto
    WHERE Login = @Login
);
GO
CREATE OR ALTER FUNCTION dbo.uf_SprawdzEmail
(
    @E_mail VARCHAR(50)
)
RETURNS TABLE
AS
RETURN
(
    SELECT COUNT(*) AS Liczba
    FROM tbl_uzytkownik
    WHERE E_mail = @E_mail
);
GO
CREATE OR ALTER FUNCTION uf_Organizatorzy(
)
RETURNS TABLE
AS
RETURN 
(
    SELECT O.IdOrganizatora AS 'Identyfikator organizatora', O.Nazwa AS 'Nazwa', O.Nip AS 'NIP', O.E_mail AS 'E-mail',
	O.Telefon AS 'Telefon', OS.Imie AS 'Imie', OS.Nazwisko AS 'Nazwisko' ,OS.Stanowisko AS 'Stanowisko'
	FROM tbl_organizator AS O
	INNER JOIN tbl_osoba AS OS ON O.IdOsoby = OS.IdOsoba
);
GO
CREATE OR ALTER FUNCTION uf_Opinie(
)
RETURNS TABLE
AS
RETURN 
(
    SELECT O.IdOpini AS 'Identyfikator opini', O.Tresc AS 'Treœæ',
	O.DataWystawienia AS 'Data wystawienia', U.Imie AS 'Imiê',
	U.Nazwisko AS 'Nazwisko', K.IdUzytkownika AS 'Login',
	OB.NazwaObiektu AS 'Nazwa obiektu'
	FROM tbl_opinia AS O
	INNER JOIN tbl_uzytkownik AS U ON O.IdUzytkownika = U.IdUzytkownik
	LEFT JOIN tbl_konto AS K ON U.IdUzytkownik =K.IdUzytkownika
	INNER JOIN tbl_obiekt AS OB ON O.IdObiektu = OB.IdObiektu
);
GO
CREATE OR ALTER FUNCTION uf_Oceny(
)
RETURNS TABLE
AS
RETURN 
(
    SELECT WO.IdWystawionejOceny AS 'Identyfikator oceny', WO.DataWystawienia AS 'Data wystawienia',
	O.Wartosc AS 'Wystawiona ocena', U.Imie AS 'Imie', U.Nazwisko AS 'Nazwisko',
	K.Login AS 'Login', OB.NazwaObiektu AS 'Nazwa obiektu'
	FROM tbl_wystawiona_ocena AS WO
	INNER JOIN tbl_uzytkownik AS U ON WO.IdUzytkownika = U.IdUzytkownik
	LEFT JOIN tbl_konto AS K ON U.IdUzytkownik =K.IdUzytkownika
	INNER JOIN tbl_obiekt AS OB ON WO.IdObiektu = OB.IdObiektu
	LEFT JOIN tbl_ocena AS O ON WO.IdOceny = O.IdOceny
);
GO
CREATE OR ALTER FUNCTION uf_Zgloszenia(
)
RETURNS TABLE
AS
RETURN 
(
    SELECT Z.IdZgloszenia AS 'Numer zg³oszenia', Z.DataZgloszenia AS 'Data zg³oszenia',
	Z.E_mail AS 'E-mail', z.Tresc AS 'Treœæ'
	FROM tbl_zgloszenie as Z
);
GO
CREATE OR ALTER FUNCTION uf_Wydarzenia(
)
RETURNS TABLE
AS
RETURN 
(
    SELECT W.IdWydarzenia AS 'Identyfikator wydarzenia', W.DataWydarzenia AS 'Data wydarzenia',
	W.NazwaWydarzenia AS 'Nazwa',TW.NazwaTypu AS 'Rodzaj wydarzenia',
	O.NazwaObiektu AS 'Nazwa obiektu', OG.Nazwa AS 'Nazwa organizatora',
	OG.E_mail AS 'E-mail organizatora', OG.Telefon AS 'Numer telefonu organizatora'
	FROM tbl_wydarzenie AS W
	INNER JOIN tbl_obiekt AS O ON W.IdObiektu = O.IdObiektu
	INNER JOIN tbl_typ_wydarzenia AS TW ON W.IdTypWydarzenia = TW.IdTypuWydarzenia
	INNER JOIN tbl_organizator AS OG ON  W.IdOrganizatora = OG.IdOrganizatora
);
GO
CREATE OR ALTER FUNCTION uf_WyszukajUzytkowników()
RETURNS TABLE
AS
RETURN
(
    SELECT 
        u.IdUzytkownik AS 'Identyfikator u¿ytkownika', 
        k.Login AS 'Login u¿ytkownika', 
       HASHBYTES('SHA2_256', k.Haslo) AS Haslo_uzytkownika ,
        u.Imie AS 'Imie u¿ytkownika', 
        u.Nazwisko AS 'Nazwisko u¿ytkownika',
        u.Telefon AS 'Telefon u¿ytkownika', 
        u.E_mail AS 'E-mail',
        u.DataUrodzenia AS 'Data urodzenia'
    FROM tbl_konto AS k
    JOIN tbl_uzytkownik AS u ON k.IdUzytkownika = u.IdUzytkownik
    WHERE k.IdTypKonta = 3
);
GO
CREATE OR ALTER FUNCTION uf_WyszukajPracowników()
RETURNS TABLE
AS
RETURN
(
    SELECT K.IdKonta,u.IdUzytkownik AS 'Identyfikator pracownika',
			K.Login AS 'Login pracownika',  HASHBYTES('SHA2_256', K.Haslo) AS 'Has³o pracownika',
			U.Nazwisko AS 'Nazwisko pracownika', U.Imie AS 'Imie pracownika',
			U.Telefon AS 'Telefon pracownika', U.E_mail AS 'E-mail',
			U.DataUrodzenia AS 'Data urodzenia'
    FROM tbl_konto AS k
    JOIN tbl_uzytkownik AS u ON k.IdUzytkownika = u.IdUzytkownik
    WHERE k.IdTypKonta = 2
);
GO
CREATE OR ALTER FUNCTION uf_SzukanieInformacjiPub(
	@IdPub INT
)
RETURNS TABLE
AS
RETURN
(
	SELECT P.IdPubu AS 'pub_id',o.IdObiektu AS 'Identyfikator obiektu',O.NazwaObiektu AS 'Nazwa obiektu',
		O.NazwUlicy AS 'Ulica', O.NumerUlicy AS 'Numer ulicy', P.NumerLokalu AS 'Numer lokalu',
		O.E_mail AS 'Email',O.Opis AS 'Opis',O.SredniaOcena AS 'Œrednia ocena', O.Telefon AS 'Telefon',
		SOB.DlaDzieci AS 'Dla dzieci', SOB.DlaNiepelnosprawnych AS 'Dla niepe³nosprawnych',
		SOB.Ogrodek AS 'Ogródek', SOB.StrefaDzieci AS 'Strefa dla dzieci',
		SOB.StrefaPalacza AS 'Strefa palacza', SOB.WpuszczanieZwierzat AS 'Wpuszczanie zwierz¹t',
		SOB.IdDarmowegoWejscia AS 'IdDniaTygodnia', OS.Imie AS 'Imie', OS.Nazwisko AS 'Nazwisko',
		OS.Stanowisko AS 'Stanowisko'
   FROM tbl_pub AS P
   INNER JOIN tbl_obiekt AS O ON P.IdObiektu = O.IdObiektu
   LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
   INNER JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
   LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia
   WHERE P.IdPubu = @IdPub
);
GO
CREATE OR ALTER FUNCTION uf_ObiektPuby()
RETURNS TABLE
AS
RETURN
(
   SELECT 
       P.IdPubu AS 'pub_id',
       O.IdObiektu AS 'Identyfikator obiektu',
       O.NazwaObiektu AS 'Nazwa obiektu',
       O.NazwUlicy AS 'Ulica',
       O.NumerUlicy AS 'Numer ulicy',
       P.NumerLokalu AS 'Numer lokalu',
       O.E_mail AS 'Email',
       O.Opis AS 'Opis',
       O.SredniaOcena AS 'Œrednia ocena',
       O.Telefon AS 'Telefon',
       SOB.DlaDzieci AS 'Dla dzieci',
       SOB.DlaNiepelnosprawnych AS 'Dla niepe³nosprawnych',
       SOB.Ogrodek AS 'Ogródek',
       SOB.StrefaDzieci AS 'Strefa dla dzieci',
       SOB.StrefaPalacza AS 'Strefa palacza',
       SOB.WpuszczanieZwierzat AS 'Wpuszczanie zwierz¹t',
       dm.NazwaDniaTygodnia AS 'NazwaDnia',
       OS.Imie AS 'Imiê',
       OS.Nazwisko AS 'Nazwisko',
       OS.Stanowisko AS 'Stanowisko'
   FROM 
       tbl_pub AS P
       INNER JOIN tbl_obiekt AS O ON P.IdObiektu = O.IdObiektu
       LEFT JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
       LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
       LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia

);
GO
CREATE or ALTER  FUNCTION uf_HarmonogramObiektu
(
    @IdObiektu INT
)
RETURNS TABLE
AS
RETURN
(
    SELECT 
		ph.IdPozycjiHarmonogramu as ID,
        h.DataUtworzenia as 'Utworzony',
        h.DataWarznosci as 'Waznosc',
        gOtwarcia.Wartosc AS GodzinaOtwarcia,
        gZamkniecia.Wartosc AS GodzinaZamkniecia,
        dt.NazwaDniaTygodnia as 'Nazwa dnia'
    FROM 
        tbl_harmonogram h
    INNER JOIN 
        tbl_pozycja_harmonogramu ph ON h.IdHarmonogramu = ph.IdHarmonogramu
    LEFT JOIN 
        tbl_godzina gOtwarcia ON ph.IdGodzinyOtwarcia = gOtwarcia.IdGodziny
    LEFT JOIN 
        tbl_godzina gZamkniecia ON ph.IdGodzinyZamkniecia = gZamkniecia.IdGodziny
    INNER JOIN 
        tbl_dzien_tygodnia dt ON ph.IdDniaTygodnia = dt.IdDniaTygodnia
    WHERE 
        h.IdObiektu = @IdObiektu
);
GO
CREATE OR ALTER FUNCTION uf_SzczegolHarmonogram(
	@IdPozycji INT
)
RETURNS TABLE
AS
RETURN
(
    SELECT 
			O.IdObiektu AS 'IdObiektu',
			PH.IdPozycjiHarmonogramu AS ID,
			PH.IdGodzinyOtwarcia AS 'GodzinaOtwarcia',
			PH.IdGodzinyZamkniecia AS 'GodzinaZamkniecia',
			H.DataUtworzenia AS 'Utworzony',
			H.DataWarznosci AS 'DataWaznosci'
	FROM tbl_pozycja_harmonogramu AS PH 
	INNER JOIN tbl_harmonogram AS H ON PH.IdHarmonogramu = H.IdHarmonogramu
	INNER JOIN tbl_obiekt AS O ON H.IdObiektu = O.IdObiektu
	WHERE PH.IdPozycjiHarmonogramu = @IdPozycji
);
GO
CREATE OR ALTER FUNCTION uf_ObiektyRestauracje()
RETURNS TABLE
AS
RETURN
(
   SELECT R.IdRestauracji AS 'IdRestauracji', o.IdObiektu AS 'Identyfikator obiektu',O.NazwaObiektu AS 'Nazwa obiektu',
		O.NazwUlicy AS 'Ulica', O.NumerUlicy AS 'Numer ulicy', 
		O.E_mail AS 'Email',O.Opis AS 'Opis',O.SredniaOcena AS 'Œrednia ocena', O.Telefon AS 'Telefon',
		SOB.DlaDzieci AS 'Dla dzieci', SOB.DlaNiepelnosprawnych AS 'Dla niepe³nosprawnych',
		SOB.Ogrodek AS 'Ogródek', SOB.StrefaDzieci AS 'Strefa dla dzieci',
		SOB.StrefaPalacza AS 'Strefa palacza', SOB.WpuszczanieZwierzat AS 'Wpuszczanie zwierz¹t',
		DM.NazwaDniaTygodnia AS 'Darmowe wejœcie', OS.Imie 'Imie',os.Nazwisko AS 'Nazwisko',
		OS.Stanowisko AS 'Stanowisko', TK.NazwaTypu AS 'Kuchnia', R.NumerLokalu AS 'Numer lokalu'
   FROM tbl_restauracja AS R
   INNER JOIN tbl_obiekt AS O ON R.IdObiektu = O.IdObiektu
   INNER JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
   LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
   INNER JOIN tbl_typ_kuchni AS TK ON R.IdTypuKuchni = TK.IdTypuKuchni
   LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia
);
GO
CREATE OR ALTER FUNCTION uf_ObiektyMurale()
RETURNS TABLE
AS
RETURN
(
   SELECT MU.IdMuralu AS  'IdMuralu',o.IdObiektu AS 'Identyfikator obiektu',O.NazwaObiektu AS 'Nazwa obiektu',
		O.NazwUlicy AS 'Ulica', O.NumerUlicy AS 'Numer ulicy', 
		O.E_mail AS 'Email',O.Opis AS 'Opis',O.SredniaOcena AS 'Œrednia ocena', O.Telefon AS 'Telefon',
		SOB.DlaDzieci AS 'Dla dzieci', SOB.DlaNiepelnosprawnych AS 'Dla niepe³nosprawnych',
		SOB.Ogrodek AS 'Ogródek', SOB.StrefaDzieci AS 'Strefa dla dzieci',
		SOB.StrefaPalacza AS 'Strefa palacza', SOB.WpuszczanieZwierzat AS 'Wpuszczanie zwierz¹t',
		DM.NazwaDniaTygodnia AS 'Darmowe wejœcie', OS.Imie 'Imie',os.Nazwisko AS 'Nazwisko',
		OS.Stanowisko AS 'Stanowisko', MU.OpisDotarcia AS 'Opis dotarcia', MU.Historia AS 'Historia'
   FROM tbl_mural AS MU
   INNER JOIN tbl_obiekt AS O ON MU.IdObiektu = O.IdObiektu
   INNER JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
   LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
   LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia
);
GO
CREATE OR ALTER FUNCTION uf_ObiektyMiejsceKulturowe()
RETURNS TABLE
AS
RETURN
(
   SELECT MK.IdMiejscaKulturowego AS 'IdMiejscaKulturowego',o.IdObiektu AS 'Identyfikator obiektu',O.NazwaObiektu AS 'Nazwa obiektu',
		O.NazwUlicy AS 'Ulica', O.NumerUlicy AS 'Numer ulicy', 
		O.E_mail AS 'Email',O.Opis AS 'Opis',O.SredniaOcena AS 'Œrednia ocena', O.Telefon AS 'Telefon',
		SOB.DlaDzieci AS 'Dla dzieci', SOB.DlaNiepelnosprawnych AS 'Dla niepe³nosprawnych',
		SOB.Ogrodek AS 'Ogródek', SOB.StrefaDzieci AS 'Strefa dla dzieci',
		SOB.StrefaPalacza AS 'Strefa palacza', SOB.WpuszczanieZwierzat AS 'Wpuszczanie zwierz¹t',
		DM.NazwaDniaTygodnia AS 'Darmowe wejœcie', OS.Imie 'Imie',os.Nazwisko AS 'Nazwisko',
		OS.Stanowisko AS 'Stanowisko'
   FROM tbl_miejsce_kulturowe AS MK
   INNER JOIN tbl_obiekt AS O ON MK.IdObiektu = O.IdObiektu
   INNER JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
   LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
   LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia
);
GO
CREATE OR ALTER FUNCTION uf_Top5Obiektow()
RETURNS TABLE
AS
RETURN 
(
    SELECT TOP 5 
        O.NazwaObiektu AS 'Nazwa obiektu',
        O.NazwUlicy AS 'Nazwa ulicy',
        O.NumerUlicy AS 'Numer ulicy',
        O.Telefon AS 'Telefon',
        O.E_mail AS 'E-mail',
        O.SredniaOcena AS 'Œrednia ocena',
        O.Opis AS 'Opis',
        TB.NazwaTypu AS 'Rodzaj obiektu',
        Z.NazwaZdjecia
    FROM tbl_obiekt AS O
    LEFT JOIN tbl_typ_obiektu AS TB ON O.IdTypObiektu = TB.IdTypObiektu
    LEFT JOIN tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
    WHERE O.SredniaOcena IS NOT NULL
    ORDER BY O.SredniaOcena DESC
);
GO
CREATE OR ALTER FUNCTION uf_WyszukajUlubione(
    @IdUzytkownika INT
)
RETURNS TABLE
AS
RETURN
(
    SELECT U.IdUlubionych AS 'IdUlubionych', o.NazwaObiektu AS 'Nazwa obiektu', o.Opis,
 u.DataUtworzenia as 'Data utworzenia'
    FROM tbl_ulubione AS u
    INNER JOIN tbl_obiekt AS o ON u.IdObiektu = o.IdObiektu
    WHERE u.IdUzytkownika = @IdUzytkownika
);
GO
CREATE OR ALTER FUNCTION uf_WyszukajOpinie
(
    @IdUzytkownika INT
)
RETURNS TABLE
AS
RETURN 
(
    SELECT O.IdOpini AS 'Numer opini', 
           O.Tresc AS 'Treœæ opini', 
           O.DataWystawienia AS 'Data wystawionej opini',
		   OB.NazwaObiektu AS 'Obiekt'
    FROM tbl_opinia AS O
	LEFT JOIN tbl_obiekt AS OB ON O.IdObiektu=OB.IdObiektu
    WHERE O.IdUzytkownika = @IdUzytkownika

);
GO
CREATE OR ALTER FUNCTION uf_WyszukajOcene(	
	@IdUzytkownik int
)
RETURNS TABLE
AS
RETURN 
(
    SELECT O.IdWystawionejOceny AS 'Numer oceny',OC.Wartosc AS 'Wystawiono ocena',
	O.DataWystawienia AS 'Data wystawionej oceny', OB.NazwaObiektu AS 'Obiekt'
	FROM tbl_wystawiona_ocena AS O
	INNER JOIN tbl_uzytkownik AS U ON O.IdUzytkownika = U.IdUzytkownik
	LEFT JOIN tbl_ocena AS OC ON O.IdOceny = OC.IdOceny
	LEFT JOIN tbl_obiekt AS OB ON O.IdObiektu = OB.IdObiektu
	WHERE O.IdUzytkownika = @IdUzytkownik
);
GO
CREATE OR ALTER FUNCTION uf_SzukanieInformacjiMiejscKulturowych(
	@Miejscakultuowego INT
)
RETURNS TABLE
AS
RETURN
(
	SELECT M.IdMiejscaKulturowego AS 'IdMiejscaKulturowego',o.IdObiektu AS 'Identyfikator obiektu',O.NazwaObiektu AS 'Nazwa obiektu',
		O.NazwUlicy AS 'Ulica', O.NumerUlicy AS 'Numer ulicy', 
		O.E_mail AS 'Email',O.Opis AS 'Opis',O.SredniaOcena AS 'Œrednia ocena', O.Telefon AS 'Telefon',
		SOB.DlaDzieci AS 'Dla dzieci', SOB.DlaNiepelnosprawnych AS 'Dla niepe³nosprawnych',
		SOB.Ogrodek AS 'Ogródek', SOB.StrefaDzieci AS 'Strefa dla dzieci',
		SOB.StrefaPalacza AS 'Strefa palacza', SOB.WpuszczanieZwierzat AS 'Wpuszczanie zwierz¹t',
		SOB.IdDarmowegoWejscia AS 'IdDniaTygodnia', OS.Imie AS 'Imie', OS.Nazwisko AS 'Nazwisko',
		OS.Stanowisko AS 'Stanowisko'
   FROM tbl_miejsce_kulturowe AS M
   INNER JOIN tbl_obiekt AS O ON M.IdObiektu = O.IdObiektu
   LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
   INNER JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
   LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia
   WHERE M.IdMiejscaKulturowego = @Miejscakultuowego
);
GO
CREATE OR ALTER FUNCTION uf_SzukanieInformacjiMural(
	@IdMuralu INT
)
RETURNS TABLE
AS
RETURN
(
	SELECT M.IdMuralu AS 'IdMuralu',o.IdObiektu AS 'Identyfikator obiektu',O.NazwaObiektu AS 'NazwaObiektu',
		O.NazwUlicy AS 'NazwaUlicy', O.NumerUlicy AS 'NumerUlicy', 
		O.E_mail AS 'E_mail',O.Opis AS 'Opis',O.SredniaOcena AS 'SredniaOcena', O.Telefon AS 'Telefon',
		SOB.DlaDzieci AS 'DlaDzieci', SOB.DlaNiepelnosprawnych AS 'DlaNiepelnosprawnych',
		SOB.Ogrodek AS 'Ogrodek', SOB.StrefaDzieci AS 'StrefaDzieci',
		SOB.StrefaPalacza AS 'StrefaPalacza', SOB.WpuszczanieZwierzat AS 'WpuszczanieZwierzat',
		SOB.IdDarmowegoWejscia AS 'IdDarmowegoWejscia', OS.Imie AS 'Imie', OS.Nazwisko AS 'Nazwisko',
		OS.Stanowisko AS 'Stanowisko', M.Historia AS 'Historia',M.OpisDotarcia AS 'OpisDotarcia'
   FROM tbl_mural AS M
   INNER JOIN tbl_obiekt AS O ON M.IdObiektu = O.IdObiektu
   LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
   INNER JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
   LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia
   WHERE M.IdMuralu = @IdMuralu
);
GO
create or alter  FUNCTION uf_SzukanieInformacjiRestauracji(
	@IdRestauracji INT
)
RETURNS TABLE
AS
RETURN
(
	SELECT R.IdRestauracji AS 'IdRestauracji',o.IdObiektu AS 'Identyfikator obiektu',O.NazwaObiektu AS 'NazwaObiektu',
		O.NazwUlicy AS 'NazwaUlicy', O.NumerUlicy AS 'NumerUlicy', R.NumerLokalu AS 'NumerLokalu',TK.NazwaTypu AS 'IdTypuKuchni',
		O.E_mail AS 'E_mail',O.Opis AS 'Opis',O.SredniaOcena AS 'SredniaOcena', O.Telefon AS 'Telefon',
		SOB.DlaDzieci AS 'DlaDzieci', SOB.DlaNiepelnosprawnych AS 'DlaNiepelnosprawnych',
		SOB.Ogrodek AS 'Ogrodek', SOB.StrefaDzieci AS 'StrefaDzieci',
		SOB.StrefaPalacza AS 'StrefaPalacza', SOB.WpuszczanieZwierzat AS 'WpuszczanieZwierzat',
		SOB.IdDarmowegoWejscia AS 'IdDarmowegoWejscia', OS.Imie AS 'Imie', OS.Nazwisko AS 'Nazwisko',
		OS.Stanowisko AS 'Stanowisko'
   FROM tbl_restauracja AS R
   INNER JOIN tbl_obiekt AS O ON R.IdObiektu = O.IdObiektu
   LEFT JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
   INNER JOIN tbl_typ_kuchni AS TK ON R.IdTypuKuchni = TK.IdTypuKuchni
   INNER JOIN tbl_szczegoly_obiektu AS SOB ON O.IdObiektu = SOB.IdObiektu
   LEFT JOIN tbl_dzien_tygodnia AS DM ON SOB.IdDarmowegoWejscia = DM.IdDniaTygodnia
   WHERE R.IdRestauracji = @IdRestauracji
);
GO
CREATE OR ALTER FUNCTION uf_SzukajUzytkownika(
	@IdUzytkownika INT)
RETURNS TABLE
AS
RETURN
(
  SELECT 
           U.DataUrodzenia AS 'DataUrodzenia', U.Imie as 'Imie', U.Nazwisko as 'Nazwisko',
           U.E_mail AS 'E_mail', U.Telefon as 'Telefon',
           k.Login AS 'Login', k.Haslo as 'Has³o'
    FROM tbl_konto AS k
    JOIN tbl_uzytkownik AS U ON k.IdUzytkownika = U.IdUzytkownik
    WHERE k.IdUzytkownika = @IdUzytkownika
	);
GO
