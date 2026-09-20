<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações | FastService</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/painel.css">
    <link rel="stylesheet" href="app/css/notificacoes.css">
</head>

<body>
    <?php
    // Nenhum item do menu fica ativo: notificações abre pelo sino da barra superior
    $paginaAtiva = '';
    $paginaTitulo = 'Notificações';
    require __DIR__ . '/../prestador/partials/menu.php';

    if (!isset($notificacoes))
        $notificacoes = [];
    ?>

    <main class="conteudo">
        <div class="pagina-cab">
            <h2>Notificações</h2>
            <p>Acompanhe o que acontece com seus serviços.</p>
        </div>

        <?php if (!empty($notificacoes)): ?>
            <ul class="lista-notificacoes">
                <?php foreach ($notificacoes as $notificacao): ?>
                    <?php
                    $nova = !$notificacao['lida_TB_notificacao'];
                    $ts = strtotime((string) ($notificacao['data_criacao_TB_notificacao'] ?? ''));
                    ?>
                    <li class="notificacao<?= $nova ? ' nova' : '' ?>">
                        <div class="notificacao-corpo">
                            <h3><?= htmlspecialchars($notificacao['titulo_TB_notificacao'] ?? '') ?></h3>
                            <p><?= htmlspecialchars($notificacao['mensagem_TB_notificacao'] ?? '') ?></p>
                        </div>
                        <div class="notificacao-lado">
                            <?php if ($nova): ?>
                                <span class="selo-nova">Nova</span>
                            <?php endif; ?>
                            <time><?= $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars((string) ($notificacao['data_criacao_TB_notificacao'] ?? '')) ?></time>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="vazio">Você não tem nenhuma notificação por enquanto.</p>
        <?php endif; ?>
    </main>
    </div>
</body>

</html>
