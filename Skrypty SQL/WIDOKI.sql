USE MULTIWYSZUKIWARKA
GO
CREATE OR ALTER VIEW VW_Obiekty AS
SELECT 
    O.IdObiektu AS 'Idobiektu',
    O.NazwaObiektu AS 'Nazwa obiektu',
    Z.NazwaZdjecia AS 'NazwaZdjecia',
    O.NazwUlicy AS 'Nazwa ulicy',
    O.NumerUlicy AS 'Numer ulicy',
    O.Telefon AS 'Telefon',
    O.E_mail AS 'E-mail',
    O.Opis AS 'Opis',
	O.SredniaOcena AS 'Œrednia ocena',
    TOB.NazwaTypu AS 'Rodzaj obiektu',
    GOS.Wartosc AS 'Godzina Otwarcia',
    GZ.Wartosc AS 'Godzina zamkniêcia'
FROM 
    tbl_obiekt AS O
INNER JOIN 
    tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
INNER JOIN 
    tbl_typ_obiektu AS TOB ON O.IdTypObiektu = TOB.IdTypObiektu
INNER JOIN 
    tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
LEFT JOIN 
	tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
LEFT JOIN 
    tbl_godzina AS GOS ON PH.IdGodzinyOtwarcia = GOS.IdGodziny
LEFT JOIN 
    tbl_godzina AS GZ ON PH.IdGodzinyZamkniecia = GZ.IdGodziny
	WHERE 
CAST(H.DataWarznosci AS DATE) >= CAST(GETDATE() AS DATE)AND 
PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE());
GO
CREATE OR ALTER VIEW VW_MURALE AS
SELECT 
    Z.NazwaZdjecia as 'NazwaZdjecia',
    O.NazwaObiektu AS 'Nazwa obiektu',
    M.Historia AS 'Historia',
    M.OpisDotarcia AS 'Jak dotrzeæ',
    O.NazwUlicy AS 'Nazwa ulicy',
    O.NumerUlicy AS 'Numer ulicy',
    O.SredniaOcena AS 'Œrednia ocena',
    GOT.Wartosc AS 'Godzina otwarcia',
    GOZ.Wartosc AS 'Godzina zamkniêcia',
    O.IdObiektu AS 'Idobiektu',
    OS.Imie AS 'Imiê autora',
    OS.Nazwisko AS 'Nazwisko autora'
FROM 
    tbl_mural AS M
INNER JOIN 
    tbl_obiekt AS O ON M.IdObiektu = O.IdObiektu 
LEFT JOIN 
    tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
INNER JOIN 
    tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
LEFT JOIN 
	tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
LEFT JOIN
    tbl_godzina AS GOT ON PH.IdGodzinyOtwarcia = GOT.IdGodziny
LEFT JOIN 
    tbl_godzina AS GOZ ON PH.IdGodzinyZamkniecia = GOT.IdGodziny
LEFT JOIN 
    tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
WHERE 
CAST(H.DataWarznosci AS DATE) >= CAST(GETDATE() AS DATE)AND 
PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE());
GO
CREATE OR ALTER VIEW VW_Puby AS
SELECT Z.NazwaZdjecia AS 'NazwaZdjecia',
	O.NazwaObiektu AS 'Nazwa obiektu',
	O.IdObiektu AS 'Idobiektu',
	O.NazwUlicy AS 'Nazwa ulicy',
	O.NumerUlicy AS 'Numer ulicy',
	O.SredniaOcena AS 'Œrednia ocena',
	GOT.Wartosc AS 'Godzina otwarcia',
	GOZ.Wartosc AS 'Godzina zamkniêcia',
	OS.Imie AS 'Imiê',
	OS.Nazwisko AS 'Nazwisko'
FROM tbl_pub AS P
INNER JOIN tbl_obiekt AS O ON P.IdObiektu = O.IdObiektu
LEFT JOIN tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
INNER JOIN 
    tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
LEFT JOIN 
	tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
LEFT JOIN
    tbl_godzina AS GOT ON PH.IdGodzinyOtwarcia = GOT.IdGodziny
