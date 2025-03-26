<?php
// app/config/routes.php
use flight\Engine;
use flight\net\Router;
use app\controllers\AchatController;

Flight::route('GET /achat/form', function() {
    $ac = new AchatController();
    $ac->afficherListe();
});
