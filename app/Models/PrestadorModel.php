<?php

require_once __DIR__ . "/../conexao.php";

class PrestadorModel
{
    public static function getPrestadorById($id) {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_prestadorPerfil
        INNER JOIN TB_usuario ON FK_id_TB_usuario = PK_id_TB_usuario
        WHERE FK_id_TB_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prestador = $result->fetch_assoc();
        return $prestador;
    }
    public static function editPerfil($data)
    {
        
    }
}