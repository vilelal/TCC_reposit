<?php

require_once "app/conexao.php";

class AdminModel
{
    // 1. Buscar todos os usuários para listar no painel
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

    // 2. Banir/Deletar um usuário (pode ser usado tanto para o denunciado quanto para o denunciante)
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

    // 3. Busca todas as categorias cadastradas
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

    // 4. Insere uma nova categoria no banco de dados
    public static function criarCategoria($nomeCategoria)
    {
        $conexao = Database::conectarBanco();
        $sql = "INSERT INTO TB_categoria (nome_TB_categoria) VALUES (?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $nomeCategoria);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }

    // 5. Insere o serviço salvando a categoria junto
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

    // =========================================================================
    // NOVOS MÉTODOS: GESTÃO DE DENÚNCIAS
    // =========================================================================

    // 6. Listar todas as denúncias trazendo os e-mails do denunciante e do denunciado
    public static function listarTodasDenuncias()
    {
        $conexao = Database::conectarBanco();

        $sql = "SELECT 
                    d.PK_id_TB_denuncia,
                    d.FK_id_TB_denunciante,
                    d.FK_id_TB_denunciado,
                    d.FK_id_TB_solicitacao,
                    d.motivo_TB_denuncia,
                    d.descricao_TB_denuncia,
                    d.status_TB_denuncia,
                    d.data_TB_denuncia,
                    u_denunciante.email_TB_usuario AS email_denunciante,
                    u_denunciado.email_TB_usuario AS email_denunciado
                FROM tb_denuncia d
                INNER JOIN tb_usuario u_denunciante 
                    ON d.FK_id_TB_denunciante = u_denunciante.PK_id_TB_usuario
                INNER JOIN tb_usuario u_denunciado 
                    ON d.FK_id_TB_denunciado = u_denunciado.PK_id_TB_usuario
                ORDER BY d.data_TB_denuncia DESC";

        $result = $conexao->query($sql);

        $denuncias = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $denuncias[] = $row;
            }
        }

        $conexao->close();
        return $denuncias;
    }

    // 7. Atualizar o status da denúncia ('pendente', 'em_analise', 'resolvido', 'rejeitado')
    public static function atualizarStatusDenuncia($idDenuncia, $novoStatus)
    {
        $conexao = Database::conectarBanco();
        $sql = "UPDATE tb_denuncia SET status_TB_denuncia = ? WHERE PK_id_TB_denuncia = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $novoStatus, $idDenuncia);
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();
        return $sucesso;
    }

    // 8. Buscar os detalhes de uma denúncia específica pelo ID
    public static function buscarDenunciaPorId($idDenuncia)
    {
        $conexao = Database::conectarBanco();

        $sql = "SELECT 
                    d.*,
                    u_denunciante.email_TB_usuario AS email_denunciante,
                    u_denunciado.email_TB_usuario AS email_denunciado
                FROM tb_denuncia d
                INNER JOIN tb_usuario u_denunciante ON d.FK_id_TB_denunciante = u_denunciante.PK_id_TB_usuario
                INNER JOIN tb_usuario u_denunciado ON d.FK_id_TB_denunciado = u_denunciado.PK_id_TB_usuario
                WHERE d.PK_id_TB_denuncia = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $idDenuncia);
        $stmt->execute();
        $result = $stmt->get_result();
        $denuncia = $result->fetch_assoc();

        $stmt->close();
        $conexao->close();
        return $denuncia;
    }
}