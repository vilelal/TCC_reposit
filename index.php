<?php

$time = 7 * 24 * 60 * 60; // tempo da session (quanto tempo ele fica logado aofazer login)

// config da session
session_set_cookie_params([
    "lifetime" => $time,
    "path" => "/",
    "httponly" => true,
    "samesite" => "Lax"
]);

// configura tempo maximo da session no servidor
ini_set("session.gc_maxlifetime", $time);

session_start(); // inicia session

// renova o tempo da sessão a cada requisição
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), session_id(), time() + $time, "/");
}

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

    case "cadastro-form":
        $controller = new UserController();
        $controller->formCadastro();
        break;

    case "cadastro": 
        $controller = new UserController();
        $controller->cadastro();
        break;
    
    case "login-form":
        $controller = new UserController();
        $controller->formLogin();
        break;
    
    case "prestador-form":
        $controller = new UserController();
        $controller->formPrestador();
        break;

    case "cadastro-prestador":
        $controller = new UserController();
        $controller->cadastroPrestador();
        break;
    
    case "login":
        $controller = new UserController();
        $controller->login();
        break;

    case "logout": 
        $controller = new UserController();
        $controller->logout();
        break;
}