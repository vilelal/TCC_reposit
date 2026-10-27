<?php

$time = 7 * 24 * 60 * 60; // 7 dias em segundos (tempo da session)

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

// =========================================================================
// TRAVA DE SEGURANÇA / CONTROLE DE ACESSO DO ADMINISTRAÇÃO
// =========================================================================

// Identifica o tipo do usuário na sessão (ajuste para a chave exata da sua sessão, ex: 'usuario_tipo' ou 'tipo')
$tipoUsuario = $_SESSION['usuario_tipo'] ?? $_SESSION['tipo'] ?? null;

// Rotas exclusivas do Administrador + Logout
$rotasAdmin = [
    "painel-admin",
    "admin-criar-servico",
    "admin-criar-categoria", // <- Adicionado aqui
    "admin-banir-usuario",
    "atualizar-status-denuncia",
    "banir-via-denuncia",
    "logout"
];

// 1. SE FOR ADMIN: Só pode acessar as rotas do $rotasAdmin. Se tentar outra, é redirecionado para 'painel-admin'.
if ($tipoUsuario === 'admin') {
    if (!in_array($route, $rotasAdmin)) {
        header("Location: ?route=painel-admin");
        exit;
    }
}

// 2. SE NÃO FOR ADMIN: Impede o acesso às rotas restritas de administração
if (in_array($route, ["painel-admin", "admin-criar-servico", "admin-banir-usuario"])) {
    if ($tipoUsuario !== 'admin') {
        header("Location: ?route=home");
        exit;
    }
}

// =========================================================================

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

    case "solicitar-servico":
        $controller = new ServiceController();
        $controller->exibirWizard();
        break;

    // --- ROTAS DO CHAT DE MENSAGENS ---

    case "chat":
        $controller = new ChatController();
        $controller->exibirChat();
        break;

    case "enviar-mensagem":
        $controller = new ChatController();
        $controller->enviarMensagem();
        break;

    case "carregar-mensagens-json":
        $controller = new ChatController();
        $controller->carregarMensagensJSON();
        break;

    // Rotas de perfil e prestador

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

    case "editPerfil":
        $controller = new UserController();
        $controller->editPerfil();
        break;

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

    case "notificacoes":
        $controller = new NotificacaoController();
        $controller->notificacoes();
        break;

    case "painel-admin":
        $controller = new AdminController(); 
        $controller->exibirPainel();
        break;

    case "admin-criar-servico":
        $controller = new AdminController();
        $controller->criarServico();
        break;

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
        $controller = new ServiceController();
        $controller->avaliar();
        break;
    
    case "avaliar-prestador":
        $controller = new ServiceController;
        $controller->avaliacao();
        break;

    case 'enviar-denuncia':
        $controller = new DenunciaController();
        $controller->registrarDenuncia();
        break;
    
    case "admin-criar-categoria":
        $controller = new AdminController();
        $controller->criarCategoria();
        break;  
            
    case "banir-via-denuncia":
        $controller = new AdminController();
        $controller->processarBanimentoPorDenuncia();
        break;
    
    case "atualizar-status-denuncia":
        $controller = new AdminController();
        $controller->atualizarStatusDenuncia();
        break;

    case "perfil-prestador":
        $controller = new PrestadorController();
        $controller->exibirPerfil();
        break;

    default:
        $controller = new HomeController();
        $controller->home();
        break;
}