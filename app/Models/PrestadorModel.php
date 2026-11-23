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

    public static function getPerfil($id) {
        $conexao = Database::conectarBanco();
        $sql = "SELECT * FROM TB_prestadorPerfil
        LEFT JOIN TB_repositorio
        ON FK_id_TB_prestador = PK_id_TB_prestadorPerfil
        WHERE PK_id_TB_prestadorPerfil = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prestador = [];
        while ($row = $result->fetch_assoc()) {
            $prestador[] = $row;
        }
        $conexao->close();
        $stmt->close();
        return $prestador;
    }

    public static function salvarCaminhoImagem($prestador, $caminho) {
        $conexao = Database::conectarBanco();
        $sql = "INSERT INTO TB_repositorio (FK_id_TB_prestador, foto_TB_repositorio)
        VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("is", $prestador, $caminho);
        $stmt->execute();
        
        $stmt->close();
        $conexao->close();
    }
   
// 1. DADOS PARA O GRÁFICO DE 7 DIAS
    public static function obterDados7Dias($idPrestador) {
        $conexao = Database::conectarBanco();
        $rotulos = [];
        $valores = [];
        $totalServicos = 0;
        $faturamentoTotal = 0;

        $diasTraduzidos = ['Sun' => 'Dom', 'Mon' => 'Seg', 'Tue' => 'Ter', 'Wed' => 'Qua', 'Thu' => 'Qui', 'Fri' => 'Sex', 'Sat' => 'Sáb'];

        for ($i = 6; $i >= 0; $i--) {
            $dataIso = date('Y-m-d', strtotime("-$i days"));
            $diaIngles = date('D', strtotime($dataIso));
            $rotulos[] = $diasTraduzidos[$diaIngles] ?? $diaIngles;

            $sql = "SELECT COALESCE(SUM(s.valorTotal_TB_SolicitacaoServico), 0) as faturamento, COUNT(*) as qtd
                    FROM TB_SolicitacaoServico s
                    JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
                    WHERE ps.FK_id_TB_prestadorPerfil = ? 
                      AND s.status_TB_SolicitacaoServico = 'concluido'
                      AND DATE(s.data_agendamento_TB_SolicitacaoServico) = ?";
            
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("is", $idPrestador, $dataIso);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $val = (float)($res['faturamento'] ?? 0);
            $qtd = (int)($res['qtd'] ?? 0);

            $valores[] = $val;
            $totalServicos += $qtd;
            $faturamentoTotal += $val;
        }

        $conexao->close();
        return [
            'unidade' => 'dia',
            'rotulos' => $rotulos,
            'valores' => $valores,
            'servicos' => $totalServicos,
            'faturamento' => $faturamentoTotal
        ];
    }

    // 2. DADOS PARA O GRÁFICO DE 30 DIAS
    public static function obterDados30Dias($idPrestador) {
        $conexao = Database::conectarBanco();
        $rotulos = ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'];
        $valores = [0, 0, 0, 0];
        $totalServicos = 0;
        $faturamentoTotal = 0;

        for ($s = 3; $s >= 0; $s--) {
            $inicioDias = ($s + 1) * 7;
            $fimDias = $s * 7;

            $sql = "SELECT COALESCE(SUM(s.valorTotal_TB_SolicitacaoServico), 0) as faturamento, COUNT(*) as qtd
                    FROM TB_SolicitacaoServico s
                    JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
                    WHERE ps.FK_id_TB_prestadorPerfil = ? 
                      AND s.status_TB_SolicitacaoServico = 'concluido'
                      AND s.data_agendamento_TB_SolicitacaoServico >= DATE_SUB(NOW(), INTERVAL ? DAY)
                      AND s.data_agendamento_TB_SolicitacaoServico < DATE_SUB(NOW(), INTERVAL ? DAY)";
            
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("iii", $idPrestador, $inicioDias, $fimDias);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $val = (float)($res['faturamento'] ?? 0);
            $qtd = (int)($res['qtd'] ?? 0);

            $valores[3 - $s] = $val;
            $totalServicos += $qtd;
            $faturamentoTotal += $val;
        }

        $conexao->close();
        return [
            'unidade' => 'semana',
            'rotulos' => $rotulos,
            'valores' => $valores,
            'servicos' => $totalServicos,
            'faturamento' => $faturamentoTotal
        ];
    }

    // 3. DADOS PARA O GRÁFICO DE 12 MESES
    public static function obterDados12Meses($idPrestador) {
        $conexao = Database::conectarBanco();
        $rotulos = [];
        $valores = [];
        $totalServicos = 0;
        $faturamentoTotal = 0;

        $mesesTraduzidos = [
            '01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', 
            '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', 
            '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'
        ];

        for ($i = 11; $i >= 0; $i--) {
            $mesAno = date('Y-m', strtotime("-$i months"));
            list($ano, $mesNum) = explode('-', $mesAno);
            $rotulos[] = $mesesTraduzidos[$mesNum];

            $sql = "SELECT COALESCE(SUM(s.valorTotal_TB_SolicitacaoServico), 0) as faturamento, COUNT(*) as qtd
                    FROM TB_SolicitacaoServico s
                    JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
                    WHERE ps.FK_id_TB_prestadorPerfil = ? 
                      AND s.status_TB_SolicitacaoServico = 'concluido'
                      AND YEAR(s.data_agendamento_TB_SolicitacaoServico) = ?
                      AND MONTH(s.data_agendamento_TB_SolicitacaoServico) = ?";
            
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("iss", $idPrestador, $ano, $mesNum);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $val = (float)($res['faturamento'] ?? 0);
            $qtd = (int)($res['qtd'] ?? 0);

            $valores[] = $val;
            $totalServicos += $qtd;
            $faturamentoTotal += $val;
        }

        $conexao->close();
        return [
            'unidade' => 'mês',
            'rotulos' => $rotulos,
            'valores' => $valores,
            'servicos' => $totalServicos,
            'faturamento' => $faturamentoTotal
        ];
    }

    // 4. RANKING DE SERVIÇOS
    public static function buscarRankingServicos($idPrestador) {
        $conexao = Database::conectarBanco();
        $sql = "SELECT serv.nome_TB_servico as nome, COUNT(s.PK_id_TB_SolicitacaoServico) as qtd
                FROM TB_SolicitacaoServico s
                JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
                JOIN TB_servico serv ON s.FK_id_TB_servico = serv.PK_id_TB_servico
                WHERE ps.FK_id_TB_prestadorPerfil = ?
                  AND s.status_TB_SolicitacaoServico = 'concluido'
                GROUP BY serv.PK_id_TB_servico, serv.nome_TB_servico
                ORDER BY qtd DESC
                LIMIT 4";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $idPrestador);
        $stmt->execute();
        $result = $stmt->get_result();
        $ranking = [];
        while ($row = $result->fetch_assoc()) {
            $ranking[] = $row;
        }
        $stmt->close();
        $conexao->close();
        return $ranking;
    }


    public static function buscarUltimosServicos($idPrestador, $limite = 4)
{
    $conexao = Database::conectarBanco();
    $sql = "SELECT
                serv.nome_TB_servico AS nome,
                DATE_FORMAT(s.data_agendamento_TB_SolicitacaoServico, '%d/%m') AS data,
                s.valorTotal_TB_SolicitacaoServico AS valor,
                s.status_TB_SolicitacaoServico AS status
            FROM TB_SolicitacaoServico s
            JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
            JOIN TB_servico serv ON s.FK_id_TB_servico = serv.PK_id_TB_servico
            WHERE ps.FK_id_TB_prestadorPerfil = ?
            ORDER BY s.data_agendamento_TB_SolicitacaoServico DESC
            LIMIT ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $idPrestador, $limite);
    $stmt->execute();
    $result = $stmt->get_result();

    $ultimos = [];
    while ($row = $result->fetch_assoc()) {
        $row['valor'] = (float) ($row['valor'] ?? 0);
        $ultimos[] = $row;
    }

    $stmt->close();
    $conexao->close();
    return $ultimos;
}

