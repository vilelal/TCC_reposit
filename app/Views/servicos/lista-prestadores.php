<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista</title>
</head>
<body>
    <?php foreach($prestadores as $prestador): ?>
        <h3> <?= $prestador["nome_TB_prestador"] ?> </h3>
        <form action="?route=solicitar" method="post">
            <input type="hidden" name="data" value="<?= $data ?>">
            <input type="hidden" name="servico" value="<?= $servico ?>">
            <input type="hidden" name="prestador" value="<?= $prestador["PK_id_TB_prestadorperfil"] ?>">
            <input type="hidden" name="valor" value="<?= $prestador["preco_customizado_TB_servico"] ?? $prestador["precoPadrao_TB_servico"] ?>">
            <button type="submit"> Solicitar </button>
        </form>
    <?php endforeach; ?>
</body>
</html>