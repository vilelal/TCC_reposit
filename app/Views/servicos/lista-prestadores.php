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
    <title>Prestadores disponíveis</title>
    <link rel="stylesheet" href="app/css/lista-prestador.css">
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
                <!-- links para cliente logado -->
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
    <div class="lista">
        <?php if (empty($prestadores)): ?>
            <div class="vazio">Nenhum prestador encontrado para essa busca.</div>
        <?php else: ?>
            <?php foreach ($prestadores as $prestador): ?>
                <?php
                $nome = $prestador["nome_TB_prestador"];
                $inicial = mb_strtoupper(mb_substr($nome, 0, 1));
                $media = $prestador["media_avaliacoes"] ?? null;
                $valor = $prestador["preco_customizado_TB_servico"] ?? $prestador["precoPadrao_TB_servico"];
                ?>


                <div class="card-prestador"
                    onclick="document.getElementById('form-perfil-<?= $prestador['PK_id_TB_prestadorServico'] ?>').submit();">

                    <div class="avatar">
                        <?= htmlspecialchars($inicial) ?>
                    </div>

                    <div class="info">
                        <p class="nome">
                            <?= htmlspecialchars($nome) ?>
                        </p>
                        <div class="meta">
                            <span>
                                <?= (int) $prestador["total_servicos"] ?> serviços realizados
                            </span>
                            <span class="separador">•</span>
                            <span class="avaliacao">
                                <svg viewBox="0 0 20 20">
                                    <path d="M10 1l2.6 5.9 6.4.6-4.8 4.3 1.4 6.2L10 14.9 4.4 18l1.4-6.2L1 7.5l6.4-.6z" />
                                </svg>
                                <?= $media !== null ? number_format($media, 1) : "Sem avaliações" ?>
                            </span>
                            <span class="separador">•</span>
                            <span class="distancia">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z" />
                                    <circle cx="12" cy="10" r="2.5" />
                                </svg>
                                <?= htmlspecialchars($prestador["distancia_km"]) ?> km de você
                            </span>
                        </div>
                    </div>

                    <!-- 
      BOTÃO SOLICITAR (Sendo uma tag button real, ele aceita o atributo form="" 
      e não vai mais quebrar por estar dentro de uma div)
    -->
                    <button type="submit" form="form-solicitar-<?= $prestador["PK_id_TB_prestadorServico"] ?>"
                        class="btn-solicitar"
                        style=""
                        onclick="event.stopPropagation();"> <!-- Impede que o clique aqui dispare o clique do card -->
                        Solicitar
                    </button>
                </div>

                <!-- ==================================================================== -->
                <!-- FORMULÁRIOS OCULTOS (Ficam do lado de fora do card, organizados por ID) -->

                <!-- FORMULÁRIO 1: Abre o perfil do prestador -->
                <form id="form-perfil-<?= $prestador["PK_id_TB_prestadorServico"] ?>" action="?route=perfil-prestador"
                    method="post" style="display: none;">
                    <input type="hidden" name="prestador_id" value="<?= $prestador["PK_id_TB_prestadorPerfil"] ?>">
                    <input type="hidden" name="media" value="<?= $prestador["media_avaliacoes"] ?>">
                </form>

                <!-- FORMULÁRIO 2: Solicita o serviço -->
                <form id="form-solicitar-<?= $prestador["PK_id_TB_prestadorServico"] ?>" action="?route=solicitar"
                    method="post" style="display: none;">
                    <input type="hidden" name="data" value="<?= htmlspecialchars($data ?? "") ?>">
                    <input type="hidden" name="servico" value="<?= htmlspecialchars($servico ?? "") ?>">
                    <input type="hidden" name="prestador" value="<?= $prestador["PK_id_TB_prestadorServico"] ?>">
                    <input type="hidden" name="valor" value="<?= $valor ?>">
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>

</body>

</html>