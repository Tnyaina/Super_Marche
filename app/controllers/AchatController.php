<?php
// app/controllers/AchatController.php
namespace app\controllers;

use app\models\AchatModel;
use app\models\CaisseModel;
use Flight;

class AchatController
{
    private $AchatModel;
    private $CaisseModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Flight::db();
        $this->AchatModel = new AchatModel($db);
        $this->CaisseModel = new CaisseModel($db);
    }

    public function afficherListe()
    {
        $idCaisse = Flight::request()->query->caisse_id ?? null;

        // Set caisse ID in session if not already set
        if ($_SESSION['idCaisse'] === null && $idCaisse) {
            $_SESSION['idCaisse'] = $idCaisse;
        }

        // Get caisse number if we have an ID
        if ($idCaisse) {
            $nomCaisse = $this->CaisseModel->getCaisseById($idCaisse);
            if (is_array($nomCaisse) && isset($nomCaisse['numero_caisse'])) {
                $_SESSION['numCaisse'] = $nomCaisse['numero_caisse'];
            }
        }

        // Get all products for the selection dropdown
        $produits = $this->AchatModel->getAllProduit();

        $data = [
            'produits' => $produits,
            'idCaisse' => $_SESSION['idCaisse'],
            'numCaisse' => $_SESSION['numCaisse'],
            'page' => 'achat/achat'
        ];

        Flight::render('index.php', $data);
    }

    /**
     * Finalize a purchase and save it to the database
     */
    public function finaliserAchat()
    {
        try {
            $requestData = json_decode(Flight::request()->getBody(), true);
            $purchases = $requestData['purchases'] ?? [];
            $idCaisse = $_SESSION['idCaisse'] ?? null;

            error_log("finaliserAchat: Received data: " . json_encode($requestData));
            error_log("finaliserAchat: idCaisse = " . ($idCaisse ?? 'null')); // Debug idCaisse

            if (empty($purchases) || !$idCaisse) {
                error_log("finaliserAchat: No purchases or caisse ID provided");
                Flight::json(['success' => false, 'message' => 'No purchases or caisse ID provided'], 400);
                return;
            }

            $result = $this->AchatModel->insererAchat($idCaisse, $purchases);

            if ($result) {
                error_log("finaliserAchat: Purchase completed successfully, ID: $result");
                Flight::json(['success' => true, 'achat_id' => $result, 'message' => 'Purchase completed successfully']);
            } else {
                error_log("finaliserAchat: Failed to process purchase");
                Flight::json(['success' => false, 'message' => 'Failed to process purchase - check product and caisse availability'], 400);
            }
        } catch (Exception $e) {
            error_log("finaliserAchat error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            Flight::json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get purchase statistics (for Travaux à faire 5)
     */
    public function getStatistiques()
    {
        // Total sales amount
        $totalSalesSql = "SELECT SUM(montant_total) as total FROM Achat";
        $totalSalesStmt = Flight::db()->query($totalSalesSql);
        $totalSales = $totalSalesStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Sales by product
        $salesByProductSql = "
            SELECT 
                p.designation,
                SUM(la.quantite) as quantite_totale,
                SUM(la.quantite * p.prix) as montant_total
            FROM Ligne_Achat la
            JOIN Produit p ON la.id_produit = p.id
            GROUP BY p.id, p.designation";
        $salesByProductStmt = Flight::db()->query($salesByProductSql);
        $salesByProduct = $salesByProductStmt->fetchAll(PDO::FETCH_ASSOC);

        // Sales by day
        $salesByDaySql = "
            SELECT 
                DATE(date_achat) as jour,
                SUM(montant_total) as montant_total
            FROM Achat
            GROUP BY DATE(date_achat)
            ORDER BY jour DESC";
        $salesByDayStmt = Flight::db()->query($salesByDaySql);
        $salesByDay = $salesByDayStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'total_sales' => $totalSales,
            'sales_by_product' => $salesByProduct,
            'sales_by_day' => $salesByDay,
            'page' => 'achat/statistiques'
        ];

        Flight::render('index.php', $data);
    }
}