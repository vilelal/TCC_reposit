<?php

require_once "app/conexao.php";

class AvaliacaoModel {
    public static function avaliar($nota, $user) {
        $conexao = Database::conectarBanco();
        $sql = "INSERT INTO TB_avaliacao (FK_id_TB_usuario, nota_TB_avaliacao)
        VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $user, $nota);
        $stmt->execute();

        $rows = $stmt->affected_rows;
        $conexao->close();
        $stmt->close();

        if ($rows == 0) return false;
        return true;
    }

    public static function getMediaAvaliacoes($id) {
        $conexao = Database::conectarBanco();
        $sql = "SELECT ROUND(AVG(nota_TB_avaliacao), 2) FROM TB_avaliacao
        WHERE FK_id_TB_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conexao->close();
        $avaliacoes = [];
        if ($line = $result->fetch_assoc()) $avaliacoes[] = $line;
        return $avaliacoes;
    }
}