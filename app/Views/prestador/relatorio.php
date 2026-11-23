<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório | FastService</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/painel.css">
    <link rel="stylesheet" href="app/css/relatorio.css">
</head>

<body>
    <?php
    $paginaAtiva = 'relatorio';
    $paginaTitulo = 'Relatório';
    require __DIR__ . '/../partials/menu.php';
    ?>

    <main class="conteudo">
        <div class="pagina-cab cab-relatorio">
            <div>
                <h2>Relatório</h2>
                <p>Faturamento e desempenho dos seus serviços.</p>
            </div>

            <div class="periodos" role="group" aria-label="Período">
                <button type="button" class="periodo ativo" data-periodo="7d" aria-pressed="true">7 dias</button>
                <button type="button" class="periodo" data-periodo="30d" aria-pressed="false">30 dias</button>
                <button type="button" class="periodo" data-periodo="12m" aria-pressed="false">12 meses</button>
            </div>
        </div>

        <section class="kpis" aria-label="Indicadores do período">
            <article class="kpi">
                <span class="kpi-rotulo">Faturamento</span>
                <strong class="kpi-valor" id="kpi-faturamento"></strong>
                <span class="kpi-var" id="kpi-faturamento-var"></span>
            </article>
            <article class="kpi">
                <span class="kpi-rotulo">Serviços concluídos</span>
                <strong class="kpi-valor" id="kpi-servicos"></strong>
                <span class="kpi-var" id="kpi-servicos-var"></span>
            </article>
            <article class="kpi">
                <span class="kpi-rotulo">Média das avaliações</span>
                <strong class="kpi-valor" id="kpi-media"></strong>
                <span class="kpi-var" id="kpi-media-var"></span>
            </article>
            <article class="kpi">
                <span class="kpi-rotulo">Taxa de aceite</span>
                <strong class="kpi-valor" id="kpi-aceite"></strong>
                <span class="kpi-var" id="kpi-aceite-var"></span>
            </article>
        </section>

        <section class="painel-rel grafico">
            <div class="painel-rel-cab">
                <h3>Faturamento por <span id="grafico-unidade">dia</span></h3>
                <span class="painel-rel-total" id="grafico-total"></span>
            </div>
            <div class="barras" id="barras" role="img" aria-label="Gráfico de barras do faturamento no período"></div>
        </section>

        <div class="duas-colunas">
            <section class="painel-rel">
                <div class="painel-rel-cab">
                    <h3>Serviços mais prestados</h3>
                </div>
                <ul class="ranking" id="ranking"></ul>
            </section>

            <section class="painel-rel">
                <div class="painel-rel-cab">
                    <h3>Últimos serviços</h3>
                </div>
                <ul class="ultimos" id="ultimos"></ul>
            </section>
        </div>
    </main>

    <script>
        (function () {
            var RELATORIO = <?php echo json_encode($payloadRelatorio ?? [], JSON_UNESCAPED_UNICODE); ?>;
            var DADOS = RELATORIO.dados || {};
            var RANKING = RELATORIO.ranking || [];
            var ULTIMOS = RELATORIO.ultimos || [];

            var moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
            var moedaCurta = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 });
            var decimal = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

            function el(id) { return document.getElementById(id); }

            function variacao(id, valor, sufixo) {
                var alvo = el(id);
                var num = Number(valor) || 0;
                var texto = (num > 0 ? '+' : '') + String(num).replace('.', ',') + sufixo;
                alvo.textContent = num === 0 ? 'Estável' : texto + ' vs. período anterior';
                alvo.className = 'kpi-var ' + (num > 0 ? 'sobe' : num < 0 ? 'desce' : '');
            }

            function desenhar(periodo) {
                var d = DADOS[periodo] || {
                    unidade: 'dia',
                    rotulos: [],
                    valores: [],
                    servicos: 0,
                    servicosVar: 0,
                    media: 0,
                    mediaVar: 0,
                    aceite: 0,
                    aceiteVar: 0,
                    faturamentoVar: 0
                };

                var valores = (d.valores || []).map(function (v) { return Number(v) || 0; });
                var rotulos = d.rotulos || [];
                var total = valores.reduce(function (a, b) { return a + b; }, 0);
                var maximo = valores.length ? Math.max.apply(null, valores) : 0;

                el('kpi-faturamento').textContent = moeda.format(total);
                el('kpi-servicos').textContent = Number(d.servicos) || 0;
                el('kpi-media').textContent = decimal.format(Number(d.media) || 0);
                el('kpi-aceite').textContent = (Number(d.aceite) || 0) + '%';

                variacao('kpi-faturamento-var', d.faturamentoVar, '%');
                variacao('kpi-servicos-var', d.servicosVar, '%');
                variacao('kpi-media-var', d.mediaVar, '');
                variacao('kpi-aceite-var', d.aceiteVar, ' p.p.');

                el('grafico-unidade').textContent = d.unidade || 'dia';
                el('grafico-total').textContent = moeda.format(total);

                el('barras').innerHTML = valores.map(function (v, i) {
                    var altura = maximo ? Math.max((v / maximo) * 100, v > 0 ? 3 : 0) : 0;
                    var ehMaximo = v === maximo && v > 0;
                    var rotulo = rotulos[i] || '';
                    return '<div class="barra-col">' +
                        '<span class="barra-valor">' + (v > 0 ? moedaCurta.format(v) : '') + '</span>' +
                        '<div class="barra-trilho"><div class="barra' + (ehMaximo ? ' pico' : '') + '" style="height:' + altura + '%" title="' + rotulo + ': ' + moeda.format(v) + '"></div></div>' +
                        '<span class="barra-rotulo">' + rotulo + '</span>' +
                        '</div>';
                }).join('');
            }

            function desenharListas() {
                if (!RANKING.length) {
                    el('ranking').innerHTML = '<li><span>Nenhum serviço concluído ainda.</span></li>';
                } else {
                    var topo = Number(RANKING[0].qtd) || 1;
                    el('ranking').innerHTML = RANKING.map(function (r) {
                        var qtd = Number(r.qtd) || 0;
                        return '<li><div class="ranking-linha"><span>' + r.nome + '</span><strong>' + qtd + '</strong></div>' +
                            '<div class="ranking-trilho"><div class="ranking-barra" style="width:' + ((qtd / topo) * 100) + '%"></div></div></li>';
                    }).join('');
                }

                if (!ULTIMOS.length) {
                    el('ultimos').innerHTML = '<li><span>Nenhum serviço registrado ainda.</span></li>';
                } else {
                    el('ultimos').innerHTML = ULTIMOS.map(function (u) {
                        var cancelado = u.status === 'cancelado';
                        var seloTexto = cancelado ? 'Cancelado' : (u.status === 'concluido' ? 'Concluído' : u.status);
                        var seloClasse = cancelado ? 'selo-cancelado' : 'selo-ok';
                        return '<li><div><span class="ultimo-nome">' + u.nome + '</span><span class="ultimo-data">' + u.data + '</span></div>' +
                            '<div class="ultimo-dir"><strong>' + moeda.format(Number(u.valor) || 0) + '</strong>' +
                            '<span class="selo ' + seloClasse + '">' + seloTexto + '</span></div></li>';
                    }).join('');
                }
            }

            var botoes = document.querySelectorAll('.periodo');
            botoes.forEach(function (botao) {
                botao.addEventListener('click', function () {
                    botoes.forEach(function (b) {
                        var ativo = b === botao;
                        b.classList.toggle('ativo', ativo);
                        b.setAttribute('aria-pressed', ativo);
                    });
                    desenhar(botao.dataset.periodo);
                });
            });

            desenhar('7d');
            desenharListas();
        })();
    </script>
</body>

</html>