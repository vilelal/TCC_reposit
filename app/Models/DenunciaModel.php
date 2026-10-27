<?php
require_once __DIR__ . "/../conexao.php";
class DenunciaModel {

    public static function criarDenuncia($idDenunciante, $idDenunciado, $idSolicitacao, $motivo, $descricao) {
        $conexao = Database::conectarBanco();

        $sql = "INSERT INTO tb_denuncia 
                (FK_id_TB_denunciante, FK_id_TB_denunciado, FK_id_TB_solicitacao, motivo_TB_denuncia, descricao_TB_denuncia) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iiiss", $idDenunciante, $idDenunciado, $idSolicitacao, $motivo, $descricao);
        
        $sucesso = $stmt->execute();

        $stmt->close();
        $conexao->close();

        return $sucesso;
    }
}