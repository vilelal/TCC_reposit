<?php
require_once __DIR__ . "/../conexao.php";
class ChatModel
{


    // Salva uma nova mensagem enviada no chat (agora com suporte a imagem)
    public static function salvarMensagem($solicitacaoId, $remetenteId, $mensagem, $caminhoImagem = null)
    {
        $conexao = Database::conectarBanco();

        // Adicionamos a nova coluna imagem_TB_mensagem no INSERT
        $sql = "INSERT INTO TB_mensagem (FK_id_TB_SolicitacaoServico, FK_id_TB_remetente, mensagem_TB_mensagem, imagem_TB_mensagem) VALUES (?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        // Mudou para "iiss" (Integer, Integer, String, String)
        $stmt->bind_param("iiss", $solicitacaoId, $remetenteId, $mensagem, $caminhoImagem);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }

    // Busca o histórico de mensagens de uma solicitação específica (para o JSON do JavaScript)
    public static function buscarMensagens($solicitacaoId)
    {
        $conexao = Database::conectarBanco();

        // Busca todas as colunas reais da sua tabela de mensagens
        $sql = "SELECT * FROM TB_mensagem WHERE FK_id_TB_SolicitacaoServico = ? ORDER BY data_envio_TB_mensagem ASC";

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
        
        -- Identifica o nome do contato de acordo com quem está logado
        IF(c.FK_id_TB_usuario = ?, pp.nome_TB_prestador, c.nome_TB_cliente) AS nome_contato,
        
        -- IDENTIFICA A FOTO: Puxa a foto do prestador se o logado for cliente, e a do cliente se o logado for prestador
        -- (ATENÇÃO: Mude 'foto_TB_cliente' e 'foto_TB_prestadorPerfil' se os nomes das colunas no seu banco forem diferentes)
        IF(c.FK_id_TB_usuario = ?, pp.foto_TB_prestador, c.foto_TB_cliente) AS foto_contato,
        
        -- Identifica a ROLE do contato
        IF(c.FK_id_TB_usuario = ?, u_prestador.tipo_TB_usuario, u_cliente.tipo_TB_usuario) AS tipo_TB_usuario,
        
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

    -- Joins com a tabela de usuários para pegar as roles
    INNER JOIN tb_usuario u_cliente 
        ON c.FK_id_TB_usuario = u_cliente.PK_id_TB_usuario
    INNER JOIN tb_usuario u_prestador 
        ON pp.FK_id_TB_usuario = u_prestador.PK_id_TB_usuario

    -- Filtra os chats onde o usuário logado é o Cliente OU é o Prestador
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

        // Passamos o $usuarioId 6 vezes por conta dos 6 interrogações (?) na query
        $stmt->bind_param("iiiiii", $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId);
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

        // Ajustado para lida_TB_mensagem e FK_id_TB_remetente
        $sql = "UPDATE TB_mensagem SET lida_TB_mensagem = 1 WHERE FK_id_TB_SolicitacaoServico = ? AND FK_id_TB_remetente != ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $solicitacaoId, $usuarioId);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }
}
