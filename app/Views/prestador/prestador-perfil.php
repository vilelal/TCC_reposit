<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Prestador</title>
</head>

<body>
    <?php if (!isset($prestador)) $prestador = [] ?>
    nome: <?= $prestador[0]["nome_TB_prestador"] ?? "" ?> <br>
    biografia: <?= $prestador[0]["bio_TB_prestador"] ?? "" ?> <br>
    cidade: <?= $prestador[0]["cidade_TB_prestadorPerfil"] ?? "" ?><br>
    media: <?= $mediaAvaliacoes ?? "" ?><br>
    link foto de perfil: <?= $prestador[0]["foto_TB_prestador"] ?? "" ?><br>
    <?php foreach ($prestador as $prest): ?>
        <img src="<?= $prest["foto_TB_repositorio"] ?>">
    <?php endforeach; ?>
</body>
</html>