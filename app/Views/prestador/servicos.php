<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Serviços</title>
</head>

<body>
    <?php
    if (!isset($servicos)) $servicos = [];
    $servicosPendentes = array_filter($servicos, function ($servico) {
        if ($servico["status_TB_SolicitacaoServico"] == "pendente") return true;
    });

    $servicosAceitos = array_filter($servicos, function ($servico) {
        if ($servico["status_TB_SolicitacaoServico"] == "aceito") return true;
    });

    ?>

    <div class="container">
        <h3> Solicitações de Serviços </h3>

        <?php if (empty($servicosPendentes))
            echo "Sem novas solicitações..."; ?>

        <?php foreach ($servicosPendentes as $servico): ?>
            <div class="servicos-pendentes">
                <span> <?= $servico["nome_TB_servico"] ?> </span><br>
                <span> Data: <?= $servico["data_agendamento_TB_SolicitacaoServico"] ?> </span><br>
                <span> Valor: <?= $servico["valorTotal_TB_SolicitacaoServico"] ?> </span>
                <form action="?route=solicitacao" method="post">
                    <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                    <input type="hidden" name="status" value="aceito">
                    <input type="hidden" name="cliente" value="<?= $servico["FK_id_TB_cliente"] ?>">
                    <input type="hidden" name="servico" value="<?= $servico["nome_TB_servico"] ?>">
                    <button type=" submit"> Aceitar </button>
                </form>

                <form action="?route=solicitacao" method="post">
                    <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                    <input type="hidden" name="status" value="cancelado">
                    <input type="hidden" name="cliente" value="<?= $servico["FK_id_TB_cliente"] ?>">
                    <input type="hidden" name="servico" value="<?= $servico["nome_TB_servico"] ?>">
                    <button type="submit"> Recusar </button>
                </form>
            </div>
        <?php endforeach; ?>

        <h3> Serviços Aceitos </h3>

        <?php if (empty($servicosAceitos))
            echo "Sem serviços aceitos..."; ?>

        <?php foreach ($servicosAceitos as $servico):
        $agendamento = new DateTime($servico["data_agendamento_TB_SolicitacaoServico"]);
        $dataAtual = new DateTime();
        ?>

            <div class="servicos-aceitos">
                <span> <?= $servico["nome_TB_servico"] ?> </span><br>
                <span> Data: <?= $servico["data_agendamento_TB_SolicitacaoServico"] ?> </span><br>
                <span> Valor: <?= $servico["valorTotal_TB_SolicitacaoServico"] ?> </span> <br>

                <button commandFor="modal" command="show-modal"> Concluir Serviço </button>

                <dialog id="modal" closedby="any">
                    <h3> Digite o codigo passado pelo cliente! </h3>
                    <form action="?route=concluir-servico" method="post">
                        <input type="text" placeholder="0000" maxlength="4" name="pin"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                        <button type="submit"> Concluir Serviço </button>
                    </form>
                </dialog>

                <form action="?route=solicitacao" method="post">
                    <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                    <input type="hidden" name="status" value="cancelado">
                    <input type="hidden" name="cliente" value="<?= $servico["FK_id_TB_cliente"] ?>">
                    <input type="hidden" name="servico" value="<?= $servico["nome_TB_servico"] ?>">
                    <button type="submit"> Cancelar </button>
                </form>

            </div>
        <?php endforeach; ?>
    </div>

    </div>
</body>
<script>
    function concluirServico() {
        const form = document.getElementById("concluir")
        form.style.display = "block"
    }
</script>

</html>