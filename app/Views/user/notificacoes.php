<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificacoes</title>
    <link rel="stylesheet" href="app/css/notificacoes.css">
    <link rel="stylesheet" href="app/css/dashboard.css">
</head>
<body>
    <div class="menu">
            <div class="conteudo">
                <div class="titulo">
              <a href="?route=">
                <img src="app/css/img/logo.png" alt="" class="logo">
                </a>
        <h1 class="nome_empresa">FastService</h1> </div>
       
                 <a class="menu-a" href="?route=dashboard"><img class="casa" src="app/css/img_dashboard/icone-sino2.png" alt=""><h2>Inicio</h2></a>
                  <a class="menu-a" href="?route=dashboard"><img src="app/css/img_dashboard/icone-traco.png"  alt=""><h2>Relatório</h2></a>
                <a class="menu-a" href="?route=lista-servicos"><img src="app/css/img_dashboard/icone-traco2.png"  alt=""><h2>Serviços</h2></a>
                <a class="menu-a" href="?route=perfil"><img src="" alt=""><h2>Perfil</h2></a>
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
                <img src="" alt="">
                <?php
                    if (isset($_SESSION["id"])) {
                        echo "<h3 class='user-name'>{$_SESSION['nome']}</h3>";
                    }
                ?>
            </div><img class="verificado"src="app/css/img_dashboard/icone-check.png" alt="">
        </header>

    <?php if (!isset($notificacoes)) $notificacoes = []; ?>
    
    <div class="container">
        <!-- Se o array NÃO estiver vazio  -->
        <?php if (!empty($notificacoes)): ?>
            <?php foreach ($notificacoes as $notificacao): ?>
                <div class="card-notificacao <?= (!$notificacao['lida_TB_notificacao']) ? 'nova-notificacao' : '' ?>">
                    <!-- Interpolação dos dados da notificação -->
                    <h3><?= htmlspecialchars($notificacao['titulo_TB_notificacao'] ?? '') ?></h3>
                    <p><?= htmlspecialchars($notificacao['mensagem_TB_notificacao'] ?? '') ?></p>
                    <small><?= htmlspecialchars($notificacao['data_criacao_TB_notificacao'] ?? '') ?></small>
                </div>
            <?php endforeach; ?>
        
        <!-- Se o array estiver vazio -->
        <?php else: ?>
            <p>Não tens nenhuma notificação nova.</p>
        <?php endif; ?>
    </div>
</body>
</html>
