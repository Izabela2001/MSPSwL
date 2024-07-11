USE MULTIWYSZUKIWARKA
GO
CREATE TABLE tbl_typ_konta(
	IdTypKonta TINYINT NOT NULL IDENTITY(1,1),
	NazwaTypu VARCHAR(15) NOT NULL,
	CONSTRAINT PK_TypKonta PRIMARY KEY (IdTypKonta));
GO
CREATE TABLE tbl_ocena(
	IdOceny TINYINT NOT NULL IDENTITY(1,1),
	Wartosc TINYINT NOT NULL, 
	CONSTRAINT PK_IdOceny PRIMARY KEY(IdOceny));
GO
CREATE TABLE tbl_typ_wydarzenia(
	IdTypuWydarzenia TINYINT NOT NULL IDENTITY(1,1),
	NazwaTypu VARCHAR(15) NOT NULL,
	CONSTRAINT PK_TypWydarzenia PRIMARY KEY (IdTypuWydarzenia));
GO
CREATE TABLE tbl_typ_kuchni(
	IdTypuKuchni TINYINT NOT NULL IDENTITY(1,1),
	NazwaTypu VARCHAR(30) NOT NULL,
	CONSTRAINT PK_TypKuchni PRIMARY KEY (IdTypuKuchni));
GO
CREATE TABLE tbl_typ_obiektu(
	IdTypObiektu TINYINT NOT NULL IDENTITY(1,1),
	NazwaTypu VARCHAR(30) NOT NULL,
	CONSTRAINT PK_TypObiektu PRIMARY KEY (IdTypObiektu));
GO
CREATE TABLE tbl_uzytkownik(
	IdUzytkownik INT NOT NULL IDENTITY(1,1),
	Imie VARCHAR(25) NOT NULL,
	Nazwisko VARCHAR(25) NOT NULL,
	E_mail VARCHAR(50) NOT NULL,
	Telefon CHAR(15) NOT NULL,
	DataUrodzenia DATE NOT NULL,
	CONSTRAINT PK_Uzytkownik PRIMARY KEY (IdUzytkownik));
GO
CREATE TABLE tbl_konto(
	IdKonta INT NOT NULL IDENTITY(1,1),
	Login VARCHAR(15) NOT NULL,
	Haslo VARCHAR(20) NOT NULL,
	IdTypKonta TINYINT NOT NULL, 
	IdUzytkownika INT NOT NULL, 
	CONSTRAINT PK_Konto PRIMARY KEY (IdKonta),
	CONSTRAINT FK_TypKonta FOREIGN KEY (IdTypKonta) REFERENCES tbl_typ_konta(IdTypKonta),
	CONSTRAINT FK_UzytkownikaKonto FOREIGN KEY (IdUzytkownika) REFERENCES tbl_uzytkownik(IdUzytkownik));
GO
CREATE TABLE tbl_osoba (
	IdOsoba INT NOT NULL IDENTITY(1,1),
	Imie VARCHAR(25) NOT NULL,
	Nazwisko VARCHAR(25) NOT NULL,
	Stanowisko VARCHAR(35) NOT NULL,
	CONSTRAINT PK_Osoba PRIMARY KEY(IdOsoba));
GO
CREATE TABLE tbl_obiekt(
	IdObiektu INT NOT NULL IDENTITY(1,1),
	NazwaObiektu VARCHAR(30) NOT NULL,
	NazwUlicy VARCHAR(30) NOT NULL,
	NumerUlicy VARCHAR(15) NOT NULL,
	Telefon CHAR(15)  NULL,
	E_mail VARCHAR(30) NULL,
	SredniaOcena FLOAT NULL,
	Opis VARCHAR(500)  NULL,
	IdTypObiektu TINYINT NOT NULL,
	IdOsoba INT NOT NULL,
	CONSTRAINT PK_Obiekty PRIMARY KEY (IdObiektu),
	CONSTRAINT FK_TypObiektu FOREIGN KEY (IdTypObiektu) REFERENCES tbl_typ_obiektu(IdTypObiektu),
	CONSTRAINT FK_Osoba FOREIGN KEY (IdOsoba) REFERENCES tbl_osoba(IdOsoba));
GO
CREATE TABLE tbl_godzina(
	IdGodziny TINYINT NOT NULL IDENTITY(1,1),
	Wartosc VARCHAR(5) NOT NULL,
	CONSTRAINT PK_Godzina PRIMARY KEY (IdGodziny));
GO
CREATE TABLE tbl_dzien_tygodnia(
	IdDniaTygodnia TINYINT NOT NULL IDENTITY(1,1),
	NazwaDniaTygodnia VARCHAR(12) NOT NULL,
	CONSTRAINT PK_DzienTygodnia PRIMARY KEY(IdDniaTygodnia));
