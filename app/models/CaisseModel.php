<?php
// app/models/CaisseModel.php
namespace app\models;
use PDO;
use Exception;

class CaisseModel
{
    private $db;

    public function __construct(PDO $database)
    {
        $this->db = $database;
    }

    public function getAllCaisses()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM Caisse");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des caisses: " . $e->getMessage());
        }
    }

    public function getCaisseById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Caisse WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération de la caisse: " . $e->getMessage());
        }
    }

    public function createCaisse($numero_caisse)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO Caisse (numero_caisse) VALUES (:numero_caisse)");
            $stmt->execute(['numero_caisse' => $numero_caisse]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création de la caisse: " . $e->getMessage());
        }
    }

    public function updateCaisseStatus($id, $statut)
    {
        try {
            $stmt = $this->db->prepare("UPDATE Caisse SET statut = :statut WHERE id = :id");
            $stmt->execute([
                'id' => $id,
                'statut' => $statut
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour du statut de la caisse: " . $e->getMessage());
        }
    }

    public function deleteCaisse($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM Caisse WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression de la caisse: " . $e->getMessage());
        }
    }

    public function getAchatsParCaisse($id_caisse)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, COUNT(la.id) as nombre_articles 
                FROM Achat a 
                LEFT JOIN Ligne_Achat la ON a.id = la.id_achat 
                WHERE a.id_caisse = :id_caisse 
                GROUP BY a.id
                ORDER BY a.date_achat DESC
            ");
            $stmt->execute(['id_caisse' => $id_caisse]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des achats: " . $e->getMessage());
        }
    }

    public function getCaisseActiveStatus($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT statut FROM Caisse WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['statut'] === 'active' : false;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la vérification du statut de la caisse: " . $e->getMessage());
        }
    }

    public function numeroCaisseExists($numero_caisse)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM Caisse WHERE numero_caisse = :numero_caisse");
            $stmt->execute(['numero_caisse' => $numero_caisse]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la vérification du numéro de caisse: " . $e->getMessage());
        }
    }
}