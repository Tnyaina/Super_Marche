-- Create Database
CREATE DATABASE supermarche;

-- Use the database
USE supermarche;

-- Create Produit (Product) Table
CREATE TABLE Produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    designation VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    quantite_stock INT NOT NULL DEFAULT 0,
    code_produit VARCHAR(50) UNIQUE
);

-- Create Caisse (Cash Register) Table
CREATE TABLE Caisse (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_caisse VARCHAR(10) UNIQUE NOT NULL,
    statut ENUM('active', 'fermee') DEFAULT 'active'
);

-- Create Achat (Purchase) Table
CREATE TABLE Achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_caisse INT,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    montant_total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_caisse) REFERENCES Caisse(id)
);

-- Create Ligne_Achat (Purchase Line) Table
CREATE TABLE Ligne_Achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_achat INT,
    id_produit INT,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_achat) REFERENCES Achat(id),
    FOREIGN KEY (id_produit) REFERENCES Produit(id)
);

-- Insert Sample Products (5 products as specified)
INSERT INTO Produit (designation, prix, quantite_stock, code_produit) VALUES
('Pain de mie', 2.50, 100, 'PAIN001'),
('Lait', 1.20, 50, 'LAIT001'),
('Chocolat', 3.00, 75, 'CHOC001'),
('Pommes', 2.00, 200, 'POMME001'),
('Eau minerale', 1.50, 150, 'EAU001');

-- Insert Sample Cash Registers (2 cash registers)
INSERT INTO Caisse (numero_caisse) VALUES 
('Caisse 1'),
('Caisse 2');