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
            $stmt->close();

            $sql = "INSERT INTO TB_clientePerfil (FK_id_TB_usuario, nome_TB_cliente, cpf_TB_cliente, tel_TB_cliente,
            cep_TB_cliente, rua_TB_cliente, cidade_TB_cliente, numeroCasa_TB_cliente)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("isssssss", $userId, $data["nome_user"], $data["cpf_user"], $data["telefone_user"], $data["cep_user"], $data["rua_user"], $data["cidade_user"], $data["numero_user"]);
            $stmt->execute();
            $stmt->close();

            $conexao->close();

            return $userId;
        }
        catch(Exception $err) {
            echo "Erro";
            return null;
        }
    }
}