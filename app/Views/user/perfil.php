<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
</head>
<body>
    <?php
    if (!isset($user)) $user = [];
    ?>
    <div class="container" style="display: flex; flex-direction: column">
        <div class="foto" style="background-color:aqua; height: 100px; width: 100px; border-radius: 100%">

        </div>
        <h3> <?= $user["nome_TB_cliente"] ?? $user["nome_TB_prestador"] ?> </h3>
        <a href="?route=edit-perfil">dados pessoais</a>
        <a href="?route=seguranca">segurança</a>
        <a href="?route=meus-servicos">meus serviços</a>
        <a href="?route=logout">sair</a>
    </div>
</body>
</html>