<?php
/*
 * Barra superior do painel do CLIENTE (sem menu lateral).
 * Antes de incluir, defina:
 *   $paginaAtiva  -> 'notificacoes' | 'perfil' | '' (destaca o botão correspondente no topo)
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
?>
<link rel="stylesheet" href="app/css/menu-cliente.css">

<div class="principal principal-cliente">
    <header class="topo">
        <div class="topo-esq">
            <a class="marca" href="?route=" aria-label="FastService - início">
                <img src="app/css/img/logo.png" alt="" class="marca-logo">
                <span class="marca-nome">FastService</span>
            </a>
            <?php if ($paginaTitulo !== ''): ?>
                <h1 class="topo-titulo"><?= htmlspecialchars($paginaTitulo) ?></h1>
            <?php endif; ?>
        </div>

        <div class="topo-acoes">
            <a class="topo-btn" href="?route=chat" aria-label="Conversas" title="Conversas">
                <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.5-4.6A8 8 0 1 1 21 12Z"/></svg>
            </a>
            <a class="topo-btn<?= $paginaAtiva === 'notificacoes' ? ' ativo' : '' ?>" href="?route=notificacoes" aria-label="Notificações" title="Notificações"
               <?= $paginaAtiva === 'notificacoes' ? 'aria-current="page"' : '' ?>>
                <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 16V11a6 6 0 1 1 12 0v5l1.5 2h-15L6 16Z"/><path d="M10 21h4"/></svg>
            </a>

            <a class="topo-perfil<?= $paginaAtiva === 'perfil' ? ' ativo' : '' ?>" href="?route=perfil"
               <?= $paginaAtiva === 'perfil' ? 'aria-current="page"' : '' ?>>
                <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                <span>Perfil</span>
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