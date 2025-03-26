<?php

namespace app\controllers;
use app\models\ClientModel;
use Flight;

class ClientController {
    private $clientModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->clientModel = new ClientModel(Flight::db());
    }

    public function showRegister() {
        $data = [
            'success' => $_SESSION['register_success'] ?? null,
            'error' => $_SESSION['register_error'] ?? null
        ];

        unset($_SESSION['register_success']);
        unset($_SESSION['register_error']);

        Flight::render('register.php', $data);
    }

    public function register() {
        $nom_utilisateur = Flight::request()->data->nom_utilisateur;

        if (empty($nom_utilisateur)) {
            $_SESSION['register_error'] = ['Le nom d\'utilisateur est requis'];
            Flight::redirect('/register');
            return;
        }

        $success = $this->clientModel->register($nom_utilisateur);

        if ($success) {
            $_SESSION['register_success'] = 'Inscription réussie!';
            Flight::redirect('/login');
        } else {
            $_SESSION['register_error'] = ['Erreur lors de l\'inscription'];
            Flight::redirect('/register');
        }
    }

    public function showLogin() {
        $data = [
            'error' => $_SESSION['login_error'] ?? null
        ];

        unset($_SESSION['login_error']);

        Flight::render('login.php', $data);
    }

    public function login() {
        $nom_utilisateur = Flight::request()->data->nom_utilisateur;

        if (empty($nom_utilisateur)) {
            $_SESSION['login_error'] = 'Le nom d\'utilisateur est requis';
            Flight::redirect('/login');
            return;
        }

        $client = $this->clientModel->login($nom_utilisateur);

        if ($client) {
            Flight::redirect('/select-caisse');
        } else {
            $_SESSION['login_error'] = 'Client non trouvé';
            Flight::redirect('/login');
        }
    }
    public function logout() {
        session_destroy();
        Flight::redirect('/login');
    }
}

