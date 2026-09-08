<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Serviço</title>
    <link rel="stylesheet" href="app/css/styleCad.css">
    <style>
        .etapa-wizard { display: none; }
        .etapa-wizard.ativa { display: block; }
        .btn-voltar { background-color: #6c757d; color: white; margin-right: 10px; }
        .erro-mensagem { color: #d9534f; font-size: 0.9rem; margin-top: 5px; display: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="cadastro-etapa">
        <h1>Qual serviço você precisa?</h1>

        <form id="form-wizard" action="?route=buscar-prestadores-proximos" method="POST">
            
            <!-- PASSO 1: Seleção de Categoria -->
            <div id="passo-1" class="etapa-wizard ativa">
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

                <br><br>
                <button type="button" onclick="avancarPasso(1, 2)">Próximo</button>
            </div>

            <!-- PASSO 2: Especificar Serviço, Urgência e Orçamento -->
            <div id="passo-2" class="etapa-wizard">
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
                <button type="button" class="btn-voltar" onclick="mudarPasso(2, 1)">Voltar</button>
                <button type="button" onclick="avancarPasso(2, 3)">Próximo</button>
            </div>

            <!-- PASSO 3: Confirmação da Busca -->
            <div id="passo-3" class="etapa-wizard">
                <h2>3. Localizar Prestadores Próximos</h2>
                <p>Usaremos a cidade cadastrada no seu perfil para encontrar os prestadores mais próximos de você.</p>
                
                <br>
                <button type="button" class="btn-voltar" onclick="mudarPasso(3, 2)">Voltar</button>
                <button type="submit" class="btn-solicitar">Ver Prestadores Próximos</button>
            </div>

        </form>
    </div>
</div>

<script>
    // Filtra as opções de serviço dinamicamente com base na categoria selecionada no Passo 1
    function filtrarServicos() {
        const categoriaId = document.getElementById('select-categoria').value;
        const selectServico = document.getElementById('select-servico');
        const options = selectServico.querySelectorAll('option');

        // Reseta a seleção do serviço
        selectServico.value = "";

        options.forEach(option => {
            const catOption = option.getAttribute('data-categoria');
            if (catOption === categoriaId) {
                option.style.display = 'block';
            } else if (catOption) {
                option.style.display = 'none';
            }
        });

        // Oculta mensagem de erro se houver
        document.getElementById('erro-passo-1').style.display = 'none';
    }

    // Valida os campos obrigatórios antes de avançar de etapa
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

    // Alterna visualmente os passos do formulário
    function mudarPasso(de, para) {
        document.getElementById('passo-' + de).classList.remove('ativa');
        document.getElementById('passo-' + para).classList.add('ativa');
    }
</script>

</body>
</html>