<?php

require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/CriptoController.php";
require_once __DIR__ . "/../Service/GeocodingService.php"; // pega api 

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
        $_SESSION["id_cliente"] = $user["id_cliente"];
        $_SESSION["nome"] = $user["nome"];
        $_SESSION["tipo"] = $user["tipo"];

        $this->salvarCoordenadas(); //para colocar no banco as coordenadas dos prestadores tambem
        header("Location: ?route=home");
        exit;
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
            $_SESSION["tipo"] = strtolower($user["tipo_TB_usuario"]);

            if ($user["tipo_TB_usuario"] == "prestador") {
                $_SESSION["id_prestador"] = $user["PK_id_TB_prestadorPerfil"];
                $_SESSION["nome"] = $user["nome_TB_prestador"];
                header("Location: ?route=dashboard");
                return;
            }
            if ($user["tipo_TB_usuario"] == "admin") {
                header("Location: ?route=painel-admin");
                return;
            }


            $_SESSION["nome"] = $user["nome_TB_cliente"];
            $_SESSION["id_cliente"] = $user["PK_id_TB_cliente"];
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
        exit;
    }

    public function formPrestador()
    {
        $categorias = CategoriaModel::getCategorias();
        $servicos = ServiceModel::getServices();
        require_once __DIR__ . "/../Views/user/form-prestador.php";
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

        $this->salvarCoordenadas(); //para colocar no banco as coordenadas dos prestadores tambem

        // Parametros de envio de notificacao
        $notificacao = [
            "titulo" => "Bem Vindo!",
            "mensagem" => "A sua conta foi ativada com sucesso. Explore o nosso painel de controle.",
            "user_id" => $_SESSION["id"]
        ];

        NotificacaoModel::enviarNotificacao($notificacao);
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


    public function atualizarFotoPerfil()
    {
        // Define o header como JSON já que a requisição é feita via fetch
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['nova_foto'])) {
            $arquivo = $_FILES['nova_foto'];

            if ($arquivo['error'] === UPLOAD_ERR_OK) {
                $pastaDestino = __DIR__ . '/../Views/uploads/';

                if (!is_dir($pastaDestino)) {
                    mkdir($pastaDestino, 0777, true);
                }

                $idUsuario = $_SESSION['id'];

                // Nome FIXO por usuário para SOBRESCREVER o arquivo antigo automaticamente
                $nomeArquivo = 'perfil_' . $idUsuario . '.jpg';
                $caminhoFisico = $pastaDestino . $nomeArquivo;
                $caminhoBanco = 'app/Views/uploads/' . $nomeArquivo;

                // Move e sobrescreve o arquivo
                if (move_uploaded_file($arquivo['tmp_name'], $caminhoFisico)) {

                    // Atualiza o banco de dados
                    UserModel::atualizarFoto($idUsuario, $caminhoBanco);

                    // Atualiza a foto na sessão
                    $_SESSION['foto'] = $caminhoBanco;

                    echo json_encode([
                        'success' => true,
                        'caminho' => $caminhoBanco
                    ]);
                    exit;
                }
            }
        }

        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar e salvar a imagem.'
        ]);
        exit;
    }

    public function salvarCoordenadas()
    {

        // ---------------------- CASO SEJA CLIENTE ----------------------
        if ($_SESSION["tipo"] == "cliente") {
            $cliente = UserModel::getClientById($_SESSION["id"]);
            $id = $cliente["PK_id_TB_cliente"];
            $rua = $cliente["rua_TB_cliente"] ?? '';
            $cep = $cliente["cep_TB_cliente"] ?? '';
            $numero = $cliente["numeroCasa_TB_cliente"] ?? '';
            $cidade = $cliente["cidade_TB_cliente"] ?? '';


            // USA O MÉTODO CORRETO AQUI
            $coordenadas = GeocodingService::buscarCoordenadasPorEndereco($rua, $numero, $cidade, $cep);
            $latitude = $coordenadas['latitude'] ?? null;
            $longitude = $coordenadas['longitude'] ?? null;
            if ($latitude && $longitude) {
                UserModel::coordenadasCliente($id, $latitude, $longitude);
            }
        }

        // ---------------------- CASO SEJA PRESTADOR ----------------------
        elseif ($_SESSION["tipo"] == "prestador") {
            $prestador = UserModel::getPrestadorById($_SESSION["id"]);
            $id = $prestador["PK_id_TB_prestadorPerfil"];
            $rua = $prestador["rua_TB_prestadorPerfil"] ?? '';
            $cep = $prestador["cep_TB_prestadorPerfil"] ?? '';
            $numero = $prestador["numeroCasa_TB_prestadorPerfil"] ?? '';
            $cidade = $prestador["cidade_TB_prestadorPerfil"] ?? '';

            // USA O MESMO MÉTODO CORRETO AQUI TAMBÉM
            $localiza = GeocodingService::buscarCoordenadasPorEndereco($rua, $numero, $cidade, $cep);
            $latitude = $localiza['latitude'] ?? null;
            $longitude = $localiza['longitude'] ?? null;
            if ($latitude && $longitude) {
                UserModel::coordenadasPrestadorr($id, $latitude, $longitude);
            }
        }
    }
    public function listaServicos()
    {
        $servicos = solicitacaoModel::getSolicitacaoCliente($_SESSION["id_cliente"]);
        require_once "app/Views/user/servicos.php";
    }
}