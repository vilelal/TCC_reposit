<?php

session_start();

$route = $_GET["route"] ?? "home";

spl_autoload_register(function ($classe) {
    $pastas = [
        __DIR__ . "/app/Controllers/",
        __DIR__ . "/app/Models/"
    ];

    foreach($pastas as $pasta) {
        $arquivo = $pasta . $classe . ".php";
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

switch ($route) {
    case "home": 
        $controller = new HomeController();
        $controller->home();

        break;
}