<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="app/css/dashboard.css">
         <link rel="stylesheet" href="app/css/servico.css">
    <title>Serviços</title>
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
    <?php if (!isset($servicos))
        $servicos = [];
    $servicosPendentes = array_filter($servicos, function ($servico) {
        if ($servico["status_TB_SolicitacaoServico"] == "pendente")
            return true;
    });

    $servicosAceitos = array_filter($servicos, function ($servico) {
        if ($servico["status_TB_SolicitacaoServico"] == "aceito")
            return true;
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
            echo "Sem serviços aceit..."; ?>

        <?php foreach ($servicosAceitos as $servico): ?>
            <div class="servicos-aceitos">
                <span> <?= $servico["nome_TB_servico"] ?> </span><br>
                <span> Data: <?= $servico["data_agendamento_TB_SolicitacaoServico"] ?> </span><br>
                <span> Valor: <?= $servico["valorTotal_TB_SolicitacaoServico"] ?> </span>

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

</html>