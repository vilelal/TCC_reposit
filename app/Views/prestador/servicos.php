<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços</title>
</head>
<body>
    <?php if (!isset($servicos)) $servicos = []?>
    <div class="container">
        <?php foreach ($servicos as $servico): ?>
        <div class="servico">
            <span> <?= $servico["nome_TB_servico"] ?> </span>
            <form action="?aceitar" method="post">
                <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                <input type="hidden" name="status" value="aceito>
                <button type="submit"> Aceitar </button>
            </form>
            
            <form action="?aceitar" method="post">
                <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                <input type="hidden" name="status" value="recusado">
                <button type="submit"> Recusar </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>