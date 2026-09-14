<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificacoes</title>
    <link rel="stylesheet" href="app/css/notificacoes.css">
</head>
<body>
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
