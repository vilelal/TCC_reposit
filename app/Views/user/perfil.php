<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="app/css/stylePerfil.css">
</head>

<body>

    <?php
    if (!isset($user))
        $user = [];
    ?>




    <div class="container">

        <div >
            <img class="img" src="app/css/img/logo.png" alt="Logo">
        </div>

        <div class="txt">
            <h1>Meu perfil</h1>
        </div>

        <div class="foto">

        </div>
        <h3> <?= $user["nome_TB_cliente"] ?? $user["nome_TB_prestador"] ?> </h3>
        <a href="?route=edit-perfil">Dados pessoais</a>
        <a href="?route=seguranca">Segurança</a>
        <a href="?route=meus-servicos">Meus serviços</a>

        <?php
            if ($_SESSION["tipo"]=="prestador" ) {
                echo "<a href='?route=dashboard'>Voltar</a>";
            }        
            else {
                echo "<a href='?route=home'>Voltar</a>";
            }
            ?>

        <a href="?route=logout">Sair da conta</a>
    </div>
</body>

</html>