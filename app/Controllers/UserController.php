<?php

require_once __DIR__ . "/../Models/UserModel.php";

class UserController {
    public function formCadastro() {
        require_once __DIR__ . "/../Views/user/form-cadastro.php";
    }

    public function cadastro() {
        if (!is_string($_POST["email_user"])) {
            echo "Erro";
            return;
        }
        
        if (!is_string($_POST["senha_user"])) {
            echo "Erro";
            return;
        }

        $user = new UserModel();
        $userId = $user->cadastroUser($_POST);

        if (!$userid) {
            return;
        }
        header("Location: ?route=home");
    }
}