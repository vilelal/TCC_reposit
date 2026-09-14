<?php

require_once "app/conexao.php";

class NotificacaoModel
{
    public static function enviarNotificacao($data)
    {
        $conexao = Database::conectarBanco();
        $sql = "INSERT INTO TB_notificacao (titulo_TB_notificacao, mensagem_TB_notificacao, FK_id_usuario)
        VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $data["titulo"], $data["mensagem"], $data["user_id"]);
        $stmt->execute();
        $linhasAfetadas = $conexao->affected_rows;
        $conexao->close();
        $stmt->close();
        
        if ($linhasAfetadas == 0) return false;
        return true;
    }

    public static function getNotificacoes($id)
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_notificacao WHERE FK_id_usuario = ?
        ORDER BY data_criacao_TB_notificacao DESC";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notificacoes = [];
        while ($line = $result->fetch_assoc()) {
            $notificacoes[] = $line;
        }
        return $notificacoes;
    }

    public static function notificacaoLida($id)
    {
        $conexao = Database::conectarBanco();
        $sql = "UPDATE TB_notificacao SET lida_TB_notificacao = 1
        WHERE FK_id_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $linhasAfetadas = $conexao->affected_rows;
        $conexao->close();
        $stmt->close();
        
        if ($linhasAfetadas == 0) return false;
        return true;
    }
}
