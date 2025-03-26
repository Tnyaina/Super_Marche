<?php

namespace app\controllers;

use app\models\CaisseModel;
use Flight;

class CaisseController {
    private $CaisseModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['client_id'])) {
            $_SESSION['caisse_error'] = "Veuillez vous connecter d'abord";
            Flight::redirect('/');
        }
        $this->CaisseModel = new CaisseModel(Flight::db());
    }

    public function index() {
        $data = [
            'caisses' => $this->CaisseModel->getAllCaisses(),
            'success' => $_SESSION['caisse_success'] ?? null,
            'error' => $_SESSION['caisse_error'] ?? null
        ];

        unset($_SESSION['caisse_success']);
        unset($_SESSION['caisse_error']);

        Flight::render('caisse/selection.php', $data, 'pageContent');
        
        Flight::render('template-html-homepage/index.php');
    }
}