<?php

require_once __DIR__ . "/../conexao.php";

class solicitacaoModel
{

    public static function buscarTodasCategorias()
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT PK_id_TB_categoria, nome_TB_categoria FROM TB_categoria";
        $result = $conexao->query($sql);

        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }

        $conexao->close();
        return $categorias;
    }

    public static function buscarTodosServicos()
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT PK_id_TB_servico, FK_id_TB_categoria, nome_TB_servico FROM TB_servico";
        $result = $conexao->query($sql);

        $servicos = [];
        while ($row = $result->fetch_assoc()) {
            $servicos[] = $row;
        }

        $conexao->close();
        return $servicos;
    }

    // Busca os prestadores da mesma cidade/bairro do cliente cadastrado
    public static function buscarPrestadoresProximos($clienteId, $servicoId)
    {
        $conexao = Database::conectarBanco();

        // 1. Pega a cidade do cliente cadastrado na TB_clientePerfil
        $sqlCliente = "SELECT cidade_TB_clientePerfil FROM TB_clientePerfil WHERE PK_id_TB_cliente = ?";
        $stmtC = $conexao->prepare($sqlCliente);
        $stmtC->bind_param("i", $clienteId);
        $stmtC->execute();
        $cliente = $stmtC->get_result()->fetch_assoc();
        $cidadeCliente = $cliente['cidade_TB_clientePerfil'] ?? '';
        $stmtC->close();

        // 2. Busca prestadores que oferecem esse serviço e estão na mesma cidade do cliente
        $sql = "SELECT p.PK_id_TB_prestadorPerfil, p.nome_TB_prestador, p.cidade_TB_prestadorPerfil, 
                       ps.PK_id_TB_prestadorServico, ps.preco_customizado_TB_prestadorServico,
                       s.nome_TB_servico, s.precoPadrao_TB_servico
                FROM TB_prestadorServico ps
                JOIN TB_servico s ON ps.FK_id_TB_servico = s.PK_id_TB_servico
                JOIN TB_prestadorPerfil p ON ps.FK_id_TB_prestadorPerfil = p.PK_id_TB_prestadorPerfil
                WHERE ps.FK_id_TB_servico = ? 
                  AND p.cidade_TB_prestadorPerfil = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("is", $servicoId, $cidadeCliente);
        $stmt->execute();
        $result = $stmt->get_result();

        $prestadores = [];
        while ($row = $result->fetch_assoc()) {
            $prestadores[] = $row;
        }

        $stmt->close();
        $conexao->close();

        return $prestadores;
    }

    // Insere na tabela TB_SolicitacaoServico APENAS quando houver o aceite de ambos
    public static function criarSolicitacaoFinal($clienteId, $prestadorServicoId, $dataAgendamento, $valorTotal)
    {
        $conexao = Database::conectarBanco();

        $sql = "INSERT INTO TB_SolicitacaoServico 
                (FK_id_TB_cliente, FK_id_TB_prestadorServico, data_agendamento_TB_SolicitacaoServico, status_TB_SolicitacaoServico, valorTotal_TB_SolicitacaoServico) 
                VALUES (?, ?, ?, 'aceito', ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iisd", $clienteId, $prestadorServicoId, $dataAgendamento, $valorTotal);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();

        return $sucesso;
    }
}