public static function obterMediaAvaliacao($idPrestador)
{
    $conexao = Database::conectarBanco();
    $sql = "SELECT COALESCE(ROUND(AVG(a.nota_TB_avaliacao), 1), 0) AS media
            FROM TB_avaliacao a
            JOIN TB_prestadorPerfil pp ON a.FK_id_TB_usuario = pp.FK_id_TB_usuario
            WHERE pp.PK_id_TB_prestadorPerfil = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $idPrestador);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conexao->close();

    return (float) ($row['media'] ?? 0);
}

public static function obterTaxaAceite($idPrestador, $dias)
{
    $conexao = Database::conectarBanco();
    $sql = "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN s.status_TB_SolicitacaoServico IN ('aceito', 'em_andamento', 'concluido') THEN 1 ELSE 0 END) AS aceitos
            FROM TB_SolicitacaoServico s
            JOIN TB_prestadorServico ps ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico
            WHERE ps.FK_id_TB_prestadorPerfil = ?
              AND s.data_agendamento_TB_SolicitacaoServico >= DATE_SUB(NOW(), INTERVAL ? DAY)";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $idPrestador, $dias);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conexao->close();

    $total = (int) ($row['total'] ?? 0);
    $aceitos = (int) ($row['aceitos'] ?? 0);

    if ($total === 0) {
        return 0;
    }

    return (int) round(($aceitos / $total) * 100);
}

    // 5. ÚLTIMOS SERVIÇOS
//    public static function servicosConcluidos($idprestador, $diasSelecionados)
//     {
//     $conexao = Database::conectarBanco();
//     $sql = "SELECT 
//                 COUNT(*) AS total_concluidos,
//                 SUM(s.valorTotal_TB_SolicitacaoServico) AS faturamento_total
//             FROM TB_SolicitacaoServico s
//             JOIN TB_prestadorServico ps 
//             ON s.FK_id_TB_prestadorServico = ps.PK_id_TB_prestadorServico 
//             WHERE ps.FK_id_TB_prestadorPerfil = ? 
//             AND s.status_TB_SolicitacaoServico = 'concluido'
//             AND s.data_agendamento_TB_SolicitacaoServico >= DATE_SUB(NOW(), INTERVAL ? DAY);";  //calcula os dias

//     $stmt = $conexao->prepare($sql);
//     $stmt->bind_param("ii", $idprestador, $diasSelecionados );
//     $stmt->execute();
//     $resultado = $stmt->get_result();
//     $dados = $resultado->fetch_assoc();
//     $stmt->close();
    
//     return $dados; //retorna total de concluidos tipo 5

//     }
} 

