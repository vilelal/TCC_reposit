<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início | FastService</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/painel.css">
    <link rel="stylesheet" href="app/css/inicio.css">
</head>

<body>
    <?php
    $paginaAtiva = 'inicio';
    $paginaTitulo = 'Início';
    require __DIR__ . '/partials/menu.php';
    if (!isset($user)) $user = [];

    // Valores ainda não vêm do back-end; quando vierem, basta o controller defini-los.
    $faturamento = isset($user["faturamento"]) ? 'R$ ' . number_format((float) $user["faturamento"], 2, ',', '.') : 'R$ 0,00';
    $servicosPrestados = $user["total_servicos"] ?? 0;
    $mediaAvaliacoes = isset($avaliacoes["media_avaliacoes"]) ? $avaliacoes["media_avaliacoes"] : '—';

    $primeiroNome = trim(explode(' ', trim($_SESSION['nome'] ?? ''))[0]);
    ?>

    <main class="conteudo">
        <div class="pagina-cab">
            <h2>Olá<?= $primeiroNome !== '' ? ', ' . htmlspecialchars($primeiroNome) : '' ?></h2>
            <p>Um resumo da sua atividade na FastService.</p>
        </div>

        <section class="resumo" aria-label="Resumo">
            <article class="metrica destaque">
                <span class="metrica-rotulo">Faturamento</span>
                <div>
                    <strong class="metrica-valor"><?= $faturamento ?></strong>
                    <p class="metrica-nota">Total dos serviços concluídos</p>
                </div>
            </article>

            <article class="metrica">
                <span class="metrica-rotulo">Serviços prestados</span>
                <div>
                    <strong class="metrica-valor"><?= (int) $servicosPrestados ?></strong>
                    <p class="metrica-nota">Concluídos até hoje</p>
                </div>
            </article>

            <article class="metrica">
                <span class="metrica-rotulo">Média das avaliações</span>
                <div>
                    <strong class="metrica-valor"><?= $mediaAvaliacoes ?></strong>
                    <p class="metrica-nota">De 0 a 5 estrelas</p>
                </div>
            </article>
        </section>

        <div class="blocos">
            <section class="bloco">
                <h3>Atividade recente</h3>
                <p class="vazio-bloco">Nada por aqui ainda. Novas solicitações e avaliações vão aparecer neste espaço.</p>
            </section>

            <section class="bloco">
                <h3>Atalhos</h3>
                <ul class="atalhos">
                    <li>
                        <a href="?route=lista-servicos">
                            <span>Ver solicitações<small>Aceite ou recuse novos pedidos</small></span>
                            <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m9 6 6 6-6 6" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="?route=relatorio">
                            <span>Abrir relatório<small>Faturamento e desempenho</small></span>
                            <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m9 6 6 6-6 6" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="?route=chat">
                            <span>Conversas<small>Fale com seus clientes</small></span>
                            <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m9 6 6 6-6 6" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </section>
        </div>
</body>

</html>