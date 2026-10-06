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

    foreach ($pastas as $pasta) {
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

    // --- ROTAS DO CHAT DE MENSAGENS ---

    // Carrega a tela dividida com a lista de conversas e o chat ativo
    case "chat":
        $controller = new ChatController();
        $controller->exibirChat();
        break;

    // Endpoint AJAX enviado pelo formulário JS
    case "enviar-mensagem":
        $controller = new ChatController();
        $controller->enviarMensagem();
        break;

    // Endpoint AJAX para atualização em tempo real
    case "carregar-mensagens-json":
        $controller = new ChatController();
        $controller->carregarMensagensJSON();
        break;

    // 4. Rotas de perfil do prestador

    case "dashboard":
        $controller = new PrestadorController();
        $controller->dashboard();
        break;

    case "relatorio":
        $controller = new PrestadorController();
        $controller->relatorio();
        break;

    case "lista-servicos":
        $controller = new PrestadorController();
        $controller->listaServicos();
        break;

    case "lista-servicos-cliente":
        $controller = new UserController();
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
        $controller->meusServicos();
        break;

    case "edit-servicos":
        $controller = new PrestadorController();
        $controller->editServicos();
        break;

    // notificacoes

    case "notificacoes":
        $controller = new NotificacaoController();
        $controller->notificacoes();
        break;

    case "painel-admin":
        $controller = new AdminController(); 
        $controller->exibirPainel();
        break;

    // Processa a criação de um novo serviço
    case "admin-criar-servico":
        $controller = new AdminController();
        $controller->criarServico();
        break;

    // Processa o banimento/exclusão de um usuário
    case "admin-banir-usuario":
        $controller = new AdminController();
        $controller->banirUsuario();
        break;

    case "solicitacao":
        $controller = new ServiceController();
        $controller->statusSolicitacao();
        break;

    case "concluir-servico":
        $controller = new PrestadorController();
        $controller->concluirServico();
        break;

    case "atualizar-foto":
        $controller = new UserController();
        $controller->atualizarFotoPerfil();
        break;
    
    case "buscar-proximos":
        $controller = new PrestadorController();
        $controller->listarPrestadores();
        break;     
    
    case "solicitar":
        $controller = new PrestadorController();
        $controller->solicitarServico();
        break;

    case "avaliar":
        $controller = new ServiceController;
        $controller->avaliar();
        break;

    default:
        $controller = new HomeController();
        $controller->home();
        break;
}
