<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Serviços</title>
</head>

<body>
    <?php if (!isset($servicos))
        $servicos = [];
    $servicosPendentes = array_filter($servicos, function ($servico) {
        if ($servico["status_TB_SolicitacaoServico"] == "pendente")
            return true;
    });
    ?>

    <div class="container">
        <h3> Solicitações de Serviços </h3>

        <?php foreach ($servicos as $servico): ?>
            <?php if ($servico["status_TB_SolicitacaoServico"] == "pendente")
                $servicosPendentes[] = $servico; ?>

            <div class="servicos-pendentes">
                <?php if ($servico["status_TB_SolicitacaoServico"] == "pendente"): ?>
                    <span> <?= $servico["nome_TB_servico"] ?> </span><br>
                    <span> Data: <?= $servico["data_agendamento_TB_SolicitacaoServico"] ?> </span><br>
                    <span> Valor: <?= $servico["valorTotal_TB_SolicitacaoServico"] ?> </span>
                    <form action="?route=solicitacao" method="post">
                        <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                        <input type="hidden" name="status" value="aceito">
                        <button type=" submit"> Aceitar </button>
                    </form>

                    <form action="?route=solicitacao" method="post">
                        <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                        <input type="hidden" name="status" value="cancelado">
                        <button type="submit"> Recusar </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div class="servicos-aceitos">
                <?php if ($servico["status_TB_SolicitacaoServico"] == "aceito"): ?>
                    <span>
                        <?= $servico["nome_TB_servico"] ?>
                    </span><br>
                    <span> Data:
                        <?= $servico["data_agendamento_TB_SolicitacaoServico"] ?>
                    </span><br>
                    <span> Valor:
                        <?= $servico["valorTotal_TB_SolicitacaoServico"] ?>
                    </span>

                    <form action="?route=solicitacao" method="post">
                        <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                        <input type="hidden" name="status" value="cancelado">
                        <button type="submit"> Cancelar </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (empty($servicosPendentes))
            echo "Sem novas solicitações..."; ?>

    </div>
</body>

</html>