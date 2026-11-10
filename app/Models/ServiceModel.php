<?php

require_once __DIR__ . "/../conexao.php";

class ServiceModel
{

    public static function listarCategorias()
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

    public static function criarSolicitacao($data)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conexao = Database::conectarBanco();

            $sql = "INSERT INTO TB_SolicitacaoServico (FK_id_TB_cliente, FK_id_TB_prestadorServico, data_agendamento_TB_SolicitacaoServico, status_TB_SolicitacaoServico , valorTotal_TB_SolicitacaoServico) VALUES (?, ?, ?,'Pendente', ?)";
            $stmt = $conexao->prepare($sql);

            $stmt->bind_param(
                "iidss",
                $data["FK_id_TB_cliente"],
                $data["FK_id_TB_prestadorServico"],
                $data["data_agendamento"],
                $data["valor_total"]
            );

            $stmt->execute();
            $stmt->close();
            $conexao->close();

            return true;
        } catch (Exception $err) {
            return false;
        }
    }

    public static function buscarPrestadoresPorCategoriaEEspacos($categoriaId, $clienteId)
    {
        $conexao = Database::conectarBanco();

        // Exemplo de query que traz o prestador, o serviço e a cidade do prestador
        $sql = "SELECT p.PK_id_TB_prestadorPerfil, p.nome_TB_prestador, p.cidade_TB_prestadorPerfil, 
                   s.nome_TB_servico, s.precoPadrao_TB_servico, ps.PK_id_TB_prestadorServico, 
                   ps.preco_customizado_TB_prestadorServico
            FROM TB_prestadorServico ps
            JOIN TB_servico s ON ps.FK_id_TB_servico = s.PK_id_TB_servico
            JOIN TB_prestadorPerfil p ON ps.FK_id_TB_prestadorPerfil = p.PK_id_TB_prestadorPerfil
            WHERE s.FK_id_TB_categoria = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $categoriaId);
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

    public static function getServices($id = null)
    {
        $conexao = Database::conectarBanco();
        if ($id) {
            $sql = "SELECT * FROM TB_prestadorServico INNER JOIN TB_servico
            ON FK_id_TB_servico = PK_id_TB_servico
            WHERE FK_id_TB_prestadorPerfil = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            $sql = "SELECT * FROM TB_servico";
            $result = $conexao->query($sql);
        }
        $conexao->close();
        $servicos = [];
        while ($row = $result->fetch_assoc()) {
            $servicos[] = $row;
        }
        return $servicos;
    }

    public static function atualizarOrcamentoSolicitacao($idSolicitacao, $novoValor)
    {
        $conexao = Database::conectarBanco();
        // Atualiza o valor total e muda o status para aguardar aprovação do cliente
        $sql = "UPDATE TB_SolicitacaoServico 
            SET valorTotal_TB_SolicitacaoServico = ?, status_TB_SolicitacaoServico = 'aguardando_orcamento' 
            WHERE PK_id_TB_SolicitacaoServico = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("di", $novoValor, $idSolicitacao);
        $stmt->execute();

        $afetadas = $stmt->affected_rows;
        $stmt->close();
        $conexao->close();

        return $afetadas > 0;
    }

    public static function getIdUsuarioClientePorSolicitacao($idSolicitacao)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT u.PK_id_TB_usuario 
            FROM TB_SolicitacaoServico s
            JOIN TB_clientePerfil c ON s.FK_id_TB_cliente = c.PK_id_TB_cliente
            JOIN TB_usuario u ON c.FK_id_TB_usuario = u.PK_id_TB_usuario
            WHERE s.PK_id_TB_SolicitacaoServico = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $idSolicitacao);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();

        $stmt->close();
        $conexao->close();

        return $resultado['PK_id_TB_usuario'] ?? null;
    }
}
