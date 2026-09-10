<?php

$time = 7 * 24 * 60 * 60; // tempo da session (quanto tempo ele fica logado ao fazer login)

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

    case "login":
        $controller = new UserController();
        $controller->login();
        break;

    case "logout": 
        $controller = new UserController();
        $controller->logout();
        break;
    
    case "prestador-form":
        $controller = new UserController();
        $controller->formPrestador();
        break;

    case "cadastro-prestador":
        $controller = new UserController();
        $controller->cadastroPrestador();
        break;

    // --- ROTAS DO FLUXO DE SOLICITAÇÃO DE SERVIÇO ---

    // 1. Exibe o Formulário Wizard (Categorias, Serviços, Urgência)
    case "solicitar-servico":
        $controller = new ServiceController();
        $controller->exibirWizard();
        break;

    // 2. Processa os dados do Wizard e lista os prestadores próximos (Salva na Sessão)
    case "buscar-prestadores-proximos":
        $controller = new ServiceController();
        $controller->buscarPrestadoresProximos();
        break;

    // 3. Grava definitivamente na TB_SolicitacaoServico após o aceite
    case "confirmar-solicitacao":
        $controller = new ServiceController();
        $controller->confirmarEAceitarSolicitacao();
        break;

    // 4. Rotas de perfil do prestador
    
    case "dashboard":
        $controller = new PrestadorController();
        $controller->dashboard();
        break;

    case "lista-servicos":
        $controller = new PrestadorController();
        $controller->listaServicos();
        break;
    
    case "perfil":
        $controller = new UserController();
        $controller->perfil();
        break;

    case "edit-perfil":
        $controller = new UserController();
        $controller->edit();
        break;

    case "editPerfil": {
        $controller = new UserController();
        $controller->editPerfil();
        break;
    }

    case "seguranca": 
        $controller = new UserController();
        $controller->seguranca();
        break;

    case "meus-servicos": 
        $controller = new PrestadorController();
        $controller->listaServicos();
        break;

    case "edit-servicos":
        $controller = new PrestadorController();
        $controller->editServicos();
        break;

    default:
        $controller = new HomeController();
        $controller->home();
        break;
}