GO
CREATE TABLE tbl_harmonogram (
    IdHarmonogramu INT NOT NULL IDENTITY(1,1),
    IdObiektu INT NOT NULL,
    DataUtworzenia DATE NOT NULL,
    DataWarznosci DATE NOT NULL,
    CONSTRAINT PK_Harmonogram PRIMARY KEY (IdHarmonogramu),
    CONSTRAINT FK_HarmonogramObiekt FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu)
);
GO
CREATE TABLE tbl_pozycja_harmonogramu (
    IdPozycjiHarmonogramu INT NOT NULL IDENTITY(1,1),
    IdHarmonogramu INT NOT NULL,
    IdGodzinyOtwarcia TINYINT  NULL,
    IdGodzinyZamkniecia TINYINT  NULL,
    IdDniaTygodnia TINYINT  NULL,
    CONSTRAINT PK_PozycjeHarmonogramu PRIMARY KEY (IdPozycjiHarmonogramu),
    CONSTRAINT FK_PozycjeHarmonogramuHarmonogram FOREIGN KEY (IdHarmonogramu) REFERENCES tbl_harmonogram(IdHarmonogramu),
    CONSTRAINT FK_PozycjeHarmonogramuGodzinaOtwarcia FOREIGN KEY (IdGodzinyOtwarcia) REFERENCES tbl_godzina(IdGodziny),
    CONSTRAINT FK_PozycjeHarmonogramuGodzinaZamkniecia FOREIGN KEY (IdGodzinyZamkniecia) REFERENCES tbl_godzina(IdGodziny),
    CONSTRAINT FK_PozycjeHarmonogramuDzienTygodnia FOREIGN KEY (IdDniaTygodnia) REFERENCES tbl_dzien_tygodnia(IdDniaTygodnia)
);
GO
CREATE TABLE tbl_pub(
	IdPubu INT NOT NULL IDENTITY(1,1),
	NumerLokalu INT NULL, 
	IdObiektu INT NOT NULL,
	CONSTRAINT PK_Pub PRIMARY KEY(IdPubu),
	CONSTRAINT FK_ObiektuPub FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu));
GO
CREATE TABLE tbl_restauracja(
	IdRestauracji INT NOT NULL IDENTITY(1,1),
	NumerLokalu INT NULL,
	IdTypuKuchni TINYINT NOT NULL,
	IdObiektu INT NOT NULL,
	CONSTRAINT PK_Restauracja PRIMARY KEY(IdRestauracji),
	CONSTRAINT FK_TypKuchni FOREIGN KEY (IdTypuKuchni) REFERENCES tbl_typ_kuchni(IdTypuKuchni),
	CONSTRAINT FK_ObiektRestuaracja FOREIGN KEY(IdObiektu) REFERENCES tbl_obiekt(IdObiektu));
GO
CREATE TABLE tbl_mural(
	IdMuralu INT NOT NULL IDENTITY(1,1),
	Historia VARCHAR(300) NULL,
	IdObiektu INT NOT NULL,
	OpisDotarcia VARCHAR(500) NULL,
	CONSTRAINT PK_Mural PRIMARY KEY (IdMuralu),
	CONSTRAINT FK_ObiektuMuralu FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu));
GO
CREATE TABLE tbl_miejsce_kulturowe(	
	IdMiejscaKulturowego INT NOT NULL IDENTITY(1,1),
	IdObiektu INT NOT NULL,
	CONSTRAINT PK_MiejscaKulturowego PRIMARY KEY(IdMiejscaKulturowego),
	CONSTRAINT FK_ObiektuMiejsca FOREIGN KEY(IdObiektu) REFERENCES tbl_obiekt(IdObiektu));
GO
CREATE TABLE tbl_szczegoly_obiektu(
	IdSzczegoluObiektu INT NOT NULL IDENTITY(1,1),
	Ogrodek BIT NULL,
	StrefaPalacza BIT NULL,
	StrefaDzieci BIT NULL,
	WpuszczanieZwierzat BIT NULL,
	DlaNiepelnosprawnych BIT NULL,
	DlaDzieci BIT NULL,
	IdDarmowegoWejscia TINYINT NULL,
	IdObiektu INT NOT NULL,
	CONSTRAINT PK_Szczegoly PRIMARY KEY (IdSzczegoluObiektu),
	CONSTRAINT FK_Obiektu FOREIGN KEY (IdObiektu)REFERENCES tbl_obiekt(IdObiektu),
	CONSTRAINT FK_DarmoweWejscie FOREIGN KEY (IdDarmowegoWejscia)REFERENCES tbl_dzien_tygodnia(IdDniaTygodnia));
