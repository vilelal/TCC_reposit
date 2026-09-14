<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app/css/dashboard.css">
    <title>Dashboard</title>
</head>
<body>  
        <div class="menu">
            <div class="conteudo">
                <div class="titulo">
              <a href="?route=">
                <img src="app/css/img/logo.png" alt="" class="logo">
                </a>
        <h1 class="nome_empresa">FastService</h1> </div>
       
                 <a class="menu-a" href="?route=dashboard"><img class="casa" src="app/css/img_dashboard/icone-casa.png" alt=""><h2>Inicio</h2></a>
                  <a class="menu-a" href="?route=dashboard"><img src="" alt=""><h2>Relatório</h2></a>
                <a class="menu-a" href="?route=lista-servicos"><img src="" alt=""><h2>Serviços</h2></a>
                <a class="menu-a" href="?route=perfil"><img src="" alt=""><h2>Perfil</h2></a>
                </div>
        </div>
       <header class="header">
            <div class="header-right">
                <a href="">
                    <img src="app/css/img/chat.png" alt="" class="chat">
                </a>
                <a href="">
                    <img src="app/css/img/icone-sino.png" alt="" class="chat">
                </a>
                <img src="" alt="">
                <?php
                    if (isset($_SESSION["id"])) {
                        echo "<h3 class='user-name'>{$_SESSION['nome']}</h3>";
                    }
                ?>
            </div>
        </header>
        <div class="conteiner">
            
        </div>

</body>
</html>