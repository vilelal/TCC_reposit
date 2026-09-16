<?php

require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/CriptoController.php";

class UserController
{
    public function formCadastro()
    {
        require_once __DIR__ . "/../Views/user/form-cadastro.php";
    }

    public function cadastro()
    {
        $data = $_POST;
        $cripto = new CriptoController();


        $data["senha_user"] = password_hash($data["senha_user"], PASSWORD_DEFAULT);
        $data["cpf_user"] = $cripto::encrypt($data["cpf_user"]);
        $data["telefone_user"] = $cripto::encrypt($data["telefone_user"]);

        $user = UserModel::cadastroUser($data);

        if (!$user) {
            return;
        }

        // cria as variaveis de sessão apos o cadastro
        $_SESSION["id"] = $user["id"];
        $_SESSION["nome"] = $user["nome"];
        $_SESSION["tipo"] = $user["tipo"];

        $this->salvarCoordenadas();            // ---------------------------------  CHAMO ELA PARA SALVAR NO BANCO A LONGI E LAT

        header("Location: ?route=home");
    }

    public function formLogin()
    {
        require_once __DIR__ . "/../Views/user/form-login.php";
    }

    public function login()
    {
        try {
            $data = $_POST;
            $user = UserModel::login($data);

            if (!$user) {
                $_SESSION["success"] = false;
                header("Location: ?route=login-form");
                return;
            }

            $_SESSION["success"] = true; // status da requisicao
            // dados da sessão do usuario apos login
            $_SESSION["id"] = $user["PK_id_TB_usuario"];
            $_SESSION["nome"] = $user["nome_TB_cliente"];
            $_SESSION["tipo"] = $user["tipo_TB_usuario"];

            if ($user["tipo_TB_usuario"] == "prestador") {
                header("Location: ?route=dashboard");
            }

            header("Location: ?route=home");
        } catch (Exception $err) {
            $_SESSION["success"] = false;
            echo "erro";
        }
    }

    public function logout()
    {
        session_destroy();

        header("Location: ?route=home");
    }

    public function formPrestador()
    {
        $categorias = CategoriaModel::getCategorias();
        $servicos = ServiceModel::getServices();
        require_once __DIR__ . "/../Views/user/form-prestador.php";
    }

    public function salvarCoordenadas(){                          //aqui serve para salvar as coordenadas no banco my sql, na verdade aqui extrai e manda para o model salvar
       
       if ($_SESSION["tipo"] == "cliente") {
        $cliente = UserModel::getClienteById($_SESSION["id"]);   // passa todas as informações do cliente para essa variavel
        $id = $cliente["PK_id_TB_cliente"];
        $rua = $cliente["rua_tb_cliente"]; 
        $cep = $cliente["cep_tb_cliente"];
        $numero = $cliente["numeroCasa_TB_cliente"];
        $cidade = $cliente["cidade_TB_cliente"];

        $coordenadas = GeocodingService::buscarCoordenadasPorEndereco($rua,$numero, $cidade, $cep ); //ele pega o retorno da longi e lat do lugar, ele é tipo uma array

        $latitude = $coordenadas['latitude'];
        $longitude = $coordenadas['longitude'];

        if ($latitude && $longitude) {
        UserModel::coordenadasCliente($id, $latitude, $longitude);  // aqui passo para o model inserir no banco latitude e longitude 
        }
       }  elseif ($_SESSION["tipo"] == "prestador") {             // ------------------------------   CASO SEJA PRESTADOR né ----------------------------------------
        
        $prestador = UserModel::getPrestadorById($_SESSION["id"]);
        $id = $prestador["PK_id_TB_prestadorPerfil"];
        $rua = $prestador["rua_TB_prestadorPerfil"]; 
        $cep = $prestador["cep_TB_prestadorPerfil"];
        $numero = $prestador["numeroCasa_TB_prestadorPerfil"];
        $cidade = $prestador["cidade_TB_prestadorPerfil"];

        $localiza = GeocodingService::buscarCoordenadasPorEndereco($rua,$numero, $cidade, $cep ); //ele pega o retorno da longi e lat do lugar, ele é tipo uma array

        $latitude = $localiza['latitude'];
        $longitude = $localiza['longitude'];

        if ($latitude && $longitude) {
        UserModel::coordenadasPrestador($id, $latitude, $longitude);  // aqui passo para o model inserir no banco latitude e longitude , chama a função
       
        }
       
    }
}

    public function filtrandoPrestadores(){                 //----------------------- aqui passa o id da pessoa e a distancia padrão de busca para o model comparar. -----------
         $cliente = UserModel::getClienteById($_SESSION["id"]);
         $id = $cliente["PK_id_TB_cliente"];
         UserModel:: puxarCoordenadas($id); 
         
    } 



