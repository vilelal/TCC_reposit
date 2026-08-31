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

            header("Location: ?route=home");
        } catch (Exception $err) {
            $_SESSION["success"] = false;
            echo "erro";
        }
    }

    public function logout() {
        session_destroy();

        header("Location: ?route=home");
    }

    public function cadastroPrestador() {
        $data = $_POST;
        $cripto = new CriptoController();


        $data["senha_user"] = password_hash($data["senha_user"], PASSWORD_DEFAULT);
        $data["cpf_cnpj_user"] = $cripto::encrypt($data["cpf_cnpj_user"]);
        $data["tel_user"] = $cripto::encrypt($data["tel_user"]);

        $user = UserModel::cadastroUser($data);

        if (!$user) {
            return;
        }

        // cria as variaveis de sessão apos o cadastro
        $_SESSION["id"] = $user["id"];
        $_SESSION["nome"] = $user["nome"];
        $_SESSION["tipo"] = $user["tipo"];

        header("Location: ?route=home");
    }
}
