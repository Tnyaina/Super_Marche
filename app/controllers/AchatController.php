<?php
// app/controllers/UserController.php
namespace app\controllers;

use app\models\AchatModel;
use Flight;

class AchatController
{
    private $db;
    private $AchatModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Flight::db();
        $this->AchatModel = new AchatModel($db);
    }
    public function afficherListe(){
        $idProduit = Flight::request()->query->idProduit ?? null;
        $quantite = Flight::request()->query->quantite ?? null;
        $idCaisse = Flight::request()->query->caisse_selectionnee ?? null;

        $produits = $this->AchatModel->getAllProduit();
        if ($idProduit !== null || $quantite !== null) {
            $this->AchatModel->insertionTemporaire($idProduit, $quantite, $idCaisse);
        }
        $listeProduitsValide = $this->AchatModel->getAllTemporaire() ?? null;
        $data = [
            'produits' => $produits,
            'listeProduitsValide' => $listeProduitsValide,
            'page' => 'achat/achat'  // Modified path - using relative path from index.php location
        ];
        Flight::render('index.php', $data);
    }
}