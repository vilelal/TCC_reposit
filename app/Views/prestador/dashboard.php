<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app/css/dashboard.css">
    <title>Dashboard</title>
</head>
<body>
    <?php if (!isset($user)) $user = [] ?>
        <div class="menu">
            <div class="conteudo">
                <div class="titulo">
              <a href="?route=">
                <img src="app/css/img/logo.png" alt="" class="logo">
                </a>
        <h1 class="nome_empresa">FastService</h1> </div>
       
                 <a class="menu-a" href="?route=dashboard"><img class="casa" src="app/css/img_dashboard/icone-sino2.png" alt=""><h2>Inicio</h2></a>
                  <a class="menu-a" href="?route="><img src="app/css/img_dashboard/icone-traco.png"  alt=""><h2>Relatório</h2></a>
                <a class="menu-a" href="?route=lista-servicos"><img src="app/css/img_dashboard/icone-traco2.png"  alt=""><h2>Serviços</h2></a>
                <a class="menu-a" href="?route=perfil"><img src="<?= !empty($user['foto_TB_usuario']) ? $user['foto_TB_usuario'] : ($_SESSION['foto'] ?? 'app/css/img/default-user.png') ?>" alt="Foto do usuário"><h2>Perfil</h2></a>
                </div>
        </div>
    <div class="area-direita">
       <header class="header">
        <!--d
-->
            <div class="header-right">
                <a href="">
                    <img src="app/css/img/chat.png" alt="" class="chat">
                </a>
                <a href="?route=notificacoes">
                    <img src="app/css/img_dashboard/icone-sino.png" alt="" class="chat">
                </a>
                <img src="<?= !empty($user['foto_TB_usuario']) ? $user['foto_TB_usuario'] : ($_SESSION['foto'] ?? 'app/css/img/default-user.png') ?>" alt="Foto do usuário">
                <?php
                    if (isset($_SESSION["id"])) {
                        echo "<h3 class='user-name'>{$_SESSION['nome']}</h3>";
                    }
                ?>
            </div><img class="verificado"src="app/css/img_dashboard/icone-check.png" alt="">
        </header>
        <div class="cards">
            <div class="card">
                <h4>Faturamento</h4>
            </div>
            <div class="card">
                <h4>Serviços prestados</h4>
                <h3> <?= $user["total_servicos"] ?> </h3>
            </div>
            <div class="card">
                <h4>média das avaliações</h4>
                <h3> <?= $avaliacoes["media_avaliacoes"] ?? 0 ?> </h3>
            </div>
        </div>
    </div>
</body>
 
</html>
</body>
</html>