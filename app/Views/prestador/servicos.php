<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços | FastService</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/painel.css">
    <link rel="stylesheet" href="app/css/servico.css">
</head>

<body>
    <?php
    $paginaAtiva = 'servicos';
    $paginaTitulo = 'Serviços';
    require __DIR__ . '/partials/menu.php';

    if (!isset($servicos))
        $servicos = [];

    $servicosPendentes = array_filter($servicos, function ($servico) {
        return $servico["status_TB_SolicitacaoServico"] == "pendente";
    });

    $servicosAceitos = array_filter($servicos, function ($servico) {
        return $servico["status_TB_SolicitacaoServico"] == "aceito";
    });

    function formatarDataServico($data)
    {
        $ts = strtotime((string) $data);
        return $ts ? date('d/m/Y \à\s H:i', $ts) : htmlspecialchars((string) $data);
    }

    function formatarValorServico($valor)
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }

    $iconeData = '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17"/><path d="M8 3v4"/><path d="M16 3v4"/></svg>';
    $iconeValor = '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M14.5 9.5c-.5-.9-1.4-1.3-2.5-1.3-1.4 0-2.4.7-2.4 1.8 0 2.6 5 1.2 5 3.8 0 1.1-1.1 1.8-2.6 1.8-1.2 0-2.2-.5-2.7-1.4"/><path d="M12 6.5v1.7"/><path d="M12 15.8v1.7"/></svg>';
    ?>

    <main class="conteudo">
        <div class="pagina-cab">
            <h2>Serviços</h2>
            <p>Responda às solicitações e acompanhe os serviços que você aceitou.</p>
        </div>

        <div class="abas" role="tablist">
            <button type="button" class="aba ativa" role="tab" data-alvo="painel-pendentes" aria-selected="true">
                Solicitações <span class="contador"><?= count($servicosPendentes) ?></span>
            </button>
            <button type="button" class="aba" role="tab" data-alvo="painel-aceitos" aria-selected="false">
                Serviços aceitos <span class="contador"><?= count($servicosAceitos) ?></span>
            </button>
        </div>

        <div class="painel ativo" id="painel-pendentes" role="tabpanel">
            <?php if (empty($servicosPendentes)): ?>
                <p class="vazio">Sem novas solicitações por enquanto.</p>
            <?php endif; ?>

            <?php foreach ($servicosPendentes as $servico): ?>
                <article class="servico-card">
                    <div class="servico-info">
                        <h3><?= htmlspecialchars($servico["nome_TB_servico"]) ?></h3>
                        <ul class="servico-meta">
                            <li><?= $iconeData ?><?= formatarDataServico($servico["data_agendamento_TB_SolicitacaoServico"]) ?></li>
                            <li><?= $iconeValor ?><?= formatarValorServico($servico["valorTotal_TB_SolicitacaoServico"]) ?></li>
                        </ul>
                    </div>

                    <div class="servico-acoes">
                        <form action="?route=solicitacao" method="post">
                            <input type="hidden" name="servico_id" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                            <input type="hidden" name="status" value="cancelado">
                            <input type="hidden" name="cliente" value="<?= $servico["FK_id_TB_cliente"] ?>">
                            <input type="hidden" name="servico" value="<?= htmlspecialchars($servico["nome_TB_servico"]) ?>">
                            <button type="submit" class="btn-recusar">Recusar</button>
                        </form>

                        <form action="?route=solicitacao" method="post">
                            <input type="hidden" name="servico_id" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                            <input type="hidden" name="status" value="aceito">
                            <input type="hidden" name="cliente" value="<?= $servico["FK_id_TB_cliente"] ?>">
                            <input type="hidden" name="servico" value="<?= htmlspecialchars($servico["nome_TB_servico"]) ?>">
                            <button type="submit" class="btn-aceitar">Aceitar</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="painel" id="painel-aceitos" role="tabpanel">
            <?php if (empty($servicosAceitos)): ?>
                <p class="vazio">Você ainda não tem serviços aceitos.</p>
            <?php endif; ?>

            <?php foreach ($servicosAceitos as $servico): ?>
                <article class="servico-card aceito">
                    <div class="servico-info">
                        <h3><?= htmlspecialchars($servico["nome_TB_servico"]) ?></h3>
                        <ul class="servico-meta">
                            <li><?= $iconeData ?><?= formatarDataServico($servico["data_agendamento_TB_SolicitacaoServico"]) ?></li>
                            <li><?= $iconeValor ?><?= formatarValorServico($servico["valorTotal_TB_SolicitacaoServico"]) ?></li>
                        </ul>
                    </div>

                    <div class="servico-acoes">
                        <form action="?route=solicitacao" method="post">
                            <input type="hidden" name="servico_id" value="<?= $servico["PK_id_TB_SolicitacaoServico"] ?>">
                            <input type="hidden" name="status" value="cancelado">
                            <input type="hidden" name="cliente" value="<?= $servico["FK_id_TB_cliente"] ?>">
                            <input type="hidden" name="servico" value="<?= htmlspecialchars($servico["nome_TB_servico"]) ?>">
                            <button type="submit" class="btn-recusar">Cancelar</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
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

</html>
