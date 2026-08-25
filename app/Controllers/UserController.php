<?php

require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/CriptoController.php";

class UserController {
    public function formCadastro() {
        require_once __DIR__ . "/../Views/user/form-cadastro.php";
    }

    public function cadastro() {
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
}