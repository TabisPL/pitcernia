CREATE TABLE `Uzytkownicy` (
  `UzytkownikID` INT PRIMARY KEY AUTO_INCREMENT,
  `NazwaUzytkownika` VARCHAR(100) UNIQUE NOT NULL,
  `Imie` VARCHAR(100) NOT NULL,
  `Nazwisko` VARCHAR(100) NOT NULL,
  `Email` VARCHAR(255) UNIQUE NOT NULL,
  `HasloHash` VARCHAR(255) NOT NULL,
  `DataRejestracji` DATETIME DEFAULT (CURRENT_TIMESTAMP),
  `CzyAktywny` BOOLEAN DEFAULT true
);

CREATE TABLE `AdresyUzytkownikow` (
  `AdresID` INT PRIMARY KEY AUTO_INCREMENT,
  `UzytkownikID` INT NOT NULL,
  `Ulica` VARCHAR(255) NOT NULL,
  `NumerDomu` VARCHAR(50) NOT NULL,
  `NumerMieszkania` VARCHAR(50),
  `Miasto` VARCHAR(100) NOT NULL,
  `KodPocztowy` VARCHAR(20) NOT NULL
);

CREATE TABLE `Pizze` (
  `PizzaID` INT PRIMARY KEY AUTO_INCREMENT,
  `Nazwa` VARCHAR(100) NOT NULL,
  `Opis` TEXT,
  `Cena` DECIMAL(10,2) NOT NULL,
  `Rozmiar` ENUM ('Mala', 'Srednia', 'Duza') NOT NULL,
  `ObrazekURL` VARCHAR(255),
  `DataUtworzenia` DATETIME DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `Skladniki` (
  `SkladnikID` INT PRIMARY KEY AUTO_INCREMENT,
  `Nazwa` VARCHAR(100) NOT NULL
);

CREATE TABLE `PizzaSkladniki` (
  `PizzaID` INT NOT NULL,
  `SkladnikID` INT NOT NULL,
  PRIMARY KEY (`PizzaID`, `SkladnikID`)
);

CREATE TABLE `Zamowienia` (
  `ZamowienieID` INT PRIMARY KEY AUTO_INCREMENT,
  `UzytkownikID` INT NOT NULL,
  `KwotaCalkowita` DECIMAL(10,2) NOT NULL,
  `Status` ENUM ('Oczekujace', 'WRealizacji', 'Zakonczone', 'Anulowane') NOT NULL,
  `DataUtworzenia` DATETIME DEFAULT (CURRENT_TIMESTAMP),
  `DataAktualizacji` DATETIME DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `SzczegolyZamowienia` (
  `SzczegolID` INT PRIMARY KEY AUTO_INCREMENT,
  `ZamowienieID` INT NOT NULL,
  `PizzaID` INT NOT NULL,
  `Ilosc` INT NOT NULL,
  `Suma` DECIMAL(10,2) NOT NULL
);

ALTER TABLE `AdresyUzytkownikow` ADD FOREIGN KEY (`UzytkownikID`) REFERENCES `Uzytkownicy` (`UzytkownikID`) ON DELETE CASCADE;

ALTER TABLE `PizzaSkladniki` ADD FOREIGN KEY (`PizzaID`) REFERENCES `Pizze` (`PizzaID`);

ALTER TABLE `PizzaSkladniki` ADD FOREIGN KEY (`SkladnikID`) REFERENCES `Skladniki` (`SkladnikID`);

ALTER TABLE `Zamowienia` ADD FOREIGN KEY (`UzytkownikID`) REFERENCES `Uzytkownicy` (`UzytkownikID`);

ALTER TABLE `SzczegolyZamowienia` ADD FOREIGN KEY (`ZamowienieID`) REFERENCES `Zamowienia` (`ZamowienieID`);

ALTER TABLE `SzczegolyZamowienia` ADD FOREIGN KEY (`PizzaID`) REFERENCES `Pizze` (`PizzaID`);