LEFT JOIN 
    tbl_godzina AS GOZ ON PH.IdGodzinyZamkniecia = GOT.IdGodziny
INNER JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
WHERE 
CAST(H.DataWarznosci AS DATE) >= CAST(GETDATE() AS DATE)AND 
PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE());
GO
CREATE OR ALTER VIEW VW_Restauracje AS
SELECT Z.NazwaZdjecia AS 'NazwaZdjecia',
	O.NazwaObiektu AS 'Nazwa obiektu',
	O.Opis AS 'Opis',
	R.NumerLokalu AS 'Numer lokalu',
	O.NazwUlicy AS 'Nazwa ulicy',
	O.NumerUlicy AS 'Numer ulicy',
	O.SredniaOcena AS 'Œrednia ocena',
	GOT.Wartosc AS 'Godzina otwarcia',
	GOZ.Wartosc AS 'Godzina zamkniêcia',
	OS.Imie AS 'Imiê',
	OS.Nazwisko AS 'Nazwisko',
	OS.Stanowisko AS 'Stanowisko',
	TK.NazwaTypu AS 'Kuchnia',
	O.Telefon AS 'Telefon',
	O.IdObiektu AS 'Idobiektu'
FROM tbl_restauracja AS R
INNER JOIN tbl_obiekt AS O ON R.IdObiektu = O.IdObiektu
LEFT JOIN tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
INNER JOIN 
    tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
LEFT JOIN 
	tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
LEFT JOIN
    tbl_godzina AS GOT ON PH.IdGodzinyOtwarcia = GOT.IdGodziny
LEFT JOIN 
    tbl_godzina AS GOZ ON PH.IdGodzinyZamkniecia = GOT.IdGodziny
INNER JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
INNER JOIN tbl_typ_kuchni AS TK ON R.IdTypuKuchni = TK.IdTypuKuchni
WHERE 
CAST(H.DataWarznosci AS DATE) >= CAST(GETDATE() AS DATE)AND 
PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE());
GO
CREATE OR ALTER VIEW VW_MiejsceKulturowe AS
SELECT Z.NazwaZdjecia AS 'NazwaZdjecia',
	O.NazwaObiektu AS 'Nazwa obiektu',
	O.IdObiektu AS 'Idobiektu',
	O.NazwUlicy AS 'Nazwa ulicy',
	O.NumerUlicy AS 'Numer ulicy',
	O.SredniaOcena AS 'Œrednia ocena',
	GOT.Wartosc AS 'Godzina otwarcia',
	GOZ.Wartosc AS 'Godzina zamkniêcia',
	O.Opis AS 'Opis',
	OS.Imie AS 'Imie',
	OS.Nazwisko AS 'Nazwisko',
	OS.Stanowisko AS 'Stanowisko',
	TB.NazwaTypu AS 'Typ miejsca'
FROM tbl_miejsce_kulturowe AS M
INNER JOIN tbl_obiekt AS O ON M.IdObiektu = O.IdObiektu
LEFT JOIN tbl_zdjecie AS Z ON O.IdObiektu = Z.IdObiektu
INNER JOIN tbl_osoba AS OS ON O.IdOsoba = OS.IdOsoba
INNER JOIN 
    tbl_harmonogram AS H ON O.IdObiektu = H.IdObiektu
LEFT JOIN 
	tbl_pozycja_harmonogramu AS PH ON H.IdHarmonogramu = PH.IdHarmonogramu
LEFT JOIN
    tbl_godzina AS GOT ON PH.IdGodzinyOtwarcia = GOT.IdGodziny
LEFT JOIN 
    tbl_godzina AS GOZ ON PH.IdGodzinyZamkniecia = GOT.IdGodziny
LEFT JOIN tbl_typ_obiektu AS TB ON O.IdTypObiektu = TB.IdTypObiektu
WHERE 
CAST(H.DataWarznosci AS DATE) >= CAST(GETDATE() AS DATE)AND 
PH.IdDniaTygodnia =  DATEPART(WEEKDAY, GETDATE());

