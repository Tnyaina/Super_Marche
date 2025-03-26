<?php
namespace App\Models;
use PDO;
use PDOException;
class ClientModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($nom_utilisateur) {
        try {
            // Vérifier si le client existe déjà
            $stmt = $this->db->prepare("SELECT id FROM Clients WHERE nom = ?");
            $stmt->execute([$nom_utilisateur]);
            
            if ($stmt->rowCount() > 0) {
                return ['error' => 'Ce nom d\'utilisateur existe déjà'];
            }

            // Insérer le nouveau client
            $stmt = $this->db->prepare("INSERT INTO Clients (nom) VALUES (?)");
            $stmt->execute([$nom_utilisateur]);

            return ['success' => 'Inscription réussie'];
        } catch (PDOException $e) {
            return ['error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()];
        }
    }

    public function login($nom_utilisateur) {
        try {
            $stmt = $this->db->prepare("SELECT id, nom FROM Clients WHERE nom = ?");
            $stmt->execute([$nom_utilisateur]);
            
            if ($stmt->rowCount() > 0) {
                $client = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['client_id'] = $client['id'];
                $_SESSION['client_nom'] = $client['nom'];
                return ['success' => true];
            }

            return ['error' => 'Client non trouvé'];
        } catch (PDOException $e) {
            return ['error' => 'Erreur lors de la connexion: ' . $e->getMessage()];
        }
    }

    public function getClientById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Clients WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
}