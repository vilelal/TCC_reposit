<?php

require_once __DIR__ . "/../conexao.php";

class ServiceModel
{

public static function listarCategorias() {
        $conexao = Database::conectarBanco();
        
        $sql = "SELECT PK_id_TB_categoria, nome_TB_categoria FROM TB_categoria";
        $result = $conexao->query($sql);
        
        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
        
        $conexao->close();
        return $categorias;
    }

public static function criarSolicitacao($data) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conexao = Database::conectarBanco();
        
        $sql = "INSERT INTO TB_SolicitacaoServico (FK_id_TB_cliente, FK_id_TB_prestadorServico, data_agendamento_TB_SolicitacaoServico, status_TB_SolicitacaoServico , valorTotal_TB_SolicitacaoServico) VALUES (?, ?, ?,'Pendente', ?)";
        $stmt = $conexao->prepare($sql);
        
        $stmt->bind_param("iidss", 
            $data["FK_id_TB_cliente"], 
            $data["FK_id_TB_prestadorServico"], 
            $data["data_agendamento"],
            $data["valor_total"]
        );
        
        $stmt->execute();
        $stmt->close();
        $conexao->close();

        return true;
    } catch (Exception $err) {
        return false;
    }
}

public static function buscarPrestadoresPorCategoriaEEspacos($categoriaId, $clienteId) {
    $conexao = Database::conectarBanco();

    // Exemplo de query que traz o prestador, o serviço e a cidade do prestador
    $sql = "SELECT p.PK_id_TB_prestadorPerfil, p.nome_TB_prestador, p.cidade_TB_prestadorPerfil, 
                   s.nome_TB_servico, s.precoPadrao_TB_servico, ps.PK_id_TB_prestadorServico, 
                   ps.preco_customizado_TB_prestadorServico
            FROM TB_prestadorServico ps
            JOIN TB_servico s ON ps.FK_id_TB_servico = s.PK_id_TB_servico
            JOIN TB_prestadorPerfil p ON ps.FK_id_TB_prestadorPerfil = p.PK_id_TB_prestadorPerfil
            WHERE s.FK_id_TB_categoria = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $categoriaId);
    $stmt->execute();
    $result = $stmt->get_result();

    $prestadores = [];
    while ($row = $result->fetch_assoc()) {
        $prestadores[] = $row;
    }

    $stmt->close();
    $conexao->close();

    return $prestadores;
}   


//Ver se vai utilizar isso depois

// public static function AddService($data)
// {
//     // Ativa o disparo de exceções no mysqli para entrar no catch em caso de erro
//     mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

//     try {
//         $conexao = Database::conectarBanco();

//         // Inicia uma transação
//         $conexao->begin_transaction();

//         // Inserção do Serviço com a Chave Estrangeira
//         $sql = "INSERT INTO TB_servico (PK_id_TB_servico, FK_id_TB_categoria, nome_TB_servico, descricao_TB_servico, precoPadrao_TB_servico, orcamento_TB_servico) VALUES (?, ?, ?, ?, ?, ?)";
//         $stmt = $conexao->prepare($sql);
        
//         // Ajustado para "iissdd" (6 parâmetros correspondentes aos 6 placeholders)
//         $stmt->bind_param("iissdd", 
//             $data["PK_id_TB_servico"], 
//             $data["id_categoria"], 
//             $data["nome_servico"], 
//             $data["descricao_servico"], 
//             $data["precoPadrao_servico"], 
//             $data["orcamento_servico"]
//         );
        
//         $stmt->execute();
//         $stmt->close();

//         // Confirma a transação no banco
//         $conexao->commit();
//         $conexao->close();

//     } catch (Exception $err) {
//         // Desfaz alterações no banco caso algo falhe
//         if (isset($conexao) && $conexao instanceof mysqli) {
//             $conexao->rollback();
//             $conexao->close();
//         }

//         die("Erro no cadastro: " . $err->getMessage());
//     }
// }
}