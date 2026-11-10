<?php

require_once __DIR__ . "/../conexao.php";

class OrcamentoModel
{
    // Dados da solicitação + quem são os dois usuários (cliente e prestador) dela
    public static function getParticipantes($idSolicitacao)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT s.status_TB_SolicitacaoServico AS status_solicitacao,
                       c.FK_id_TB_usuario  AS id_usuario_cliente,
                       pp.FK_id_TB_usuario AS id_usuario_prestador
                FROM TB_SolicitacaoServico s
                INNER JOIN TB_clientePerfil c ON s.FK_id_TB_cliente = c.PK_id_TB_cliente
                INNER JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
                INNER JOIN TB_prestadorPerfil pp ON ps.FK_id_TB_prestadorPerfil = pp.PK_id_TB_prestadorPerfil
                WHERE s.PK_id_TB_SolicitacaoServico = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $idSolicitacao);
        $stmt->execute();
        $dados = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conexao->close();
        return $dados ?: null;
    }

    // Orçamento + participantes da solicitação dele
    public static function getOrcamento($idOrcamento)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT o.*,
                       s.status_TB_SolicitacaoServico AS status_solicitacao,
                       c.FK_id_TB_usuario  AS id_usuario_cliente,
                       pp.FK_id_TB_usuario AS id_usuario_prestador
                FROM TB_orcamento o
                INNER JOIN TB_SolicitacaoServico s ON o.FK_id_TB_SolicitacaoServico = s.PK_id_TB_SolicitacaoServico
                INNER JOIN TB_clientePerfil c ON s.FK_id_TB_cliente = c.PK_id_TB_cliente
                INNER JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
                INNER JOIN TB_prestadorPerfil pp ON ps.FK_id_TB_prestadorPerfil = pp.PK_id_TB_prestadorPerfil
                WHERE o.PK_id_TB_orcamento = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $idOrcamento);
        $stmt->execute();
        $dados = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conexao->close();
        return $dados ?: null;
    }

    // Cria o orçamento e a mensagem-card no chat (tudo ou nada)
    public static function criar($idSolicitacao, $idRemetente, $d)
    {
        $conexao = Database::conectarBanco();
        try {
            $conexao->begin_transaction();

            // um novo orçamento substitui o anterior que ainda estava pendente
            $stmt = $conexao->prepare("UPDATE TB_orcamento SET status_TB_orcamento = 'substituido'
                WHERE FK_id_TB_SolicitacaoServico = ? AND status_TB_orcamento = 'pendente'");
            $stmt->bind_param("i", $idSolicitacao);
            $stmt->execute();
            $stmt->close();

            $stmt = $conexao->prepare("INSERT INTO TB_orcamento
                (FK_id_TB_SolicitacaoServico, dias_TB_orcamento, valorDiaria_TB_orcamento,
                 materiais_TB_orcamento, transporte_TB_orcamento, valor_TB_orcamento, descricao_TB_orcamento)
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "iidddds",
                $idSolicitacao,
                $d["dias"],
                $d["diaria"],
                $d["materiais"],
                $d["transporte"],
                $d["valor"],
                $d["descricao"]
            );
            $stmt->execute();
            $idOrcamento = $conexao->insert_id;
            $stmt->close();

            $texto = "Proposta de orçamento: R$ " . number_format($d["valor"], 2, ',', '.');
            $stmt = $conexao->prepare("INSERT INTO TB_mensagem
                (FK_id_TB_SolicitacaoServico, FK_id_TB_remetente, mensagem_TB_mensagem, tipo_TB_mensagem, FK_id_TB_orcamento)
                VALUES (?, ?, ?, 'orcamento', ?)");
            $stmt->bind_param("iisi", $idSolicitacao, $idRemetente, $texto, $idOrcamento);
            $stmt->execute();
            $stmt->close();

            $conexao->commit();
            $conexao->close();
            return $idOrcamento;
        } catch (Throwable $e) {
            $conexao->rollback();
            $conexao->close();
            error_log("OrcamentoModel::criar -> " . $e->getMessage());
            return false;
        }
    }

    // Cliente aceita ou recusa. Aceitar atualiza o valor da solicitação.
    public static function responder($orcamento, $idUsuarioCliente, $aceitar)
    {
        $conexao = Database::conectarBanco();
        try {
            $conexao->begin_transaction();
            $novoStatus = $aceitar ? 'aceito' : 'recusado';

            // só muda se ainda estiver pendente (evita clique duplo / corrida)
            $stmt = $conexao->prepare("UPDATE TB_orcamento SET status_TB_orcamento = ?
                WHERE PK_id_TB_orcamento = ? AND status_TB_orcamento = 'pendente'");
            $stmt->bind_param("si", $novoStatus, $orcamento["PK_id_TB_orcamento"]);
            $stmt->execute();
            $alterou = $stmt->affected_rows > 0;
            $stmt->close();

            if (!$alterou) {
                $conexao->rollback();
                $conexao->close();
                return false;
            }

            if ($aceitar) {
                $stmt = $conexao->prepare("UPDATE TB_SolicitacaoServico
                    SET valorTotal_TB_SolicitacaoServico = ?,
                        status_TB_SolicitacaoServico = IF(status_TB_SolicitacaoServico = 'pendente', 'aceito', status_TB_SolicitacaoServico)
                    WHERE PK_id_TB_SolicitacaoServico = ?");
                $stmt->bind_param("di", $orcamento["valor_TB_orcamento"], $orcamento["FK_id_TB_SolicitacaoServico"]);
                $stmt->execute();
                $stmt->close();
            }

            $texto = $aceitar
                ? "Orçamento de R$ " . number_format($orcamento["valor_TB_orcamento"], 2, ',', '.') . " aceito pelo cliente."
                : "Orçamento de R$ " . number_format($orcamento["valor_TB_orcamento"], 2, ',', '.') . " recusado pelo cliente.";
            $stmt = $conexao->prepare("INSERT INTO TB_mensagem
                (FK_id_TB_SolicitacaoServico, FK_id_TB_remetente, mensagem_TB_mensagem)
                VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $orcamento["FK_id_TB_SolicitacaoServico"], $idUsuarioCliente, $texto);
            $stmt->execute();
            $stmt->close();

            $conexao->commit();
            $conexao->close();
            return true;
        } catch (Throwable $e) {
            $conexao->rollback();
            $conexao->close();
            error_log("OrcamentoModel::responder -> " . $e->getMessage());
            return false;
        }
    }
}
