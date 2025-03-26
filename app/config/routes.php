<?php
// app/config/routes.php
use flight\Engine;
use flight\net\Router;
use app\controllers\AchatController;

Flight::route('GET /achat/form', function() {
    $ac = new AchatController();
    $ac->afficherListe();
});
use app\controllers\CaisseController;


Flight::route('GET /', function() {
    Flight::redirect('/select-caisse');
});

Flight::route('GET /select-caisse', function() {
    $controller = new CaisseController();
    $controller->index();
});
// Flight::route('GET /achat', ['app\controllers\AchatController', 'afficherListe']);
Flight::route('GET /achat', function() {
    $controller = new AchatController();
    $controller->afficherListe();
});
// Flight::route('POST /achat/finaliser', ['app\controllers\AchatController', 'finaliserAchat']);
Flight::route('POST /achat/finaliser', function() {
    $controller = new AchatController();
    $controller->finaliserAchat();
});
// Flight::route('GET /achat/statistiques', ['app\controllers\AchatController', 'getStatistiques']);