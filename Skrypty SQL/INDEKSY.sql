USE MULTIWYSZUKIWARKA
GO
CREATE UNIQUE INDEX idx_TypKonta ON tbl_typ_konta(NazwaTypu);
GO
CREATE UNIQUE INDEX idx_Ocena ON tbl_ocena(Wartosc);
GO
CREATE UNIQUE INDEX idx_Godzina ON tbl_godzina(Wartosc);
GO
CREATE UNIQUE INDEX idx_DzienTygodnia ON tbl_dzien_tygodnia(NazwaDniaTygodnia);
GO
CREATE UNIQUE INDEX idx_TypWydarzenia ON tbl_typ_wydarzenia(NazwaTypu);
GO
CREATE UNIQUE INDEX idx_TypKuchni ON tbl_typ_kuchni(NazwaTypu);
GO
CREATE UNIQUE INDEX idx_TypObiektu ON tbl_typ_obiektu(NazwaTypu);
GO
CREATE UNIQUE INDEX idx_Login ON tbl_konto(Login);
GO
CREATE INDEX idx_DaneUzytkownika ON tbl_uzytkownik(Imie,Nazwisko);
GO
CREATE UNIQUE INDEX idx_E_mail ON tbl_uzytkownik(E_mail);
GO
CREATE INDEX idx_DataUrodzenia ON tbl_uzytkownik(DataUrodzenia);
GO
CREATE INDEX idx_NazwaUlicy ON tbl_obiekt(NazwUlicy);
GO
CREATE INDEX idx_SredniaOcena ON tbl_obiekt(SredniaOcena);
GO
CREATE INDEX idx_NazwaObiektu ON tbl_obiekt(NazwaObiektu);
GO
CREATE INDEX idx_DaneOsoby ON tbl_osoba(Imie,Nazwisko);
GO
CREATE INDEX idx_Stanowisko ON tbl_osoba(Stanowisko);
GO
CREATE INDEX idx_DataWystawieniaOcena ON tbl_wystawiona_ocena(DataWystawienia) ;
GO
CREATE INDEX idx_DataWystawieniaOpinia ON tbl_opinia(DataWystawienia); 
GO 
CREATE INDEX idx_NazwaOrganizatora ON tbl_organizator(Nazwa);
GO
CREATE UNIQUE INDEX idx_Nip ON tbl_organizator(Nip);
GO
CREATE INDEX idx_DataWydarzenia ON tbl_wydarzenie(DataWydarzenia);
