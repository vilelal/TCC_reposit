<?php

require_once __DIR__ . "/../conexao.php";

class PrestadorModel
{
    public static function editServicos($data)
    {
        try {
            // limpa servicos cadastrados do prestador 
            $conexao = Database::conectarBanco();
            $conexao->begin_transaction();
            $sql = "DELETE FROM TB_prestadorServico WHERE FK_id_TB_prestadorPerfil = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("i", $_SESSION["id_prestador"]);
            $stmt->execute();
            $stmt->close();
    
            // adiciona nova lista de servicos
            if (isset($data["servicos"])) {
                foreach ($data["servicos"] as $servicoId) {
                    $sql = "INSERT INTO TB_prestadorServico
                        (FK_id_TB_servico, FK_id_TB_prestadorPerfil)
                        VALUES (?, ?)"; //talvez tenha preco customizado, por enquanto ignorado
                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param("ii", $servicoId, $_SESSION["id_prestador"]);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $conexao->commit();
            $conexao->close();
            return true;

        } catch (Exception $err) {
            if (isset($conexao) && $conexao instanceof mysqli) {
                $conexao->rollback();
                $conexao->close();
            }
        }
        
    }
}
