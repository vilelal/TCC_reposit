<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Prestador</title>
</head>
<body>
    nome: <?= $prestador["nome_TB_prestador"]?> <br>
    biografia: <?= $prestador["bio_TB_prestador"]?> <br>
    cidade: <?= $prestador["cidade_TB_prestadorPerfil"]?><br>
    media: <?= $mediaAvaliacoes ?><br>
    link foto: <?= $prestador["foto_TB_prestador"]?><br>
</body>
</html>