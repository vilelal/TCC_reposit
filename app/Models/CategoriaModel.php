<?php

require_once __DIR__ . "/../conexao.php";

class CategoriaModel {
    public static function getCategorias() {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_categoria";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $data = $stmt->get_result();
        $conexao->close();
        $stmt->close();
        $categorias = [];
        while ($line = $data->fetch_assoc()) {
            $categorias[] = $line;
        }
        return $categorias;
    }
}