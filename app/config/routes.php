<?php
// app/config/routes.php
use flight\Engine;
use flight\net\Router;
use app\controllers\CaisseController;


Flight::route('GET /', function() {
    Flight::redirect('/select-caisse');
});

Flight::route('GET /select-caisse', function() {
    $controller = new CaisseController();
    $controller->index();
});