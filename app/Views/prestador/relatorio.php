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
    require __DIR__ . '/partials/menu.php';
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

        <!-- TODO: trocar os dados de exemplo (const DADOS no script abaixo) por dados reais do back-end -->
        <p class="aviso-exemplo">
            <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><path d="M12 16.5v.01"/></svg>
            Dados de exemplo. O relatório real será conectado em breve.
        </p>

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
    </div>

    <script>
        (function () {
            // Dados de exemplo: só existem aqui, no front.
            var DADOS = {
                '7d': {
                    unidade: 'dia',
                    rotulos: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    valores: [180, 320, 0, 450, 290, 610, 240],
                    servicos: 9, servicosVar: 12,
                    media: 4.8, mediaVar: 0.1,
                    aceite: 86, aceiteVar: -3,
                    faturamentoVar: 8
                },
                '30d': {
                    unidade: 'semana',
                    rotulos: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                    valores: [1240, 1890, 1520, 2310],
                    servicos: 34, servicosVar: 9,
                    media: 4.7, mediaVar: 0,
                    aceite: 82, aceiteVar: 4,
                    faturamentoVar: 15
                },
                '12m': {
                    unidade: 'mês',
                    rotulos: ['Out', 'Nov', 'Dez', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set'],
                    valores: [2100, 2450, 3200, 2800, 3050, 3600, 3300, 4100, 3900, 4400, 4750, 4200],
                    servicos: 412, servicosVar: 21,
                    media: 4.7, mediaVar: 0.2,
                    aceite: 80, aceiteVar: 6,
                    faturamentoVar: 27
                }
            };

            var RANKING = [
                { nome: 'Instalação de chuveiro', qtd: 14 },
                { nome: 'Pintura de parede', qtd: 9 },
                { nome: 'Reparo hidráulico', qtd: 7 },
                { nome: 'Montagem de móveis', qtd: 4 }
            ];

            var ULTIMOS = [
                { nome: 'Instalação de chuveiro', data: '18/09', valor: 180, status: 'concluido' },
                { nome: 'Pintura de parede', data: '17/09', valor: 450, status: 'concluido' },
                { nome: 'Reparo hidráulico', data: '15/09', valor: 220, status: 'concluido' },
                { nome: 'Montagem de móveis', data: '14/09', valor: 150, status: 'cancelado' }
            ];

            var moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
            var moedaCurta = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 });
            var decimal = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

            function el(id) { return document.getElementById(id); }

            function variacao(id, valor, sufixo) {
                var alvo = el(id);
                var texto = (valor > 0 ? '+' : '') + String(valor).replace('.', ',') + sufixo;
                alvo.textContent = valor === 0 ? 'Estável' : texto + ' vs. período anterior';
                alvo.className = 'kpi-var ' + (valor > 0 ? 'sobe' : valor < 0 ? 'desce' : '');
            }

            function desenhar(periodo) {
                var d = DADOS[periodo];
                var total = d.valores.reduce(function (a, b) { return a + b; }, 0);
                var maximo = Math.max.apply(null, d.valores);

                el('kpi-faturamento').textContent = moeda.format(total);
                el('kpi-servicos').textContent = d.servicos;
                el('kpi-media').textContent = decimal.format(d.media);
                el('kpi-aceite').textContent = d.aceite + '%';
                variacao('kpi-faturamento-var', d.faturamentoVar, '%');
                variacao('kpi-servicos-var', d.servicosVar, '%');
                variacao('kpi-media-var', d.mediaVar, '');
                variacao('kpi-aceite-var', d.aceiteVar, ' p.p.');

                el('grafico-unidade').textContent = d.unidade;
                el('grafico-total').textContent = moeda.format(total);

                el('barras').innerHTML = d.valores.map(function (v, i) {
                    var altura = maximo ? Math.max((v / maximo) * 100, v > 0 ? 3 : 0) : 0;
                    var ehMaximo = v === maximo && v > 0;
                    return '<div class="barra-col">' +
                        '<span class="barra-valor">' + (v > 0 ? moedaCurta.format(v) : '') + '</span>' +
                        '<div class="barra-trilho"><div class="barra' + (ehMaximo ? ' pico' : '') + '" style="height:' + altura + '%" title="' + d.rotulos[i] + ': ' + moeda.format(v) + '"></div></div>' +
                        '<span class="barra-rotulo">' + d.rotulos[i] + '</span>' +
                        '</div>';
                }).join('');
            }

            function desenharListas() {
                var topo = RANKING[0].qtd;
                el('ranking').innerHTML = RANKING.map(function (r) {
                    return '<li><div class="ranking-linha"><span>' + r.nome + '</span><strong>' + r.qtd + '</strong></div>' +
                        '<div class="ranking-trilho"><div class="ranking-barra" style="width:' + (r.qtd / topo * 100) + '%"></div></div></li>';
                }).join('');

                el('ultimos').innerHTML = ULTIMOS.map(function (u) {
                    var cancelado = u.status === 'cancelado';
                    return '<li><div><span class="ultimo-nome">' + u.nome + '</span><span class="ultimo-data">' + u.data + '</span></div>' +
                        '<div class="ultimo-dir"><strong>' + moeda.format(u.valor) + '</strong>' +
                        '<span class="selo ' + (cancelado ? 'selo-cancelado' : 'selo-ok') + '">' + (cancelado ? 'Cancelado' : 'Concluído') + '</span></div></li>';
                }).join('');
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
