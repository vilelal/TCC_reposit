<?php
$logado = isset($_SESSION["id"]);
$tipo = $_SESSION["tipo"] ?? "";
$nome = htmlspecialchars($_SESSION["nome"] ?? "", ENT_QUOTES, "UTF-8");

$ehPrestador = $tipo === "prestador";
$ehAdmin = $tipo === "Admin";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Serviço</title>
 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="app/css/wizard.css">
    
    <link rel="stylesheet" href="app/css/detalhe.css">
    
</head>
<body>

<header class="site-header">
    <div class="header-wrap">

        <a href="?route=home" class="brand" aria-label="FastService - página inicial">
            <img src="app/css/img/logo.png" alt="FastService">
            <span>FastService</span>
        </a>

        <nav class="nav-links" aria-label="Principal">
            <a href=""  class="funcionamento">Como funciona</a>
            <a href=""  class="servico">Serviços</a>
            <!-- links para cliente logado -->
            <?php if (isset($_SESSION["id_cliente"])): ?>
            <a href="?route=lista-servicos-cliente"  class="servico">Minhas Solicitações</a>
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
<div class="container">
    <div class="cadastro-etapa">
        <h1>Qual serviço você precisa?</h1>


        <form id="form-wizard" action="?route=buscar-proximos" method="POST">
            <div class="progress-conteiner">
                    <div class="progress"></div>
                <ol>
                    <li class="current">Categoria</li>
                    <li>Detalhes</li>
                    <li>Buscar</li>
                </ol>
                </div>
            <!-- PASSO 1: Seleção de Categoria -->
        <div class="steps-conteiner">
            <div id="passo-1" class="step ativa">
                <h2>1. Escolha a Categoria</h2>
                

                <p class="rotulo-chips" id="rotulo-categoria">Categoria do Serviço:</p>
                <div class="container-chips" role="radiogroup" aria-labelledby="rotulo-categoria">
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <label class="chip chip-cat">
                                <input type="radio" name="FK_id_TB_categoria" value="<?= (int) $cat['PK_id_TB_categoria'] ?>" onchange="filtrarServicos()" required>
                                <span class="chip-corpo"><?= htmlspecialchars($cat['nome_TB_categoria']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <p id="erro-passo-1" class="erro-mensagem">Por favor, selecione uma categoria antes de prosseguir.</p>
                <div class= "controls">
                <br><br>
                <button class="next-btn" type="button" onclick="avancarPasso(1, 2)">Próximo</button>
                </div>
            </div>
           

            <!-- PASSO 2: Especificar Serviço, Urgência e Orçamento -->
            <div id="passo-2" class="step">
                <h2>2. Detalhes e Urgência</h2>
                
                <p class="rotulo-chips" id="rotulo-servico">Serviço Específico:</p>
                <div class="container-chips" id="container-servicos" role="radiogroup" aria-labelledby="rotulo-servico">
                    <p class="dica" id="dica-servicos">Selecione primeiro a categoria.</p>
                    <?php if (!empty($todosServicos)): ?>
                        <?php foreach ($todosServicos as $servico): ?>
                            <label class="chip chip-servico" data-categoria="<?= (int) $servico['FK_id_TB_categoria'] ?>" hidden>
                                <input type="radio" name="FK_id_TB_servico" value="<?= (int) $servico['PK_id_TB_servico'] ?>">
                                <span class="chip-corpo">
                                    <svg class="chip-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                                    <?= htmlspecialchars($servico['nome_TB_servico']) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <p class="rotulo-chips" id="rotulo-urgencia">Urgência do Serviço:</p>
                <div class="container-chips" role="radiogroup" aria-labelledby="rotulo-urgencia">
                    <label class="chip chip-urgencia">
                        <input type="radio" name="urgencia" value="urgente" required>
                        <span class="chip-corpo"><svg class="chip-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>Urgente (Ainda Hoje / Emergência)</span>
                    </label>
                    <label class="chip chip-urgencia">
                        <input type="radio" name="urgencia" value="moderada" checked required>
                        <span class="chip-corpo"><svg class="chip-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>Moderada (Nos próximos 2 a 3 dias)</span>
                    </label>
                    <label class="chip chip-urgencia">
                        <input type="radio" name="urgencia" value="flexivel" required>
                        <span class="chip-corpo"><svg class="chip-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>Flexível (Sem data fixa)</span>
                    </label>
                </div>

                <label for="data_agendamento">Data/Hora Desejada:</label>
                <input type="datetime-local" id="data_agendamento" name="data_agendamento" required>

                <br><br>

                <label style="margin-top: 15px; display:block;">Descrição adicional do problema:</label>
                <textarea name="descricao" rows="3" placeholder="Descreva brevemente o que precisa ser feito..."></textarea>

                <p id="erro-passo-2" class="erro-mensagem">Por favor, preencha o serviço e a data desejada.</p>

                <br><br>
                   <div class= "controls">
                        <button type="button" class="prev-btn" onclick="mudarPasso(2, 1)">Voltar</button>
                        <button type="button" class="next-btn"onclick="avancarPasso(2, 3)">Próximo</button>
                    </div>
            </div>

            <!-- PASSO 3: Confirmação da Busca -->
            <div id="passo-3" class="step">
                <h2>3. Localizar Prestadores Próximos</h2>
                <p>Usaremos a cidade cadastrada no seu perfil para encontrar os prestadores mais próximos de você.</p>
                
                <br>
                 <div class= "controls">
                <button type="button" class="prev-btn" onclick="mudarPasso(3, 2)">Voltar</button>
                <button type="submit" class="submit-btn">Ver Prestadores Próximos</button>
                   </div>
            </div>
        </div>

        </form>
    </div>
</div>

 <script>
    // 1. Função que recalcula a posição da barra e as cores dos círculos
    function atualizarProgresso() {
        const progress = document.querySelector(".progress");
        const stepIndicators = document.querySelectorAll('.progress-conteiner li');
        const steps = document.querySelectorAll('.step');

        let currentStep = 0;

        // Identifica qual passo tem a classe 'ativa'
        steps.forEach((step, index) => {
            if (step.classList.contains('ativa')) {
                currentStep = index;
            }
        });

        // Atualiza a largura da barra azul
        const width = currentStep / (stepIndicators.length - 1);
        if (progress) {
            progress.style.transform = `translateY(-50%) scaleX(${width})`;
        }

        // Atualiza as classes 'current' e 'done' nos <li> do indicador
        stepIndicators.forEach((indicator, index) => {
            indicator.classList.remove('current', 'done');

            if (index === currentStep) {
                indicator.classList.add('current');
            } else if (index < currentStep) {
                indicator.classList.add('done');
            }
        });
    }

    // 2. Transição entre as etapas
    function mudarPasso(de, para) {
        const passoAtual = document.getElementById('passo-' + de);
        const proximoPasso = document.getElementById('passo-' + para);

        if (passoAtual) passoAtual.classList.remove('ativa');
        if (proximoPasso) proximoPasso.classList.add('ativa');

        // Dispara a atualização visual dos círculos na mesma hora
        atualizarProgresso();
    }

    // 3. Validação dos campos
    function avancarPasso(atual, proximo) {
        let valido = true;

        if (atual === 1) {
            const categoria = document.querySelector('input[name="FK_id_TB_categoria"]:checked');
            if (!categoria) {
                document.getElementById('erro-passo-1').style.display = 'block';
                valido = false;
            }
        } 
        else if (atual === 2) {
            const servico = document.querySelector('input[name="FK_id_TB_servico"]:checked');
            const data = document.getElementById('data_agendamento').value;

            if (!servico || !data) {
                document.getElementById('erro-passo-2').style.display = 'block';
                valido = false;
            }
        }

        if (valido) {
            mudarPasso(atual, proximo);
        }
    }

    // 4. Filtro de serviços: mostra só os chips da categoria escolhida
    function filtrarServicos() {
        const categoria = document.querySelector('input[name="FK_id_TB_categoria"]:checked');
        const categoriaId = categoria ? categoria.value : "";
        const chips = document.querySelectorAll('.chip-servico');
        let visiveis = 0;

        chips.forEach(chip => {
            const mostrar = chip.getAttribute('data-categoria') === categoriaId;
            chip.hidden = !mostrar;
            if (mostrar) visiveis++;

            // Ao trocar de categoria, limpa o serviço escolhido antes
            chip.querySelector('input').checked = false;
        });

        const dica = document.getElementById('dica-servicos');
        dica.hidden = visiveis > 0;
        if (visiveis === 0) {
            dica.textContent = categoriaId
                ? 'Nenhum serviço disponível para esta categoria.'
                : 'Selecione primeiro a categoria.';
        }

        document.getElementById('erro-passo-1').style.display = 'none';
    }

    // Garante a execução assim que o HTML carregar
    document.addEventListener('DOMContentLoaded', () => {
        atualizarProgresso();
    });
</script>
</body>
</html>