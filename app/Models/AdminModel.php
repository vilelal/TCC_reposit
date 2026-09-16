<?php

require_once "app/conexao.php";

class AdminModel
{

    //1. Buscar todos os usuários para listar no painel
    public static function listarTodosUsuarios()
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT PK_id_TB_usuario, email_TB_usuario, tipo_TB_usuario FROM tb_usuario ORDER BY PK_id_TB_usuario DESC";

        $result = $conexao->query($sql);
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        $conexao->close();
        return $usuarios;
    }

    // 2. Banir/Deletar um usuário
    public static function banirUsuario($idUsuario)
    {
        $conexao = Database::conectarBanco();
        $sql = "DELETE FROM tb_usuario WHERE PK_id_TB_usuario = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }

 
    // Busca todas as categorias cadastradas
    public static function listarTodasCategorias()
    {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_categoria ORDER BY nome_TB_categoria ASC";
        $result = $conexao->query($sql);

        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
        $conexao->close();
        return $categorias;
    }

    // Insere o serviço salvando a categoria junto
    public static function criarServicoComCategoria($nomeServico, $idCategoria)
    {
        $conexao = Database::conectarBanco();
        $sql = "INSERT INTO TB_servico (nome_TB_servico, FK_id_TB_categoria) VALUES (?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $nomeServico, $idCategoria);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }
}
