<?php
// app/models/UtilisateurModel.php
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
    public function getAllProduit()
    {
        $sql = "SELECT * FROM Produit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function insertionTemporaire($idProduit, $quantite, $idCaisse){
        $sql = "INSERT INTO AchatTemporaire (id_produit, quantite, id_caisse) VALUES (:idProduit, :quantite, :idCaisse)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
        $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);
        $stmt->bindParam(':idCaisse', $idCaisse, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function deleteTemporaire(){
        $sql = 'TRUNCATE TABLE AchatTemporaire';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return true;
    }
    public function deleteTemporaireByProduit($idProduit){
        $sql = 'DELETE FROM AchatTemporaire where id_produit = :idProduit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function insererAchat(){
        $sql = 'SELECT * FROM AchatTemporaire';
        $stmt = $this->db->prepare($sql);
        //recuperer les AchatTemporaires
        $stmt->execute();
        $achatsTemporaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If no temporary purchases, return false
        if (empty($achatsTemporaires)) {
            return false;
        }

        // Get the caisse ID from the first temporary purchase
        $idCaisse = $achatsTemporaires[0]['id_caisse'];

        // Calculate total amount
        $montantTotal = 0;
        foreach ($achatsTemporaires as $achat) {
            // Get product price
            $sqlPrix = "SELECT prix FROM Produit WHERE id = :idProduit";
            $stmtPrix = $this->db->prepare($sqlPrix);
            $stmtPrix->bindParam(':idProduit', $achat['id_produit'], PDO::PARAM_INT);
            $stmtPrix->execute();
            $produit = $stmtPrix->fetch(PDO::FETCH_ASSOC);
            
            $montantTotal += $produit['prix'] * $achat['quantite'];
        }

        // Begin transaction
        $this->db->beginTransaction();

        try {
            // Insert into Achat table
            $sqlAchat = "INSERT INTO Achat (id_caisse, montant_total) VALUES (:idCaisse, :montantTotal)";
            $stmtAchat = $this->db->prepare($sqlAchat);
            $stmtAchat->bindParam(':idCaisse', $idCaisse, PDO::PARAM_INT);
            $stmtAchat->bindParam(':montantTotal', $montantTotal, PDO::PARAM_STR);
            $stmtAchat->execute();
            
            // Get the new Achat ID
            $idAchat = $this->db->lastInsertId();
            
            // Insert each product into Ligne_Achat and update stock
            foreach ($achatsTemporaires as $achat) {
                // Insert into Ligne_Achat
                $sqlLigne = "INSERT INTO Ligne_Achat (id_achat, id_produit, quantite) VALUES (:idAchat, :idProduit, :quantite)";
                $stmtLigne = $this->db->prepare($sqlLigne);
                $stmtLigne->bindParam(':idAchat', $idAchat, PDO::PARAM_INT);
                $stmtLigne->bindParam(':idProduit', $achat['id_produit'], PDO::PARAM_INT);
                $stmtLigne->bindParam(':quantite', $achat['quantite'], PDO::PARAM_INT);
                $stmtLigne->execute();
                
                // Update product stock
                $sqlStock = "UPDATE Produit SET quantite_stock = quantite_stock - :quantite WHERE id = :idProduit";
                $stmtStock = $this->db->prepare($sqlStock);
                $stmtStock->bindParam(':quantite', $achat['quantite'], PDO::PARAM_INT);
                $stmtStock->bindParam(':idProduit', $achat['id_produit'], PDO::PARAM_INT);
                $stmtStock->execute();
            }
            
            // Commit transaction
            $this->db->commit();
            
            // Clear temporary purchases
            $this->deleteTemporaire();
            
            return $idAchat;
            
        } catch (Exception $e) {
            // Rollback in case of error
            $this->db->rollBack();
            return false;
        }
    }
    public function getAllTemporaire(){
        $sql = 'SELECT * FROM AchatTemporaire JOIN Produit ON AchatTemporaire.id_produit = Produit.id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}