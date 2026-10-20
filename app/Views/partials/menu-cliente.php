<?php
/*
 * Menu lateral + barra superior do painel do prestador.
 * Antes de incluir, defina:
 *   $paginaAtiva  -> 'inicio' | 'relatorio' | 'servicos' | 'perfil'
 *   $paginaTitulo -> texto exibido na barra superior
 * Este arquivo abre a <div class="principal">: a página fecha com </main></div>.
 */
$paginaAtiva = $paginaAtiva ?? '';
$paginaTitulo = $paginaTitulo ?? '';

$nomeMenu = $_SESSION['nome'] ?? '';

// A foto só entra na sessão depois de um upload. Quem apenas fez login ainda não a tem,
// então buscamos no banco uma única vez e guardamos (inclusive a ausência) na sessão.
$fotoMenu = !empty($user['foto_TB_usuario']) ? $user['foto_TB_usuario'] : ($_SESSION['foto'] ?? '');
if ($fotoMenu === '' && isset($_SESSION['id']) && empty($_SESSION['foto_verificada'])) {
    $dadosMenu = ($_SESSION['tipo'] ?? '') === 'prestador'
        ? UserModel::getPrestadorById($_SESSION['id'])
        : UserModel::getClientById($_SESSION['id']);
    $fotoMenu = $dadosMenu['foto_TB_usuario'] ?? '';
    if ($fotoMenu !== '') {
        $_SESSION['foto'] = $fotoMenu;
    }
    $_SESSION['foto_verificada'] = true;
}

// Caminho salvo no banco cujo arquivo não existe mais: trata como sem foto
if ($fotoMenu !== '' && !preg_match('#^https?://#', $fotoMenu)) {
    $fotoMenu = is_file($fotoMenu)
        ? $fotoMenu . '?v=' . filemtime($fotoMenu) // o arquivo é sobrescrito no upload; evita cache velho
        : '';
}

// Sem foto: círculo com a inicial do nome
$inicialMenu = $nomeMenu !== '' ? mb_strtoupper(mb_substr($nomeMenu, 0, 1)) : '?';

$itensMenu = [
    'inicio'    => ['rota' => 'dashboard',      'rotulo' => 'Início'],
    'relatorio' => ['rota' => 'relatorio',      'rotulo' => 'Relatório'],
    'servicos'  => ['rota' => 'lista-servicos', 'rotulo' => 'Serviços'],
];

$iconesMenu = [
    'inicio'    => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>',
    'relatorio' => '<path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20H2"/>',
    'servicos'  => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1"/><path d="M9 11h6"/><path d="M9 15h4"/>',
];
?>
<aside class="menu">
    <a class="marca" href="?route=">
        <img src="app/css/img/logo.png" alt="" class="marca-logo">
        <span class="marca-nome">FastService</span>
    </a>

    <nav class="menu-nav" aria-label="Menu do painel">
        <?php foreach ($itensMenu as $chave => $item): ?>
            <a class="menu-item<?= $paginaAtiva === $chave ? ' ativo' : '' ?>"
               href="?route=<?= $item['rota'] ?>"
               <?= $paginaAtiva === $chave ? 'aria-current="page"' : '' ?>>
                <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $iconesMenu[$chave] ?></svg>
                <span><?= $item['rotulo'] ?></span>
            </a>
        <?php endforeach; ?>

        <a class="menu-item<?= $paginaAtiva === 'perfil' ? ' ativo' : '' ?>" href="?route=perfil"
           <?= $paginaAtiva === 'perfil' ? 'aria-current="page"' : '' ?>>
            <?php if ($fotoMenu !== ''): ?>
                <img class="menu-foto" src="<?= htmlspecialchars($fotoMenu) ?>" alt="">
            <?php else: ?>
                <span class="menu-foto sem-foto" aria-hidden="true"><?= htmlspecialchars($inicialMenu) ?></span>
            <?php endif; ?>
            <span>Perfil</span>
        </a>
    </nav>
</aside>

<div class="principal">
    <header class="topo">
        <h1 class="topo-titulo"><?= htmlspecialchars($paginaTitulo) ?></h1>

        <div class="topo-acoes">
            <a class="topo-btn" href="?route=chat" aria-label="Conversas" title="Conversas">
                <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.5-4.6A8 8 0 1 1 21 12Z"/></svg>
            </a>
            <a class="topo-btn" href="?route=notificacoes" aria-label="Notificações" title="Notificações">
                <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 16V11a6 6 0 1 1 12 0v5l1.5 2h-15L6 16Z"/><path d="M10 21h4"/></svg>
            </a>

            <span class="topo-divisor" aria-hidden="true"></span>

            <div class="topo-usuario">
                <?php if ($fotoMenu !== ''): ?>
                    <img class="topo-foto" src="<?= htmlspecialchars($fotoMenu) ?>" alt="">
                <?php else: ?>
                    <span class="topo-foto sem-foto" aria-hidden="true"><?= htmlspecialchars($inicialMenu) ?></span>
                <?php endif; ?>
                <?php if ($nomeMenu !== ''): ?>
                    <span class="topo-nome"><?= htmlspecialchars($nomeMenu) ?></span>
                <?php endif; ?>
                <svg class="topo-verificado" viewBox="0 0 24 24" aria-label="Perfil verificado" role="img"><title>Perfil verificado</title><circle cx="12" cy="12" r="10" fill="#FDB441"/><path d="m7.5 12.5 3 3 6-6.5" fill="none" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        </div>
    </header>