GO
CREATE TABLE tbl_ulubione(
	IdUlubionych INT NOT NULL IDENTITY(1,1),
	IdUzytkownika INT NOT NULL, 
	IdObiektu INT NOT NULL,
	DataUtworzenia DATE NOT NULL,
	CONSTRAINT PK_Ulubionych PRIMARY KEY (IdUlubionych),
	CONSTRAINT FK_UzytkownikUlubione FOREIGN KEY (IdUzytkownika) REFERENCES tbl_uzytkownik(IdUzytkownik),
	CONSTRAINT FK_ObiektuUlubione FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu));
GO
CREATE TABLE tbl_wystawiona_ocena(
	IdWystawionejOceny INT NOT NULL IDENTITY(1,1),
	IdOceny TINYINT NOT NULL,
	IdObiektu INT NOT NULL,
	IdUzytkownika INT NOT NULL,
	DataWystawienia DATE NOT NULL,
	CONSTRAINT PK_WystawionejOcecny PRIMARY KEY (IdWystawionejOceny),
	CONSTRAINT FK_WystawionaOcenaObiekt FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu),
	CONSTRAINT FK_WsystawionaOcenaOcena FOREIGN KEY (IdOceny) REFERENCES tbl_ocena(IdOceny),
	CONSTRAINT FK_WystawioneOcenyUzytk FOREIGN KEY (IdUzytkownika) REFERENCES tbl_uzytkownik(IdUzytkownik));
GO
CREATE TABLE tbl_opinia(
	IdOpini INT NOT NULL IDENTITY(1,1),
	Tresc VARCHAR(200) NOT NULL,
	IdUzytkownika INT NOT NULL, 
	IdObiektu INT NOT NULL,
	DataWystawienia DATE NOT NULL,
	CONSTRAINT PK_Opinia PRIMARY KEY (IdOpini),
	CONSTRAINT FK_OpiniaUzytkownika FOREIGN KEY (IdUzytkownika) REFERENCES tbl_uzytkownik(IdUzytkownik),
	CONSTRAINT FK_OpiniObiektu FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu));
GO
CREATE TABLE tbl_organizator(
	IdOrganizatora INT NOT NULL IDENTITY(1,1),
	Nazwa VARCHAR(100) NOT NULL,
	Nip VARCHAR(13) NOT NULL,
	IdOsoby INT NOT NULL,
	Telefon CHAR(15) NOT NULL,
	E_mail VARCHAR(50) NOT NULL,
	CONSTRAINT PK_Organizatora PRIMARY KEY(IdOrganizatora),
	CONSTRAINT FK_OrganizatorOsoba FOREIGN KEY (IdOsoby) REFERENCES tbl_osoba(IdOsoba));
GO
CREATE TABLE tbl_wydarzenie(
	IdWydarzenia INT NOT NULL IDENTITY(1,1),
	DataWydarzenia DATE NOT NULL,
	IdTypWydarzenia TINYINT NOT NULL,
	NazwaWydarzenia VARCHAR(50) NOT NULL,
	IdOrganizatora INT NOT NULL,
	IdObiektu INT NOT NULL,
	Informacje VARCHAR(300) NOT NULL,
	CONSTRAINT PK_Wydarzenia PRIMARY KEY (IdWydarzenia),
	CONSTRAINT FK_WydarzenieOrganizator FOREIGN KEY (IdOrganizatora) REFERENCES tbl_organizator(IdOrganizatora),
	CONSTRAINT FK_WydarzenieObiektu FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu),
	CONSTRAINT FK_WydarzenieTypw FOREIGN KEY (IdTypWydarzenia) REFERENCES tbl_typ_wydarzenia(IdTypuWydarzenia));
GO
CREATE TABLE tbl_zgloszenie(
	IdZgloszenia INT NOT NULL IDENTITY(1,1),
	Tresc VARCHAR(500) NOT NULL,
	E_mail VARCHAR(50) NOT NULL,
	DataZgloszenia DATE NOT NULL,
	CONSTRAINT PK_IdZgloszenia PRIMARY KEY(IdZgloszenia));
GO 
CREATE TABLE tbl_zdjecie(
	IdZdjecia INT NOT NULL IDENTITY(1,1),
	NazwaZdjecia VARCHAR(50) NOT NULL,
	IdObiektu INT NOT NULL,
	CONSTRAINT PK_IdZdjecia PRIMARY KEY(IdZdjecia),
	CONSTRAINT FK_ObiektZdjecia FOREIGN KEY (IdObiektu) REFERENCES tbl_obiekt(IdObiektu));
