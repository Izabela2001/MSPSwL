USE MULTIWYSZUKIWARKA
GO
CREATE DEFAULT vd_telefon AS '+48 000-000-000';
GO 
EXEC sp_bindefault vd_telefon, 'tbl_uzytkownik.Telefon';
GO
EXEC sp_bindefault vd_telefon, 'tbl_obiekt.Telefon';
GO
EXEC sp_bindefault vd_telefon, 'tbl_organizator.Telefon';
-- Regu³y 
USE MULTIWYSZUKIWARKA
GO
CREATE RULE rl_haslo AS @haslo LIKE '%[!@#$%^&*(),.?":{}|<>]%' AND
    LEN(@haslo) >= 8
    AND @haslo LIKE '%[0-9]%'
    AND @haslo LIKE '%[A-Z]%'
    AND @haslo  LIKE '%[a-z]%';
GO
EXEC sp_bindrule rl_haslo, 'tbl_konto.Haslo';
GO
CREATE RULE rl_Email as @email LIKE '%[A-Z0-9][@][A-Z0-9]%[.][A-Z0-9]%' 
OR @email LIKE NULL;
GO
EXEC sp_bindrule rl_email, 'tbl_uzytkownik.E_mail';
GO
EXEC sp_bindrule rl_email,'tbl_obiekt.E_mail';
GO
EXEC sp_bindrule rl_email,'tbl_zgloszenie.E_mail';
GO
EXEC sp_bindrule rl_email,'tbl_organizator.E_mail';
GO
CREATE RULE rl_telefon AS @telefon 
	LIKE '+48[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]' or
	@telefon  LIKE NULL;
GO
EXEC sp_bindrule rl_telefon, 'tbl_obiekt.Telefon';
GO
EXEC sp_bindrule rl_telefon, 'tbl_uzytkownik.Telefon';
GO
EXEC sp_bindrule rl_telefon, 'tbl_organizator.Telefon';
GO
CREATE RULE rl_nazwa_ulicy AS @nazwa_ulicy
	LIKE '[A-Z][a-z]' AND
	 LEN(@nazwa_ulicy)>=2 or @nazwa_ulicy like null;
GO
EXEC sp_bindrule rl_nazwa_ulicy,'tbl_obiekt.NazwUlicy';
GO
CREATE RULE rl_opis AS LEN(@opis)>=4 or @opis like null;
GO
EXEC sp_bindrule rl_opis, 'tbl_obiekt.Opis';
GO
