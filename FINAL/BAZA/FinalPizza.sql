SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `AdresyUzytkownikow` (
  `AdresID` int(11) NOT NULL,
  `UzytkownikID` int(11) NOT NULL,
  `Ulica` varchar(255) NOT NULL,
  `NumerDomu` varchar(50) NOT NULL,
  `NumerMieszkania` varchar(50) DEFAULT NULL,
  `Miasto` varchar(100) NOT NULL,
  `KodPocztowy` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `PizzaSkladniki` (
  `PizzaID` int(11) NOT NULL,
  `SkladnikID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `Pizze` (
  `PizzaID` int(11) NOT NULL,
  `Nazwa` varchar(100) NOT NULL,
  `Opis` text DEFAULT NULL,
  `Cena` decimal(10,2) NOT NULL,
  `Rozmiar` enum('Mala','Srednia','Duza') NOT NULL,
  `ObrazekURL` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `Skladniki` (
  `SkladnikID` int(11) NOT NULL,
  `NazwaSkladnika` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SzczegolyZamowienia` (
  `SzczegolID` int(11) NOT NULL,
  `ZamowienieID` int(11) NOT NULL,
  `PizzaID` int(11) NOT NULL,
  `Ilosc` int(11) NOT NULL,
  `Suma` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `Uzytkownicy` (
  `UzytkownikID` int(11) NOT NULL,
  `NazwaUzytkownika` varchar(100) NOT NULL,
  `Imie` varchar(100) NOT NULL,
  `Nazwisko` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `HasloHash` varchar(255) NOT NULL,
  `DataRejestracji` datetime DEFAULT current_timestamp(),
  `CzyAktywny` tinyint(1) DEFAULT 1,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_zanika_za` datetime DEFAULT NULL,
  `czyAdmin` tinyint(1) NOT NULL,
  `Token_akt` varchar(255) DEFAULT NULL,
  `Token_akt_wyg` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `Zamowienia` (
  `ZamowienieID` int(11) NOT NULL,
  `UzytkownikID` int(11) NOT NULL,
  `KwotaCalkowita` decimal(10,2) NOT NULL,
  `Status` enum('Oczekujace','WRealizacji','Zakonczone','Anulowane') NOT NULL,
  `DataUtworzenia` datetime DEFAULT current_timestamp(),
  `DataAktualizacji` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


ALTER TABLE `AdresyUzytkownikow`
  ADD PRIMARY KEY (`AdresID`),
  ADD KEY `UzytkownikID` (`UzytkownikID`);

ALTER TABLE `PizzaSkladniki`
  ADD PRIMARY KEY (`PizzaID`,`SkladnikID`),
  ADD KEY `SkladnikID` (`SkladnikID`);

ALTER TABLE `Pizze`
  ADD PRIMARY KEY (`PizzaID`);

ALTER TABLE `Skladniki`
  ADD PRIMARY KEY (`SkladnikID`);

ALTER TABLE `SzczegolyZamowienia`
  ADD PRIMARY KEY (`SzczegolID`),
  ADD KEY `ZamowienieID` (`ZamowienieID`),
  ADD KEY `PizzaID` (`PizzaID`);

ALTER TABLE `Uzytkownicy`
  ADD PRIMARY KEY (`UzytkownikID`),
  ADD UNIQUE KEY `NazwaUzytkownika` (`NazwaUzytkownika`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

ALTER TABLE `Zamowienia`
  ADD PRIMARY KEY (`ZamowienieID`),
  ADD KEY `UzytkownikID` (`UzytkownikID`);


ALTER TABLE `AdresyUzytkownikow`
  MODIFY `AdresID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Pizze`
  MODIFY `PizzaID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Skladniki`
  MODIFY `SkladnikID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `SzczegolyZamowienia`
  MODIFY `SzczegolID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Uzytkownicy`
  MODIFY `UzytkownikID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Zamowienia`
  MODIFY `ZamowienieID` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `AdresyUzytkownikow`
  ADD CONSTRAINT `AdresyUzytkownikow_ibfk_1` FOREIGN KEY (`UzytkownikID`) REFERENCES `Uzytkownicy` (`UzytkownikID`) ON DELETE CASCADE;

ALTER TABLE `PizzaSkladniki`
  ADD CONSTRAINT `PizzaSkladniki_ibfk_1` FOREIGN KEY (`PizzaID`) REFERENCES `Pizze` (`PizzaID`),
  ADD CONSTRAINT `PizzaSkladniki_ibfk_2` FOREIGN KEY (`SkladnikID`) REFERENCES `Skladniki` (`SkladnikID`);

ALTER TABLE `SzczegolyZamowienia`
  ADD CONSTRAINT `SzczegolyZamowienia_ibfk_1` FOREIGN KEY (`ZamowienieID`) REFERENCES `Zamowienia` (`ZamowienieID`),
  ADD CONSTRAINT `SzczegolyZamowienia_ibfk_2` FOREIGN KEY (`PizzaID`) REFERENCES `Pizze` (`PizzaID`);

ALTER TABLE `Zamowienia`
  ADD CONSTRAINT `Zamowienia_ibfk_1` FOREIGN KEY (`UzytkownikID`) REFERENCES `Uzytkownicy` (`UzytkownikID`);
COMMIT;