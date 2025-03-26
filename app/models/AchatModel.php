<?php
// app/models/AchatModel.php
namespace app\models;

use PDO;
use Exception;

class AchatModel
{
    private $db;

    public function __construct(PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Get all products
     */
    public function getAllProduit()
    {
        $sql = "SELECT * FROM Produit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new purchase
     */
    public function insererAchat($idCaisse, $purchases)
{
    if (empty($purchases)) {
        error_log("insererAchat: No purchases provided");
        return false;
    }

    // Validate id_caisse
    $sqlCaisseCheck = "SELECT id FROM Caisse WHERE id = ?";
    $stmtCaisseCheck = $this->db->prepare($sqlCaisseCheck);
    $stmtCaisseCheck->execute([$idCaisse]);
    if (!$stmtCaisseCheck->fetch(PDO::FETCH_ASSOC)) {
        error_log("insererAchat: Caisse not found - ID: " . $idCaisse);
        throw new Exception("Caisse not found with ID: " . $idCaisse);
    }

    $montantTotal = 0;
    foreach ($purchases as $achat) {
        $sqlPrix = "SELECT prix FROM Produit WHERE id = ?";
        $stmtPrix = $this->db->prepare($sqlPrix);
        $stmtPrix->execute([$achat['id_produit']]);
        $produit = $stmtPrix->fetch(PDO::FETCH_ASSOC);

        if (!$produit) {
            error_log("insererAchat: Product not found - ID: " . $achat['id_produit']);
            throw new Exception("Product not found with ID: " . $achat['id_produit']);
        }

        $montantTotal += $produit['prix'] * $achat['quantite'];
    }

    $this->db->beginTransaction();

    try {
        $sqlAchat = "INSERT INTO Achat (id_caisse, montant_total) VALUES (?, ?)";
        $stmtAchat = $this->db->prepare($sqlAchat);
        $stmtAchat->execute([$idCaisse, $montantTotal]);

        $idAchat = $this->db->lastInsertId();

        foreach ($purchases as $achat) {
            $sqlStockCheck = "SELECT quantite_stock FROM Produit WHERE id = ?";
            $stmtStockCheck = $this->db->prepare($sqlStockCheck);
            $stmtStockCheck->execute([$achat['id_produit']]);
            $stock = $stmtStockCheck->fetch(PDO::FETCH_ASSOC)['quantite_stock'];

            if ($stock < $achat['quantite']) {
                $this->db->rollBack();
                error_log("insererAchat: Insufficient stock for product ID: " . $achat['id_produit']);
                throw new Exception("Insufficient stock for product ID: " . $achat['id_produit']);
            }

            $sqlLigne = "INSERT INTO Ligne_Achat (id_achat, id_produit, quantite) VALUES (?, ?, ?)";
            $stmtLigne = $this->db->prepare($sqlLigne);
            $stmtLigne->execute([$idAchat, $achat['id_produit'], $achat['quantite']]);

            $sqlStock = "UPDATE Produit SET quantite_stock = quantite_stock - ? WHERE id = ?";
            $stmtStock = $this->db->prepare($sqlStock);
            $stmtStock->execute([$achat['quantite'], $achat['id_produit']]);
        }

        $this->db->commit();
        error_log("insererAchat: Transaction committed, ID: $idAchat");
        return $idAchat;

    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("insererAchat error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
        return false;
    }
}

    /**
     * Get total sales amount
     */
    public function getTotalSales()
    {
        try {
            $sql = "SELECT SUM(montant_total) as total FROM Achat";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("getTotalSales error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get sales by product
     */
    public function getSalesByProduct()
    {
        try {
            $sql = "
                SELECT 
                    p.designation,
                    SUM(la.quantite) as quantite_totale,
                    SUM(la.quantite * p.prix) as montant_total
                FROM Ligne_Achat la
                JOIN Produit p ON la.id_produit = p.id
                GROUP BY p.id, p.designation";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("getSalesByProduct error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get sales by day
     */
    public function getSalesByDay()
    {
        try {
            $sql = "
                SELECT 
                    DATE(date_achat) as jour,
                    SUM(montant_total) as montant_total
                FROM Achat
                GROUP BY DATE(date_achat)
                ORDER BY jour DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("getSalesByDay error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get purchases by caisse
     */
    public function getPurchasesByCaisse($idCaisse)
    {
        try {
            $sql = "
                SELECT a.*, COUNT(la.id) as nombre_articles 
                FROM Achat a 
                LEFT JOIN Ligne_Achat la ON a.id = la.id_achat 
                WHERE a.id_caisse = :idCaisse 
                GROUP BY a.id
                ORDER BY a.date_achat DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idCaisse', $idCaisse, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("getPurchasesByCaisse error: " . $e->getMessage());
            return [];
        }
    }
}