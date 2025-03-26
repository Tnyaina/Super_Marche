<?php
// app/config/routes.php
use flight\Engine;
use flight\net\Router;
use app\controllers\CaisseController;
use app\controllers\ClientController;

//--------------------- Acceuil
Flight::route('GET /', function() {
    Flight::redirect('/login');
});

Flight::route('GET /logout', function() {
    $controller = new ClientController();
    $controller->logout();
});

// caissee

Flight::route('GET /select-caisse', function() {
    $controller = new CaisseController();
    $controller->index();
});

//------------------------- Client

Flight::route('GET /register', function() {
    $controller = new ClientController();
    $controller->showRegister();
});

Flight::route('POST /register', function() {
    $controller = new ClientController();
    $controller->register();
});

Flight::route('GET /login', function() {
    $controller = new ClientController();
    $controller->showLogin();
});

Flight::route('POST /login', function() {
    $controller = new ClientController();
    $controller->login();
});