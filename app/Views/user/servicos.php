<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Serviços</title>
</head>

<body>
    <?php if (!isset($servicos)) $servicos = [];
    $servicosPendentes = array_filter($servicos, function ($servico) {
        if ($servico["status_TB_SolicitacaoServico"] == "pendente")
            return true;
    });

    $servicosAceitos = array_filter($servicos, function ($servico) {
        if ($servico["status_TB_SolicitacaoServico"] == "aceito")
            return true;
    });
    ?>

    <div class="container">
        <h3> Solicitações de Serviços </h3>

        <?php if (empty($servicosPendentes))
            echo "Sem novas solicitações..."; ?>

        <?php foreach ($servicosPendentes as $servico): ?>
            <div class="servicos-pendentes">
                <span> Prestador Solicitado: <?= $servico["nome_TB_prestador"] ?> </span> <br>
                <span> <?= $servico["nome_TB_servico"] ?> </span><br>
                <span> Data: <?= $servico["data_agendamento_TB_SolicitacaoServico"] ?> </span><br>
                <span> Valor: <?= $servico["valorTotal_TB_SolicitacaoServico"] ?> </span>

                <form action="?route=solicitacao" method="post">
                    <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                    <input type="hidden" name="status" value="cancelado">
                    <input type="hidden" name="prestador" value="<?= $servico["PK_id_TB_prestadorPerfil"] ?>">
                    <input type="hidden" name="servico" value="<?= $servico["nome_TB_servico"] ?>">
                    <button type="submit"> Cancelar </button>
                </form>
            </div>
        <?php endforeach; ?>

        <h3> Serviços Aceitos </h3>

        <?php if (empty($servicosAceitos))
            echo "Sem serviços aceitos..."; ?>

        <?php foreach ($servicosAceitos as $servico): ?>
            <div class="servicos-aceitos">
                <span> Prestador: <?= $servico["nome_TB_prestador"] ?> </span> <br>
                <span> <?= $servico["nome_TB_servico"] ?> </span><br>
                <span> Data: <?= $servico["data_agendamento_TB_SolicitacaoServico"] ?> </span><br>
                <span> Valor: <?= $servico["valorTotal_TB_SolicitacaoServico"] ?> </span> <br>
                <span> Codigo de Confirmação: <?= $servico["pin_TB_SolicitacaoServico"] ?> </span>

                <form action="?route=solicitacao" method="post">
                    <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                    <input type="hidden" name="status" value="cancelado">
                    <input type="hidden" name="prestador" value="<?= $servico["PK_id_TB_prestadorPerfil"] ?>">
                    <input type="hidden" name="servico" value="<?= $servico["nome_TB_servico"] ?>">
                    <button type="submit"> Cancelar </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    </div>
</body>

</html>