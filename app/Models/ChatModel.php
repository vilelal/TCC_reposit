<?php
require_once __DIR__ . "/../conexao.php";
class ChatModel
{

    // Salva uma nova mensagem enviada no chat
    public static function salvarMensagem($solicitacaoId, $remetenteId, $mensagem)
    {
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
    public static function buscarMensagens($solicitacaoId)
    {
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
    public static function buscarMeusChats($usuarioId)
    {
        $conexao = Database::conectarBanco();

        $sql = "SELECT 
    s.PK_id_TB_SolicitacaoServico AS id_solicitacao,
    serv.nome_TB_servico,
    -- Identifica o nome do contato de acordo com quem está logado (se for o cliente, traz o prestador; se for o prestador, traz o cliente)
    IF(c.FK_id_TB_usuario = ?, pp.nome_TB_prestador, c.nome_TB_cliente) AS nome_contato,
    
    -- Traz a última mensagem trocada na solicitação
    (
        SELECT m.mensagem_TB_mensagem 
        FROM TB_mensagem m 
        WHERE m.FK_id_TB_SolicitacaoServico = s.PK_id_TB_SolicitacaoServico 
        ORDER BY m.data_envio_TB_mensagem DESC 
        LIMIT 1
    ) AS ultima_mensagem,
    
    -- Conta as mensagens não lidas enviadas pela outra pessoa
    (
        SELECT COUNT(*) 
        FROM TB_mensagem m 
        WHERE m.FK_id_TB_SolicitacaoServico = s.PK_id_TB_SolicitacaoServico 
          AND m.FK_id_TB_remetente != ? 
          AND m.lida_TB_mensagem = 0
    ) AS nao_lidas

FROM TB_SolicitacaoServico s
INNER JOIN TB_clientePerfil c 
    ON s.FK_id_TB_cliente = c.PK_id_TB_cliente
INNER JOIN TB_prestadorServico ps 
    ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
INNER JOIN TB_prestadorPerfil pp 
    ON ps.FK_id_TB_prestadorPerfil = pp.PK_id_TB_prestadorPerfil
INNER JOIN TB_servico serv 
    ON ps.FK_id_TB_servico = serv.PK_id_TB_servico

-- Filtra os chats onde o usuário logado é o Cliente OU é o Prestador do serviço
WHERE c.FK_id_TB_usuario = ? OR pp.FK_id_TB_usuario = ?

ORDER BY 
    COALESCE(
        (
            SELECT m.data_envio_TB_mensagem 
            FROM TB_mensagem m 
            WHERE m.FK_id_TB_SolicitacaoServico = s.PK_id_TB_SolicitacaoServico 
            ORDER BY m.data_envio_TB_mensagem DESC 
            LIMIT 1
        ), 
        s.data_agendamento_TB_SolicitacaoServico
    ) DESC, 
    s.PK_id_TB_SolicitacaoServico DESC;";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iiii", $idUsuarioLogado, $idUsuarioLogado, $idUsuarioLogado, $idUsuarioLogado);
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
    public static function marcarMensagensComoLidas($solicitacaoId, $usuarioId)
    {
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
