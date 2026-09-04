<?php

require_once __DIR__ . "/../conexao.php";

class UserModel
{
    public static function cadastroUser($data)
    {
        // Ativa o disparo de exceções no mysqli para entrar no catch em caso de erro
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conexao = Database::conectarBanco();

            // Inicia uma transação
            $conexao->begin_transaction();

            // 1. Inserção do Usuário
            $sql = "INSERT INTO TB_usuario (email_TB_usuario, senha_TB_usuario, tipo_TB_usuario) VALUES (?, ?, 'cliente')";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ss", $data["email_user"], $data["senha_user"]);
            $stmt->execute();
            $userId = $stmt->insert_id;
            $stmt->close();

            // 2. Inserção do Perfil do Cliente
            $sql = "INSERT INTO TB_clientePerfil (FK_id_TB_usuario, nome_TB_cliente, cpf_TB_cliente, tel_TB_cliente,
                    cep_TB_cliente, rua_TB_cliente, cidade_TB_cliente, numeroCasa_TB_cliente)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param(
                "isssssss",
                $userId,
                $data["nome_user"],
                $data["cpf_user"],
                $data["telefone_user"],
                $data["cep_user"],
                $data["rua_user"],
                $data["cidade_user"],
                $data["numero_user"]
            );
            $stmt->execute();
            $stmt->close();

            // Confirma a transação no banco
            $conexao->commit();
            $conexao->close();

            $user = [
                "id" => $userId,
                "nome" => $data["nome_user"],
                "tipo" => "cliente",
            ];

            return $user;
        } catch (Exception $err) {
            // Desfaz alterações no banco caso algo falhe
            if (isset($conexao) && $conexao instanceof mysqli) {
                $conexao->rollback();
                $conexao->close();
            }
        }
    }

    public static function login($data)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_clientePerfil
        INNER JOIN TB_usuario ON FK_id_TB_usuario = PK_id_TB_usuario
        WHERE email_TB_usuario = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $data["email_user"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conexao->close();
        $user = $result->fetch_assoc();

        if (!$user) {
            return false;
        }
        if (!password_verify($data["senha_user"], $user["senha_TB_usuario"])) {
            return false;
        }

        return $user;
    }

    public static function cadastroPrestador($data)
    {
        try {
            $conexao = Database::conectarBanco();
            $conexao->begin_transaction();
            
            if (!isset($_SESSION["id"])) {
                // 1. Inserção do Usuário
                $sql = "INSERT INTO TB_usuario (email_TB_usuario, senha_TB_usuario, tipo_TB_usuario) VALUES (?, ?, 'prestador')";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("ss", $data["email_user"], $data["senha_user"]);
                $stmt->execute();
                $userId = $stmt->insert_id;
                $stmt->close();
            } else {
                $userId = $_SESSION["id"];
                $sql = "UPDATE TB_usuario SET tipo_TB_usuario = 'prestador' WHERE PK_id_TB_usuario = ?";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("i", $_SESSION["id"]);
                $stmt->execute();
                $stmt->close();
            }
            // 2. Inserção do Perfil do Prestador
            $sql = "INSERT INTO TB_prestadorPerfil (FK_id_TB_usuario, nome_TB_prestador, cpf_cnpj_TB_prestador, tel_TB_prestador,
                    cep_TB_prestadorPerfil, rua_TB_prestadorPerfil, cidade_TB_prestadorPerfil, numeroCasa_TB_prestadorPerfil, bio_TB_prestador)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexao->prepare($sql);

            // Utilize as variáveis limpadas no bind_param
            $stmt->bind_param(
                "issssssss",
                $userId,
                $data["nome_user"],
                $data["cpf_cnpj_user"],
                $data["tel_user"],
                $data["cep_user"],
                $data["rua_user"],
                $data["cidade_user"],
                $data["numero_user"],
                $data["bio_user"]
            );
            $stmt->execute();
            $stmt->close();
            $conexao->commit();
            $conexao->close();

            $user = [
                "id" => $userId,
                "nome" => $data["nome_user"],
                "tipo" => "prestador",
            ];

            return $user;

        } catch (Exception $err) {
            if (isset($conexao) && $conexao instanceof mysqli) {
                $conexao->rollback();
                $conexao->close();
                echo $err;
            }
        }
    }
    public static function getClientById($id) {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_clientePerfil
                INNER JOIN TB_usuario ON FK_id_TB_usuario = PK_id_TB_usuario
                WHERE FK_id_TB_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (!$user) {
            return null;
        }
        return $user;
    }

}
