<?php
$logado = isset($_SESSION["id"]);
$tipo = $_SESSION["tipo"] ?? "";
$nome = htmlspecialchars($_SESSION["nome"] ?? "", ENT_QUOTES, "UTF-8");

$ehPrestador = $tipo === "prestador";
$ehAdmin = $tipo === "Admin";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
      <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/servico-user.css">

    <title>Serviços</title>
</head>

<body>
<?php

if (!isset($servicos)) {
    $servicos = [];
}

$servicosPendentes = array_filter($servicos, function ($servico) {
    return $servico["status_TB_SolicitacaoServico"] == "pendente";
});

$servicosAceitos = array_filter($servicos, function ($servico) {
    return $servico["status_TB_SolicitacaoServico"] == "aceito";
});

?>
<header class="site-header">
    <div class="header-wrap">

        <a href="?route=home" class="brand" aria-label="FastService - página inicial">
            <img src="app/css/img/logo.png" alt="FastService">
            <span>FastService</span>
        </a>

        <nav class="nav-links" aria-label="Principal">
            <a href="?route=home#como-funciona">Como funciona</a>
            <a href="?route=home#categorias">Categorias</a>
               <?php if (!$ehPrestador): ?>
                            <a href="?route=solicitar-servico">Solicitar um serviço</a>
                <?php endif; ?>
        </nav>

        <div class="nav-actions">

            <?php if ($logado): ?>

                <span class="saudacao">
                    Olá, <?= $nome ?>
                </span>

                <a href="?route=chat" class="btn-header btn-ghost btn-sm hide-sm">
                    Chat
                </a>

                <?php if ($ehAdmin): ?>

                    <a href="?route=painel-admin" class="btn-header btn-ghost btn-sm hide-sm">
                        Painel
                    </a>

                <?php elseif ($ehPrestador): ?>

                    <a href="?route=dashboard" class="btn-header btn-primary btn-sm">
                        Meu painel
                    </a>

                <?php else: ?>

                    <a href="?route=perfil" class="btn-header btn-primary btn-sm">
                        Meu perfil
                    </a>

                <?php endif; ?>

                <a href="?route=logout" class="btn-header btn-ghost btn-sm">
                    Sair
                </a>

            <?php else: ?>

                <a href="?route=login-form" class="btn-header btn-ghost btn-sm">
                    Entrar
                </a>

                <a href="?route=prestador-form" class="btn-header btn-primary btn-sm">
                    Seja um profissional
                </a>

            <?php endif; ?>

        </div>

    </div>
</header>
<div class="conteudo">

    <div class="pagina-cab">
        <h2>Serviços</h2>
        <p>Responda às solicitações e acompanhe os serviços que você solicitou.</p>
    </div>


    <!-- ABAS -->

    <div class="abas">

        <button type="button"
                class="aba ativa"
                data-alvo="painel-pendentes">

            Solicitações

            <span class="contador">
                <?= count($servicosPendentes) ?>
            </span>

        </button>


        <button type="button"
                class="aba"
                data-alvo="painel-aceitos">

            Serviços aceitos

            <span class="contador">
                <?= count($servicosAceitos) ?>
            </span>

        </button>

    </div>


    <!-- SERVIÇOS PENDENTES -->

    <div class="painel ativo" id="painel-pendentes">

        <?php if (empty($servicosPendentes)): ?>

            <p class="vazio">
                Sem novas solicitações...
            </p>

        <?php endif; ?>


        <?php foreach ($servicosPendentes as $servico): ?>

<<<<<<< Updated upstream
                <form action="?route=solicitacao" method="post">
                    <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                    <input type="hidden" name="status" value="cancelado">
                    <input type="hidden" name="prestador" value="<?= $servico["PK_id_TB_prestadorPerfil"] ?>">
                    <input type="hidden" name="servico" value="<?= $servico["nome_TB_servico"] ?>">
                    <button type="submit"> Cancelar </button>
                </form>
