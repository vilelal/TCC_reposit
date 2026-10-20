<?php
$logado = isset($_SESSION["id"]);
$tipo =$_SESSION["tipo"] ?? "";
$nome = htmlspecialchars($_SESSION["nome"] ?? "", ENT_QUOTES, "UTF-8");

$ehPrestador =$tipo === "prestador";
$ehAdmin =$tipo === "Admin";
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
            <a href="" class="funcionamento">Como funciona</a>
            <a href="" class="servico">Serviços</a>
            <?php if (isset($_SESSION["id_cliente"])): ?>
            <a href="?route=lista-servicos-cliente" class="servico">Minhas Solicitações</a>
            <?php endif; ?>
        </nav>

        <div class="nav-actions">
            <?php if ($logado): ?>
                <a href="?route=chat" class="btn-header btn-ghost btn-sm hide-sm">Chat</a>
                <?php if ($ehAdmin): ?>
                    <a href="?route=painel-admin" class="btn-header btn-ghost btn-sm hide-sm">Painel</a>
                <?php elseif ($ehPrestador): ?>
                    <a href="?route=dashboard" class="btn-header btn-primary btn-sm">Meu painel</a>
                <?php else: ?>
                    <a href="?route=perfil" class="btn-header btn-primary btn-sm">Meu perfil</a>
                <?php endif; ?>
                <a href="?route=logout" class="btn-header btn-ghost btn-sm">Sair</a>
            <?php else: ?>
                <a href="?route=login-form" class="btn-header btn-ghost btn-sm">Entrar</a>
                <a href="?route=prestador-form" class="btn-header btn-primary btn-sm">Seja um profissional</a>
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

            <div class="steps-conteiner">
                <!-- PASSO 1: Seleção de Categoria por Chips -->
                <div id="passo-1" class="step ativa">
                    <h2>1. Escolha a Categoria</h2>
                    
                    <p class="rotulo" id="rotulo-categoria">Categoria do Serviço:</p>
                    <div class="container-chips" id="container-categorias" role="group" aria-labelledby="rotulo-categoria">
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as$cat): ?>
                                <button type="button" class="chip-cat" data-id="<?= (int)$cat['PK_id_TB_categoria'] ?>" aria-pressed="false">
                                    <?= htmlspecialchars($cat['nome_TB_categoria']) ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" id="input-categoria" name="FK_id_TB_categoria" required>

                    <p id="erro-passo-1" class="erro-mensagem">Por favor, selecione uma categoria antes de prosseguir.</p>
                    <div class="controls">
                        <br><br>
                        <button class="next-btn" type="button" onclick="avancarPasso(1, 2)">Próximo</button>
                    </div>
                </div>

                <!-- PASSO 2: Especificar Serviço, Urgência (em Chips) e Orçamento -->
                <div id="passo-2" class="step">
                    <h2>2. Detalhes e Urgência</h2>
                    
                    <p class="rotulo">Serviço Específico:</p>
                    <div class="container-chips" id="container-servicos">
                        <p class="dica">Escolha uma categoria no passo anterior para ver os serviços disponíveis.</p>
                    </div>
                    <input type="hidden" id="input-servico" name="FK_id_TB_servico" required>

                    <!-- Urgência em formato de Chips -->
                    <p class="rotulo" style="margin-top: 20px;">Urgência do Serviço:</p>
                    <div class="container-chips" id="container-urgencia" role="group">
                        <button type="button" class="chip-urgencia" data-valor="urgente" aria-pressed="false">
                            <span class="chip-corpo">Urgente (Ainda Hoje / Emergência)</span>
                        </button>
                        <button type="button" class="chip-urgencia" data-valor="moderada" aria-pressed="false">
                            <span class="chip-corpo">Moderada (Próximos 2 a 3 dias)</span>
                        </button>
                        <button type="button" class="chip-urgencia" data-valor="flexivel" aria-pressed="false">
                            <span class="chip-corpo">Flexível (Sem data fixa)</span>
                        </button>
                    </div>
                    <input type="hidden" id="input-urgencia" name="urgencia" required>

                    <label for="data_agendamento" style="margin-top: 15px; display:block;">Data/Hora Desejada:</label>
                    <input type="datetime-local" id="data_agendamento" name="data_agendamento" required>

                    <br><br>
                    <label>
                        Desejo negociar um orçamento personalizado com o prestador
                        <input type="checkbox" name="solicitar_orcamento" value="1">
                    </label>

                    <label style="margin-top: 15px; display:block;">Descrição adicional do problema:</label>
                    <textarea name="descricao" rows="3" placeholder="Descreva brevemente o que precisa ser feito..."></textarea>

                    <p id="erro-passo-2" class="erro-mensagem">Por favor, preencha o serviço, a urgência e a data desejada.</p>

                    <br><br>
                    <div class="controls">
                        <button type="button" class="prev-btn" onclick="mudarPasso(2, 1)">Voltar</button>
                        <button type="button" class="next-btn" onclick="avancarPasso(2, 3)">Próximo</button>
                    </div>
                </div>

                <!-- PASSO 3: Confirmação da Busca -->
                <div id="passo-3" class="step">
                    <h2>3. Localizar Prestadores Próximos</h2>
                    <p>Usaremos a cidade cadastrada no seu perfil para encontrar os prestadores mais próximos de você.</p>
                    
                    <br>
                    <div class="controls">
                        <button type="button" class="prev-btn" onclick="mudarPasso(3, 2)">Voltar</button>
                        <button type="submit" class="submit-btn">Ver Prestadores Próximos</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const todosServicos = <?= json_encode($todosServicos ?? []) ?>;
    const iconeCheck = '<svg class="chip-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>';

    function esc(texto) {
        const el = document.createElement("span");
        el.textContent = texto;
        return el.innerHTML;
    }

    function atualizarProgresso() {
        const progress = document.querySelector(".progress");
        const stepIndicators = document.querySelectorAll('.progress-conteiner li');
        const steps = document.querySelectorAll('.step');

        let currentStep = 0;

        steps.forEach((step, index) => {
            if (step.classList.contains('ativa')) {
                currentStep = index;
            }
        });

        const width = currentStep / (stepIndicators.length - 1);
        if (progress) {
            progress.style.transform = `translateY(-50%) scaleX(${width})`;
        }

        stepIndicators.forEach((indicator, index) => {
            indicator.classList.remove('current', 'done');

            if (index === currentStep) {
                indicator.classList.add('current');
            } else if (index < currentStep) {
                indicator.classList.add('done');
            }
        });
    }

    function mudarPasso(de, para) {
        const passoAtual = document.getElementById('passo-' + de);
        const proximoPasso = document.getElementById('passo-' + para);

        if (passoAtual) passoAtual.classList.remove('ativa');
        if (proximoPasso) proximoPasso.classList.add('ativa');

        atualizarProgresso();
    }

    function avancarPasso(atual, proximo) {
        let valido = true;

        if (atual === 1) {
            const categoriaId = document.getElementById('input-categoria').value;
            if (!categoriaId) {
                document.getElementById('erro-passo-1').style.display = 'block';
                valido = false;
            }
        } 
        else if (atual === 2) {
            const servicoId = document.getElementById('input-servico').value;
            const urgencia = document.getElementById('input-urgencia').value;
            const data = document.getElementById('data_agendamento').value;

            if (!servicoId || !urgencia || !data) {
                document.getElementById('erro-passo-2').style.display = 'block';
                valido = false;
            }
        }

        if (valido) {
            mudarPasso(atual, proximo);
        }
    }

    function renderizarServicosChips(categoriaId) {
        const containerServicos = document.getElementById('container-servicos');
        const servicosFiltrados = todosServicos.filter(s => String(s.FK_id_TB_categoria) === String(categoriaId));

        document.getElementById('input-servico').value = "";

        if (servicosFiltrados.length === 0) {
            containerServicos.innerHTML = "<p class='dica'>Nenhum serviço disponível para esta categoria.</p>";
            return;
        }

        containerServicos.innerHTML = servicosFiltrados.map(servico => {
            const id = String(servico.PK_id_TB_servico);
            return `
            <button type="button" class="chip-servico" data-id="${esc(id)}" aria-pressed="false">
                <span class="chip-corpo">${iconeCheck}${esc(servico.nome_TB_servico)}</span>
            </button>`;
        }).join("");
    }

    // Clique na Categoria (Passo 1)
    document.getElementById("container-categorias").addEventListener("click", function (e) {
        const botao = e.target.closest(".chip-cat");
        if (!botao) return;

        this.querySelectorAll(".chip-cat").forEach(b => b.setAttribute("aria-pressed", "false"));
        botao.setAttribute("aria-pressed", "true");

        const categoriaId = botao.getAttribute("data-id");
        document.getElementById('input-categoria').value = categoriaId;
        document.getElementById('erro-passo-1').style.display = 'none';

        renderizarServicosChips(categoriaId);
    });

    // Clique no Serviço Específico (Passo 2)
    document.getElementById("container-servicos").addEventListener("click", function (e) {
        const botao = e.target.closest(".chip-servico");
        if (!botao) return;

        this.querySelectorAll(".chip-servico").forEach(b => b.setAttribute("aria-pressed", "false"));
        botao.setAttribute("aria-pressed", "true");

        const servicoId = botao.getAttribute("data-id");
        document.getElementById('input-servico').value = servicoId;
        document.getElementById('erro-passo-2').style.display = 'none';
    });

    // Clique na Urgência (Passo 2)
    document.getElementById("container-urgencia").addEventListener("click", function (e) {
        const botao = e.target.closest(".chip-urgencia");
        if (!botao) return;

        this.querySelectorAll(".chip-urgencia").forEach(b => b.setAttribute("aria-pressed", "false"));
        botao.setAttribute("aria-pressed", "true");

        const valorUrgencia = botao.getAttribute("data-valor");
        document.getElementById('input-urgencia').value = valorUrgencia;
        document.getElementById('erro-passo-2').style.display = 'none';
    });

    document.addEventListener('DOMContentLoaded', () => {
        atualizarProgresso();
    });
</script>
</body>
</html>