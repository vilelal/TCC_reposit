<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Serviço</title>
      <?php require_once "app/Views/header/header.php";?>
    <link rel="stylesheet" href="app/css/wizard.css">
    
    <link rel="stylesheet" href="app/css/detalhe.css">
    
</head>
<body>

<div class="container">
    <div class="cadastro-etapa">
        <h1>Qual serviço você precisa?</h1>


        <form id="form-wizard" action="?route=buscar-prestadores-proximos" method="POST">
            <div class="progress-conteiner">
                    <div class="progress"></div>
                <ol>
                    <li class="current">Step</li>
                    <li>Step</li>
                    <li>Step</li>
                </ol>
                </div>
            <!-- PASSO 1: Seleção de Categoria -->
        <div class="steps-conteiner">
            <div id="passo-1" class="step ativa">
                <h2>1. Escolha a Categoria</h2>
                

                <label for="select-categoria">Categoria do Serviço:</label>
                <select id="select-categoria" name="FK_id_TB_categoria" onchange="filtrarServicos()" required>
                    <option value="" disabled selected>Selecione uma categoria...</option>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['PK_id_TB_categoria'] ?>">
                                <?= htmlspecialchars($cat['nome_TB_categoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <p id="erro-passo-1" class="erro-mensagem">Por favor, selecione uma categoria antes de prosseguir.</p>
                <div class= "controls">
                <br><br>
                <button class="next-btn" type="button" onclick="avancarPasso(1, 2)">Próximo</button>
                </div>
            </div>
           

            <!-- PASSO 2: Especificar Serviço, Urgência e Orçamento -->
            <div id="passo-2" class="step">
                <h2>2. Detalhes e Urgência</h2>
                
                <label for="select-servico">Serviço Específico:</label>
                <select id="select-servico" name="FK_id_TB_servico" required>
                    <option value="" disabled selected>Selecione primeiro a categoria</option>
                    <?php if (!empty($todosServicos)): ?>
                        <?php foreach ($todosServicos as $servico): ?>
                            <option value="<?= $servico['PK_id_TB_servico'] ?>" data-categoria="<?= $servico['FK_id_TB_categoria'] ?>" style="display:none;">
                                <?= htmlspecialchars($servico['nome_TB_servico']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <label for="urgencia">Urgência do Serviço:</label>
                <select id="urgencia" name="urgencia" required>
                    <option value="urgente">Urgente (Ainda Hoje / Emergência)</option>
                    <option value="moderada" selected>Moderada (Nos próximos 2 a 3 dias)</option>
                    <option value="flexivel">Flexível (Sem data fixa)</option>
                </select>

                <label for="data_agendamento">Data/Hora Desejada:</label>
                <input type="datetime-local" id="data_agendamento" name="data_agendamento" required>

                <br><br>
                <label>
                    <input type="checkbox" name="solicitar_orcamento" value="1">
                    Desejo negociar um orçamento personalizado com o prestador
                </label>

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
            const categoria = document.getElementById('select-categoria').value;
            if (!categoria) {
                document.getElementById('erro-passo-1').style.display = 'block';
                valido = false;
            }
        } 
        else if (atual === 2) {
            const servico = document.getElementById('select-servico').value;
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

    // 4. Filtro de serviços
    function filtrarServicos() {
        const categoriaId = document.getElementById('select-categoria').value;
        const selectServico = document.getElementById('select-servico');
        const options = selectServico.querySelectorAll('option');

        selectServico.value = "";

        options.forEach(option => {
            const catOption = option.getAttribute('data-categoria');
            if (catOption === categoriaId) {
                option.style.display = 'block';
            } else if (catOption) {
                option.style.display = 'none';
            }
        });

        document.getElementById('erro-passo-1').style.display = 'none';
    }

    // Garante a execução assim que o HTML carregar
    document.addEventListener('DOMContentLoaded', () => {
        atualizarProgresso();
    });
</script>
</body>
</html>