=======
            <div class="servico-card">

                <div class="servico-info">

                    <h3>
                        <?= htmlspecialchars($servico["nome_TB_servico"]) ?>
                    </h3>


                    <ul class="servico-meta">

                        <li>
                            Prestador:
                            <?= htmlspecialchars($servico["nome_TB_prestador"]) ?>
                        </li>

                        <li>
                            Data:
                            <?= htmlspecialchars($servico["data_agendamento_TB_SolicitacaoServico"]) ?>
                        </li>

                        <li>
                            Valor:
                            R$
                            <?= htmlspecialchars($servico["valorTotal_TB_SolicitacaoServico"]) ?>
                        </li>

                    </ul>

                </div>


                <div class="servico-acoes">

                    <form action="?route=solicitacao" method="post">

                        <input type="hidden"
                               name="servico_id"
                               value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">

                        <input type="hidden"
                               name="status"
                               value="cancelado">

                        <input type="hidden"
                               name="prestador"
                               value="<?= $servico["FK_id_TB_prestadorServico"] ?>">

                        <input type="hidden"
                               name="servico"
                               value="<?= htmlspecialchars($servico["nome_TB_servico"]) ?>">


                        <button type="submit"
                                class="btn-recusar">

                            Cancelar

                        </button>

                    </form>

                </div>

>>>>>>> Stashed changes
            </div>

        <?php endforeach; ?>

    </div>


    <!-- SERVIÇOS ACEITOS -->

    <div class="painel" id="painel-aceitos">

        <?php if (empty($servicosAceitos)): ?>

            <p class="vazio">
                Sem serviços aceitos...
            </p>

        <?php endif; ?>


        <?php foreach ($servicosAceitos as $servico): ?>

<<<<<<< Updated upstream
                <form action="?route=solicitacao" method="post">
                    <input type="hidden" name="servico_id" id="" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                    <input type="hidden" name="status" value="cancelado">
                    <input type="hidden" name="prestador" value="<?= $servico["PK_id_TB_prestadorPerfil"] ?>">
                    <input type="hidden" name="servico" value="<?= $servico["nome_TB_servico"] ?>">
                    <button type="submit"> Cancelar </button>
                </form>
=======
            <div class="servico-card aceito">

                <div class="servico-info">

                    <h3>
                        <?= htmlspecialchars($servico["nome_TB_servico"]) ?>
                    </h3>


                    <ul class="servico-meta">

                        <li>
                            Prestador:
                            <?= htmlspecialchars($servico["nome_TB_prestador"]) ?>
                        </li>

                        <li>
                            Data:
                            <?= htmlspecialchars($servico["data_agendamento_TB_SolicitacaoServico"]) ?>
                        </li>

                        <li>
                            Valor:
                            R$
                            <?= htmlspecialchars($servico["valorTotal_TB_SolicitacaoServico"]) ?>
                        </li>

                        <li>
                            Código de Confirmação:
                            <?= htmlspecialchars($servico["pin_TB_SolicitacaoServico"]) ?>
                        </li>

                    </ul>

                </div>


                <div class="servico-acoes">

                    <form action="?route=solicitacao" method="post">

                        <input type="hidden"
                               name="servico_id"
                               value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">

                        <input type="hidden"
                               name="status"
                               value="cancelado">

                        <input type="hidden"
                               name="prestador"
                               value="<?= $servico["FK_id_TB_prestadorServico"] ?>">

                        <input type="hidden"
                               name="servico"
                               value="<?= htmlspecialchars($servico["nome_TB_servico"]) ?>">


                        <button type="submit"
                                class="btn-recusar">

                            Cancelar

                        </button>

                    </form>

                </div>

>>>>>>> Stashed changes
            </div>

        <?php endforeach; ?>

    </div>

</div>


   
    <script>
        (function () {
            var abas = document.querySelectorAll('.aba');
            var paineis = document.querySelectorAll('.painel');

            function mostrar(alvo) {
                abas.forEach(function (aba) {
                    var ativa = aba.dataset.alvo === alvo;
                    aba.classList.toggle('ativa', ativa);
                    aba.setAttribute('aria-selected', ativa);
                });
                paineis.forEach(function (painel) {
                    painel.classList.toggle('ativo', painel.id === alvo);
                });
                try { sessionStorage.setItem('abaServicos', alvo); } catch (e) { }
            }

            abas.forEach(function (aba) {
                aba.addEventListener('click', function () { mostrar(aba.dataset.alvo); });
            });

            // Mantém a aba escolhida após aceitar/recusar (a página recarrega)
            try {
                var salva = sessionStorage.getItem('abaServicos');
                if (salva && document.getElementById(salva)) mostrar(salva);
            } catch (e) { }
        })();
    </script>
</body>
<script>
    function concluirServico() {
        const form = document.getElementById("concluir")
        form.style.display = "block"
    }
</script>

</body>

</html>