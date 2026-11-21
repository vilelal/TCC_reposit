<?php
$logado = isset($_SESSION["id"]);
$tipo = $_SESSION["tipo"] ?? "";

$ehPrestador = $tipo === "prestador";
$ehAdmin = $tipo === "Admin";

// Rota da tela lista-prestador.php (ajuste aqui se o nome da rota for outro)
$rotaLista = "lista-prestadores";

if (!isset($prestador)) $prestador = [];

$dados = $prestador[0] ?? [];

$nomePrestador = $dados["nome_TB_prestador"] ?? "";
$bio = trim($dados["bio_TB_prestador"] ?? "");
$cidade = trim($dados["cidade_TB_prestadorPerfil"] ?? "");
$fotoPerfil = trim($dados["foto_TB_prestador"] ?? "");
$inicial = $nomePrestador !== "" ? mb_strtoupper(mb_substr($nomePrestador, 0, 1)) : "?";

$media = (isset($mediaAvaliacoes) && is_numeric($mediaAvaliacoes)) ? (float) $mediaAvaliacoes : null;

// Fotos do portfólio (ignora linhas sem foto).
$fotos = [];
foreach ($prestador as $prest) {
    $foto = trim($prest["foto_TB_repositorio"] ?? "");
    if ($foto !== "") {
        $fotos[] = $foto;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Prestador<?= $nomePrestador !== "" ? " - " . htmlspecialchars($nomePrestador) : "" ?></title>
    <link rel="stylesheet" href="app/css/prestador-perfil.css">
</head>

<body>

    <header class="site-header">
        <div class="header-wrap">

            <a href="?route=home" class="brand" aria-label="FastService - página inicial">
                <img src="app/css/img/logo.png" alt="FastService">
                <span>FastService</span>
            </a>

            <nav class="nav-links" aria-label="Principal">
                <a href="" class="funcionamento">Como funciona</a>
                <a href="" class="servico">Serviços</a>
                <?php if (isset($_SESSION["id_cliente"])): ?>
                    <a href="?route=lista-servicos-cliente" class="servico">Minhas Solicitações</a>
                <?php endif; ?>
            </nav>

            <div class="nav-actions">

                <?php if ($logado): ?>

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

    <main class="perfil-page">

        <form action="?route=buscar-proximos" method="post" class="voltar-form">
            <input type="hidden" name="FK_id_TB_servico" value="<?= htmlspecialchars($servico ?? "") ?>">
            <input type="hidden" name="data_agendamento" value="<?= htmlspecialchars($data ?? "") ?>">
            <button type="submit" class="voltar">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
                Voltar
            </button>
        </form>
        
        <?php if (empty($dados)): ?>

            <div class="vazio">Prestador não encontrado.</div>

        <?php else: ?>

            <section class="perfil-card">

                <div class="perfil-capa"></div>

                <div class="perfil-topo">

                    <div class="perfil-avatar">
                        <?php if ($fotoPerfil !== ""): ?>
                            <img src="<?= htmlspecialchars($fotoPerfil) ?>"
                                alt="Foto de <?= htmlspecialchars($nomePrestador) ?>">
                        <?php else: ?>
                            <span><?= htmlspecialchars($inicial) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="perfil-identidade">
                        <h1 class="perfil-nome"><?= htmlspecialchars($nomePrestador) ?></h1>

                        <div class="perfil-meta">
                            <span class="avaliacao">
                                <svg viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10 1l2.6 5.9 6.4.6-4.8 4.3 1.4 6.2L10 14.9 4.4 18l1.4-6.2L1 7.5l6.4-.6z" />
                                </svg>
                                <?= $media !== null ? number_format($media, 1) : "Sem avaliações" ?>
                            </span>

                            <?php if ($cidade !== ""): ?>
                                <span class="separador">•</span>
                                <span class="cidade">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>
                                    <?= htmlspecialchars($cidade) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <div class="perfil-secao">
                    <h2>Sobre</h2>
                    <?php if ($bio !== ""): ?>
                        <p class="bio"><?= nl2br(htmlspecialchars($bio)) ?></p>
                    <?php else: ?>
                        <p class="bio bio-vazia">Este prestador ainda não adicionou uma biografia.</p>
                    <?php endif; ?>
                </div>

                <div class="perfil-secao">
                    <h2>Portfólio</h2>
                    <?php if (!empty($fotos)): ?>
                        <div class="galeria">
                            <?php foreach ($fotos as $i => $foto): ?>
                                <a href="<?= htmlspecialchars($foto) ?>" target="_blank" rel="noopener" class="galeria-item">
                                    <img src="<?= htmlspecialchars($foto) ?>"
                                        alt="Trabalho de <?= htmlspecialchars($nomePrestador) ?> (<?= $i + 1 ?>)"
                                        loading="lazy">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="bio bio-vazia">Nenhuma foto no portfólio ainda.</p>
                    <?php endif; ?>
                </div>

            </section>

        <?php endif; ?>

    </main>
    
</body>

</html>