    public function cadastroPrestador()
    {
        $data = $_POST;
        $cripto = new CriptoController();

        // criptografar senha caso o usuario tenha que digitar senha pra cadatro
        if (!isset($_SESSION["id"])) {
            $data["senha_user"] = password_hash($data["senha_user"], PASSWORD_DEFAULT);
        }
        $data["cpf_cnpj_user"] = $cripto::encrypt($data["cpf_cnpj_user"]);
        $data["tel_user"] = $cripto::encrypt($data["tel_user"]);

        $user = UserModel::cadastroPrestador($data);

        if (!$user) {
            return;
        }
    
        // cria as variaveis de sessão apos o cadastro
        $_SESSION["id"] = $user["id"];
        $_SESSION["id_prestador"] = $user["prestadorId"];
        $_SESSION["nome"] = $user["nome"];
        $_SESSION["tipo"] = $user["tipo"];

        // Parametros de envio de notificacao
        $notificacao = [
            "titulo" => "Bem Vindo!",
            "mensagem" => "A tua conta foi ativada com sucesso. Explore o nosso painel de controle.",
            "user_id" => $_SESSION["id"]
        ];

        NotificacaoModel::enviarNotificacao($notificacao);
        
        $this->salvarCoordenadas();              //chamando coordenada para salvar no banco


        header("Location: ?route=dashboard");
    }

    public function perfil()
    {
        if ($_SESSION["tipo"] == "prestador") {
            $user = UserModel::getPrestadorById($_SESSION["id"]);
            $user["tel_TB_prestador"] = CriptoController::decrypt($user["tel_TB_prestador"]);
            $user["cpf_cnpj_TB_prestador"] = CriptoController::decrypt($user["cpf_cnpj_TB_prestador"]);
        } else {
            $user = UserModel::getClientById($_SESSION["id"]);
            $user["tel_TB_cliente"] = CriptoController::decrypt($user["tel_TB_cliente"]);
            $user["cpf_TB_cliente"] = CriptoController::decrypt($user["cpf_TB_cliente"]);
        }
        require_once "app/Views/user/perfil.php";
    }

    public function edit()
    {
        $cripto = new CriptoController();
        if ($_SESSION["tipo"] == "prestador") {
            $user = UserModel::getPrestadorById($_SESSION["id"]);
            $user["cpf_cnpj_TB_prestador"] = $cripto::decrypt($user["cpf_cnpj_TB_prestador"]);
            $user["tel_TB_prestador"] = $cripto::decrypt($user["tel_TB_prestador"]);
        } else {
            $user = UserModel::getClientById($_SESSION["id"]);
            $user["cpf_TB_cliente"] = $cripto::decrypt($user["cpf_TB_cliente"]);
            $user["tel_TB_cliente"] = $cripto::decrypt($user["tel_TB_cliente"]);
        }

        require_once "app/Views/user/edit-perfil.php";
    }

    public function editPerfil()
    {
        $data = $_POST;
        UserModel::editPerfil($data);
        header("Location: ?route=perfil");
    }

    public function seguranca() 
    {
        require_once "app/Views/user/seguranca.php";
    }

    public function buscarPrestadoresProximos()
{
    if (!isset($_SESSION["id"])) {
        header("Location: ?route=login-form");
        exit;
    }

    if ($_SESSION["tipo"] !== "cliente") {
        header("Location: ?route=home");
        exit;
    }

    $cliente = UserModel::getClientById($_SESSION["id"]);

    if (!$cliente) {
        header("Location: ?route=home");
        exit;
    }

    $idCliente = $cliente["PK_id_TB_cliente"];

    $idServico = isset($_POST["FK_id_TB_servico"])
        ? (int) $_POST["FK_id_TB_servico"]
        : 0;

    if ($idServico <= 0) {
        header("Location: ?route=home");
        exit;
    }

    $prestadores = UserModel::puxarCoordenadas(
        $idCliente,
        $idServico,
        35,
        10
    );

    // Guarda os dados da solicitação na sessão
    $_SESSION["solicitacao_servico"] = [
        "FK_id_TB_categoria" => $_POST["FK_id_TB_categoria"] ?? null,
        "FK_id_TB_servico" => $idServico,
        "urgencia" => $_POST["urgencia"] ?? null,
        "data_agendamento" => $_POST["data_agendamento"] ?? null,
        "solicitar_orcamento" => isset($_POST["solicitar_orcamento"]) ? 1 : 0,
        "descricao" => $_POST["descricao"] ?? ""
    ];

    require_once __DIR__ . "/../Views/user/wizard-form.php";
}
}
