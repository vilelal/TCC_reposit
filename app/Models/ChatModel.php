<?php

class ChatModel {

    // Salva uma nova mensagem enviada no chat
    public static function salvarMensagem($solicitacaoId, $remetenteId, $mensagem) {
        $conexao = Database::conectarBanco();
        $sql = "INSERT INTO TB_Mensagem (FK_id_TB_solicitacao, FK_id_remetente, mensagem) VALUES (?, ?, ?)";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iis", $solicitacaoId, $remetenteId, $mensagem);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }

    // Busca o histórico de mensagens de uma solicitação específica
    public static function buscarMensagens($solicitacaoId) {
        $conexao = Database::conectarBanco();
        $sql = "SELECT m.*, p.nome_TB_pessoa AS nome_remetente 
                FROM TB_Mensagem m
                INNER JOIN TB_pessoa p ON m.FK_id_remetente = p.PK_id_TB_pessoa
                WHERE m.FK_id_TB_solicitacao = ? 
                ORDER BY m.data_envio ASC";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $solicitacaoId);
        $stmt->execute();
        $result = $stmt->get_result();

        $mensagens = [];
        while ($row = $result->fetch_assoc()) {
            $mensagens[] = $row;
        }

        $stmt->close();
        $conexao->close();
        return $mensagens;
    }

    // Busca todas as conversas do usuário (para preencher a barra lateral)
    public static function buscarMeusChats($usuarioId) {
        $conexao = Database::conectarBanco();

        $sql = "SELECT 
                    s.PK_id_TB_solicitacaoServico AS id_solicitacao,
                    serv.nome_TB_servico,
                    p_outro.nome_TB_pessoa AS nome_contato,
                    p_outro.foto_TB_pessoa AS foto_contato,
                    (SELECT mensagem FROM TB_Mensagem WHERE FK_id_TB_solicitacao = s.PK_id_TB_solicitacaoServico ORDER BY data_envio DESC LIMIT 1) AS ultima_mensagem,
                    (SELECT COUNT(*) FROM TB_Mensagem WHERE FK_id_TB_solicitacao = s.PK_id_TB_solicitacaoServico AND FK_id_remetente != ? AND lida = 0) AS nao_lidas
                FROM TB_SolicitacaoServico s
                INNER JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
                INNER JOIN TB_servico serv ON ps.FK_id_TB_servico = serv.PK_id_TB_servico
                INNER JOIN TB_pessoa p_outro ON p_outro.PK_id_TB_pessoa = IF(s.FK_id_TB_cliente = ?, ps.FK_id_TB_pessoa, s.FK_id_TB_cliente)
                WHERE s.FK_id_TB_cliente = ? OR ps.FK_id_TB_pessoa = ?
                ORDER BY (SELECT data_envio FROM TB_Mensagem WHERE FK_id_TB_solicitacao = s.PK_id_TB_solicitacaoServico ORDER BY data_envio DESC LIMIT 1) DESC, s.PK_id_TB_solicitacaoServico DESC";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iiii", $usuarioId, $usuarioId, $usuarioId, $usuarioId);
        $stmt->execute();
        $result = $stmt->get_result();

        $chats = [];
        while ($row = $result->fetch_assoc()) {
            $chats[] = $row;
        }

        $stmt->close();
        $conexao->close();
        return $chats;
    }

    // Marca as mensagens de uma conversa como lidas
    public static function marcarMensagensComoLidas($solicitacaoId, $usuarioId) {
        $conexao = Database::conectarBanco();
        $sql = "UPDATE TB_Mensagem SET lida = 1 WHERE FK_id_TB_solicitacao = ? AND FK_id_remetente != ?";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $solicitacaoId, $usuarioId);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }
}