<?php

require_once __DIR__ . "/../conexao.php";

class UserModel {
    public function cadastroUser($data) {
        try {
            $sql = "INSERT INTO TB_usuario (email_TB_usuario, senha_TB_usuario, tipo_TB_usuario) VALUES (?, ?, 'cliente')";
            $conexao = conectarBanco();
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ss", $data["email_user"], $data["senha_user"]);
            $stmt->execute();
            $userId = $stmt->insert_id;

            return $userId;
        }
        catch(Exception $err) {
            echo "Erro";
            return null;
        }

        $stmt->close();
        $conexao->close();
    }
}