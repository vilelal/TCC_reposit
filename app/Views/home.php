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
    <title>FastService | O profissional certo em poucos cliques</title>
    <meta name="description" content="Encontre profissionais avaliados perto de você para pequenos serviços, com rapidez e segurança.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/landing.css">
</head>

<body>

    <!-- HEADER -->
    <header class="site-header">
        <div class="wrap">
            <a href="?route=home" class="brand" aria-label="FastService - página inicial">
                <img src="app/css/img/logo.png" alt="">
                <span>FastService</span>
            </a>

            <nav class="nav-links" aria-label="Principal">
                <a href="#como-funciona">Como funciona</a>
                <a href="#categorias">Categorias</a>
                <a href="#servicos">Serviços</a>
            </nav>

            <div class="nav-actions">
                <?php if ($logado): ?>
                    <span class="saudacao">Olá, <?= $nome ?></span>
                    <a href="?route=lista-servicos-cliente" class="btn btn-ghost btn-sm hide-sm">Minhas Solicitações</a>
                    <a href="?route=chat" class="btn btn-ghost btn-sm hide-sm">Chat</a>
                    <?php if ($ehAdmin): ?>
                        <a href="?route=painel-admin" class="btn btn-ghost btn-sm hide-sm">Painel</a>
                    <?php elseif ($ehPrestador): ?>
                        <a href="?route=dashboard" class="btn btn-primary btn-sm">Meu painel</a>
                    <?php else: ?>
                        <a href="?route=perfil" class="btn btn-primary btn-sm">Meu perfil</a>
                    <?php endif; ?>
                    <a href="?route=logout" class="btn btn-ghost btn-sm">Sair</a>
                <?php else: ?>
                    <a href="?route=login-form" class="btn btn-ghost btn-sm">Entrar</a>
                    <a href="?route=prestador-form" class="btn btn-primary btn-sm">Seja um profissional</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>

        <!-- HERO -->
        <section class="hero">
            <div class="wrap">
                <div class="hero-texto">
                    <span class="eyebrow"><img src="app/css/img/icone-raio.png" alt="">Contratação rápida e segura</span>

                    <h1>O profissional certo em <span class="destaque">poucos cliques.</span></h1>
                    <p class="hero-sub">Segurança e rapidez em encontrar o profissional ideal para suas necessidades.</p>

                    <form class="busca" action="" method="POST" role="search">
                        <input type="text" name="nome" placeholder="Que serviço você procura?" aria-label="Nome do serviço">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </form>

                    <div class="hero-cta">
                        <?php if (!$ehPrestador): ?>
                            <a href="?route=solicitar-servico" class="btn btn-ghost">Solicitar um serviço</a>
                        <?php endif; ?>

                        <?php if (!$logado): ?>
                            <a href="?route=cadastro-form" class="btn btn-ghost">Criar conta</a>
                        <?php elseif (!$ehPrestador && !$ehAdmin): ?>
                            <a href="?route=prestador-form" class="btn btn-ghost">Torne-se prestador</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="hero-visual" aria-hidden="true">
                    <span class="circulo c1"></span>
                    <span class="circulo c2"></span>
                    <span class="circulo c3"></span>
                    <img class="foto f1" src="app/css/img_servico/pintura.png" alt="">
                    <img class="foto f2" src="app/css/img_servico/jardinagem.png" alt="">
                    <img class="mascote" src="app/css/img/logo.png" alt="">
                    <span class="chip k1"><img src="app/css/img/icone-escudo.png" alt="">Profissionais avaliados</span>
                    <span class="chip k2"><img src="app/css/img/icone-raio.png" alt="">Contratação em minutos</span>
                </div>
            </div>
        </section>

        <!-- CATEGORIAS -->
        <section class="secao" id="categorias">
            <div class="wrap">
                <div class="secao-topo">
                    <span class="rotulo">Categorias</span>
                    <h2>Profissionais por categoria</h2>
                    <p>Escolha a área e encontre quem resolve o seu problema.</p>
                </div>

                <div class="categorias-grid">
                    <a class="categoria" href="?route=solicitar-servico">
                        <span class="icone-box"><img src="app/css/img/icone-raio.png" alt=""></span>
                        Assistência técnica geral
                    </a>
                    <a class="categoria" href="?route=solicitar-servico">
                        <span class="icone-box"><img src="app/css/img/icone_ferramentas.png" alt=""></span>
                        Pequenos consertos
                    </a>
                    <a class="categoria" href="?route=solicitar-servico">
                        <span class="icone-box"><img src="app/css/img/icone-rolo.png" alt=""></span>
                        Reformas e reparos
                    </a>
                    <a class="categoria" href="?route=solicitar-servico">
                        <span class="icone-box"><img src="app/css/img/icone-engrenagem.png" alt=""></span>
                        Serviços em geral
                    </a>
                    <a class="categoria" href="?route=solicitar-servico">
                        <span class="icone-box"><img src="app/css/img/icone-casa.png" alt=""></span>
                        Serviços domésticos
                    </a>
                    <a class="categoria" href="?route=solicitar-servico">
                        <span class="icone-box"><img src="app/css/img/icone-carro.png" alt=""></span>
                        Meio automotivo
                    </a>
                </div>
            </div>
        </section>

        <!-- SERVIÇOS POPULARES -->
        <section class="secao suave" id="servicos">
            <div class="wrap">
                <div class="secao-topo">
                    <span class="rotulo">Mais procurados</span>
                    <h2>Nossos serviços populares</h2>
                    <p>Peça um orçamento em poucos passos e compare os profissionais próximos de você.</p>
                </div>

                <div class="servicos-grid">
                    <article class="servico-card">
                        <div class="servico-thumb"><img class="foto-servico" src="app/css/img_servico/jardinagem.png" alt="Profissional de jardinagem"></div>
                        <div class="servico-info">
                            <h3>Jardinagem</h3>
                            <a href="?route=solicitar-servico" class="btn btn-primary btn-sm">Orçamento</a>
                        </div>
                    </article>
                    <article class="servico-card">
                        <div class="servico-thumb"><img class="foto-servico" src="app/css/img_servico/eletrodomestico.png" alt="Técnico de eletrodomésticos"></div>
                        <div class="servico-info">
                            <h3>Eletrodomésticos</h3>
                            <a href="?route=solicitar-servico" class="btn btn-primary btn-sm">Orçamento</a>
                        </div>
                    </article>
                    <article class="servico-card">
                        <div class="servico-thumb"><img class="foto-servico" src="app/css/img_servico/pintura.png" alt="Pintor"></div>
                        <div class="servico-info">
                            <h3>Pintura</h3>
                            <a href="?route=solicitar-servico" class="btn btn-primary btn-sm">Orçamento</a>
                        </div>
                    </article>
                    <article class="servico-card">
                        <div class="servico-thumb"><img class="foto-servico" src="app/css/img_servico/pedreiro.png" alt="Pedreiro"></div>
                        <div class="servico-info">
                            <h3>Pedreiros</h3>
                            <a href="?route=solicitar-servico" class="btn btn-primary btn-sm">Orçamento</a>
                        </div>
                    </article>
                    <article class="servico-card">
                        <div class="servico-thumb"><img class="foto-servico" src="app/css/img_servico/cuidador.png" alt="Cuidador"></div>
                        <div class="servico-info">
                            <h3>Cuidadores</h3>
                            <a href="?route=solicitar-servico" class="btn btn-primary btn-sm">Orçamento</a>
                        </div>
                    </article>
                    <article class="servico-card">
                        <div class="servico-thumb"><img class="foto-servico" src="app/css/img_servico/garcons.png" alt="Garçom"></div>
                        <div class="servico-info">
                            <h3>Garçons</h3>
                            <a href="?route=solicitar-servico" class="btn btn-primary btn-sm">Orçamento</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- COMO FUNCIONA -->
        <section class="secao" id="como-funciona">
            <div class="wrap">
                <div class="secao-topo">
                    <span class="rotulo">Como funciona</span>
                    <h2>Do pedido ao serviço feito, sem complicação</h2>
                </div>

                <div class="passos">
                    <div class="passo">
                        <span class="numero">1</span>
                        <h3>Escolha o serviço</h3>
                        <p>Selecione a categoria, o serviço que você precisa e o nível de urgência.</p>
                    </div>
                    <div class="passo">
                        <span class="numero">2</span>
                        <h3>Veja quem está por perto</h3>
                        <p>Listamos os prestadores próximos, com avaliações de outros clientes.</p>
                    </div>
                    <div class="passo">
                        <span class="numero">3</span>
                        <h3>Converse e contrate</h3>
                        <p>Combine os detalhes pelo chat da plataforma e acompanhe tudo em um só lugar.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- DIFERENCIAIS -->
        <section class="secao suave" id="sobre">
            <div class="wrap">
                <div class="secao-topo">
                    <span class="rotulo">Por que a FastService</span>
                    <h2>A melhor plataforma de contratação de pequenos serviços</h2>
                    <p>Conectamos trabalhadores a clientes próximos, que solicitam e recebem atendimento de qualidade, com rapidez e segurança.</p>
                </div>

                <div class="diferenciais">
                    <div class="diferencial">
                        <span class="icone-box"><img src="app/css/img/icone-escudo.png" alt=""></span>
                        <h3>Confiabilidade</h3>
                        <strong>Profissionais confiáveis</strong>
                        <p>Prestadores avaliados e organizados em um catálogo acessível.</p>
                    </div>
                    <div class="diferencial">
                        <span class="icone-box"><img src="app/css/img/icone-estrela.png" alt=""></span>
                        <h3>Qualidade</h3>
                        <strong>Avaliações reais</strong>
                        <p>Consulte feedbacks antes de contratar.</p>
                    </div>
                    <div class="diferencial">
                        <span class="icone-box"><img src="app/css/img/icone-raio.png" alt=""></span>
                        <h3>Rapidez</h3>
                        <strong>Contratação simplificada</strong>
                        <p>Pesquise, compare e encontre serviços rapidamente.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- BANNER PRESTADOR -->
        <section class="prestador">
            <div class="wrap">
                <div class="prestador-box">
                    <div class="prestador-texto">
                        <h2>Quer ganhar mais renda e visibilidade?</h2>
                        <p>Cadastre-se como profissional, receba solicitações de clientes próximos e acompanhe seus números em um painel completo.</p>
                        <a class="btn btn-light" href="?route=prestador-form">
                            <img src="app/css/img/logo.png" alt="" width="28" height="28">
                            Virar profissional do FastService
                        </a>
                    </div>

                    <div class="depoimentos">
                        <div class="depoimento">
                            <div class="depoimento-topo">
                                <img class="avatar" src="app/css/img/icone-user.png" alt="">
                                <strong>Mario Fernandez</strong>
                                <span class="nota"><img src="app/css/img/icone-estrela.png" alt="Nota">4.9</span>
                            </div>
                            <p>Uma das melhores decisões que tomei foi me cadastrar na FastService! Desde os primeiros dias, o número de clientes e serviços realizados só vem aumentando!</p>
                        </div>
                        <div class="depoimento">
                            <div class="depoimento-topo">
                                <img class="avatar" src="app/css/img/icone-user.png" alt="">
                                <strong>Jéssica Silva</strong>
                                <span class="nota"><img src="app/css/img/icone-estrela.png" alt="Nota">4.8</span>
                            </div>
                            <p>Sem dúvidas faz a diferença no meu serviço! Cada vez mais consigo trabalhos, queria ter feito o cadastro antes. Recomendo muito!</p>
                        </div>
                        <div class="depoimento">
                            <div class="depoimento-topo">
                                <img class="avatar" src="app/css/img/icone-user.png" alt="">
                                <strong>Cléber Santos</strong>
                                <span class="nota"><img src="app/css/img/icone-estrela.png" alt="Nota">4.7</span>
                            </div>
                            <p>Além de dar muita visibilidade, ajuda com dados: apresenta números de serviços, estatísticas em geral e outras funções.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="wrap footer-grid">
            <div>
                <img src="app/css/img/logo.png" alt="FastService">
            </div>

            <div>
                <h3>Acesso rápido</h3>
                <ul>
                    <li><a href="#como-funciona">Como funciona</a></li>
                    <li><a href="#categorias">Categorias</a></li>
                    <li><a href="#sobre">Sobre nós</a></li>
                    <li><a href="?route=prestador-form">Seja um profissional</a></li>
                </ul>
            </div>

            <div>
                <p>Somos a solução para o mercado digital, disponibilizando uma plataforma moderna que permite que o cliente receba o serviço e que o prestador receba pelo seu serviço. Tudo isso com praticidade e segurança.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026, FastService LTDA</p>
        </div>
    </footer>

</body>

</html>
