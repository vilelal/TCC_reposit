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
            $clienteId = $stmt->insert_id;
            $stmt->close();

            // Confirma a transação no banco
            $conexao->commit();
            $conexao->close();

            $user = [
                "id" => $userId,
                "id_cliente" => $clienteId,
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
        $sql = "SELECT * FROM TB_usuario
        LEFT JOIN TB_clientePerfil ON TB_clientePerfil.FK_id_TB_usuario = PK_id_TB_usuario
        LEFT JOIN TB_prestadorPerfil ON TB_prestadorPerfil.FK_id_TB_usuario = PK_id_TB_usuario
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
            $prestadorId = $stmt->insert_id;
            $stmt->close();

            if (isset($data["servicos"])) {
                foreach ($data["servicos"] as $servicoId) {
                    $sql = "INSERT INTO TB_prestadorServico
                    (FK_id_TB_servico, FK_id_TB_prestadorPerfil)
                    VALUES (?, ?)"; //talvez tenha preco customizado, por enquanto ignorado
                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param("ii", $servicoId, $prestadorId);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $conexao->commit();
            $conexao->close();

            $user = [
                "id" => $userId,
                "prestadorId" => $prestadorId,
                "nome" => $data["nome_user"],
                "tipo" => "prestador",
            ];

            return $user;
        } catch (Exception $err) {
            if (isset($conexao) && $conexao instanceof mysqli) {
                $conexao->rollback();
                $conexao->close();
                die($err);
            }
        }
    }
    public static function getClientById($id)
    {
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



    public static function getPrestadorById($id)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT p.*,
        u.*,

        (
        SELECT COUNT(*)
        FROM TB_SolicitacaoServico s
        WHERE s.FK_id_TB_prestadorServico = p.PK_id_TB_prestadorPerfil 
        AND s.status_TB_SolicitacaoServico = 'concluido'
        ) AS total_servicos

        FROM TB_prestadorPerfil p
        INNER JOIN TB_usuario u ON p.FK_id_TB_usuario = u.PK_id_TB_usuario
        WHERE FK_id_TB_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prestador = $result->fetch_assoc();
        return $prestador;
    }

    public static function getUserByIdCliente($id)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_clientePerfil
                INNER JOIN TB_usuario ON FK_id_TB_usuario = PK_id_TB_usuario
                WHERE PK_id_TB_cliente = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user;
    }

    public static function getUserByIdPrestador($id)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_prestadorPerfil
        INNER JOIN TB_usuario ON FK_id_TB_usuario = PK_id_TB_usuario
        WHERE PK_id_TB_prestadorPerfil = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prestador = $result->fetch_assoc();
        return $prestador;
    }

    public static function editPerfil($data)
    {
        $tel = CriptoController::encrypt($data["tel_user"]);
        $cpf = CriptoController::encrypt($data["cpf_user"]);

        $conexao = Database::conectarBanco();
        if ($_SESSION["tipo"] == "prestador") {
            $sql = "UPDATE TB_prestadorPerfil SET
            nome_TB_prestador = ?,
            tel_TB_prestador = ?,
            cpf_cnpj_TB_prestador = ?,
            bio_TB_prestador = ?,
            rua_TB_prestadorPerfil = ?,
            cep_TB_prestadorPerfil = ?,
            numeroCasa_TB_prestadorPerfil = ?,
            cidade_TB_prestadorPerfil = ?
            WHERE FK_id_TB_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param(
                "ssssssssi",
                $data["nome_user"],
                $tel,
                $cpf,
                $data["bio_user"],
                $data["rua_user"],
                $data["cep_user"],
                $data["numero_user"],
                $data["cidade_user"],
                $_SESSION["id"]
            );
            $stmt->execute();
        } else {
            $sql = "UPDATE TB_clientePerfil SET
            nome_TB_cliente = ?,
            tel_TB_cliente = ?,
            cpf_TB_cliente = ?,
            rua_TB_cliente = ?,
            cep_TB_cliente = ?,
            numeroCasa_TB_cliente = ?,
            cidade_TB_cliente = ?
            WHERE FK_id_TB_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("sssssssi", $data["nome_user"], $tel, $cpf, $data["rua_user"], $data["cep_user"], $data["numero_user"], $data["cidade_user"], $_SESSION["id"]);
            $stmt->execute();
        }

        $conexao->close();
        $stmt->close();

        $_SESSION["nome"] = $data["nome_user"];
    }
    // Atualiza o caminho da foto na tabela principal de usuários
    public static function atualizarFoto($idUsuario, $caminhoFoto)
    {
        $conexao = Database::conectarBanco();
        $sql = "UPDATE tb_usuario SET foto_TB_usuario = ? WHERE PK_id_TB_usuario = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $caminhoFoto, $idUsuario);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();

        return $sucesso;
    }

    // aqui salvo no banco a lat e long do cliente
    public static function coordenadasCliente($id, $latitude, $longitude)
    {
        $conexao = Database::conectarBanco();

        $sql = "UPDATE tb_clientePerfil 
            SET latitude_TB_clientePerfil = ?, 
                longitude_TB_clientePerfil = ? 
            WHERE PK_id_TB_cliente = ?";

        $stmt = $conexao->prepare($sql);
        // "ddi" = double (latitude), double (longitude), integer (id do cliente)
        $stmt->bind_param("ddi", $latitude, $longitude, $id);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();

        return $sucesso;
    }

    // aqui salvo no banco a lat e long do prestador

    public static function coordenadasPrestadorr($id, $latitude, $longitude)
    {
        $conexao = Database::conectarBanco();

        $sql = "UPDATE TB_prestadorPerfil 
            SET latitude_TB_prestadorPerfil = ?, 
                longitude_TB_prestadorPerfil = ? 
            WHERE PK_id_TB_prestadorPerfil= ?";

        $stmt = $conexao->prepare($sql);
        // "ddi" = double (latitude), double (longitude), integer (id do cliente)
        $stmt->bind_param("ddi", $latitude, $longitude, $id);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();

        return $sucesso;
    }


    public static function buscarPrestadoresProximos($clienteId, $servicoId, $raioMaximoKm = 50)
    {
        $conexao = Database::conectarBanco();

        // 1. Busca as coordenadas salvas do cliente
        $sqlCliente = "SELECT latitude_TB_clientePerfil, longitude_TB_clientePerfil 
                   FROM TB_clientePerfil 
                   WHERE PK_id_TB_cliente = ?";
        $stmtC = $conexao->prepare($sqlCliente);
        $stmtC->bind_param("i", $clienteId);
        $stmtC->execute();
        $cliente = $stmtC->get_result()->fetch_assoc();  //pega lat e long do cliente
        $stmtC->close();

        $latCliente = $cliente['latitude_TB_clientePerfil']; //lat do cliente
        $lngCliente = $cliente['longitude_TB_clientePerfil']; // long do cliente

        if (!$latCliente || !$lngCliente) {
            return []; // Retorna vazio se o cliente não tiver coordenadas salvas
        }

        // 2. Faz comparação: calcula a distância em KM e ordena os prestadores, melhor fazer aqui mesmo
        $sql = "SELECT p.PK_id_TB_prestadorPerfil, p.nome_TB_prestador, p.cidade_TB_prestadorPerfil, 
                   ps.PK_id_TB_prestadorServico, ps.preco_customizado_TB_prestadorServico,
                   s.nome_TB_servico, s.precoPadrao_TB_servico,
                   (6371 * acos(
                       cos(radians(?)) * cos(radians(p.latitude_TB_prestadorPerfil)) * 
                       cos(radians(p.longitude_TB_prestadorPerfil) - radians(?)) + 
                       sin(radians(?)) * sin(radians(p.latitude_TB_prestadorPerfil))
                   )) AS distancia_km,

                   (
                    SELECT COUNT(*)
                    FROM TB_SolicitacaoServico solicitacao
                    WHERE solicitacao.FK_id_TB_prestadorServico = p.PK_id_TB_prestadorPerfil 
                    AND solicitacao.status_TB_SolicitacaoServico = 'concluido'
                    ) AS total_servicos,

                    (
                    SELECT ROUND(AVG(a.nota_TB_avaliacao), 1)
                    FROM TB_avaliacao a
                    WHERE p.FK_id_TB_usuario = a.FK_id_TB_usuario
                    ) AS media_avaliacoes

            FROM TB_prestadorServico ps
            JOIN TB_servico s ON ps.FK_id_TB_servico = s.PK_id_TB_servico
            JOIN TB_prestadorPerfil p ON ps.FK_id_TB_prestadorPerfil = p.PK_id_TB_prestadorPerfil 
            WHERE ps.FK_id_TB_servico = ?
            HAVING distancia_km <= ?
            ORDER BY distancia_km ASC";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("dddid", $latCliente, $lngCliente, $latCliente, $servicoId, $raioMaximoKm);
        $stmt->execute();
        $result = $stmt->get_result();

        $prestadores = [];
        while ($row = $result->fetch_assoc()) {
            $row['distancia_km'] = round($row['distancia_km'], 1); // Exemplo: 3.2 km
            $prestadores[] = $row;
        }

        $stmt->close();
        $conexao->close();

        return $prestadores;
    }
}
