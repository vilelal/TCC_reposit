<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Serviço</title>
    <link rel="stylesheet" href="app/css/styleCad.css">
    <style>
        .etapa-wizard { display: none; }
        .etapa-wizard.ativa { display: block; }
        .btn-voltar { background-color: #6c757d; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="cadastro-etapa">
        <h1>Qual serviço você precisa?</h1>

        <form action="?route=buscar-prestadores-proximos" method="POST">
            
            <!-- PASSO 1: Seleção de Categoria -->
            <div id="passo-1" class="etapa-wizard ativa">
                <h2>1. Escolha a Categoria</h2>
                <label for="categoria">Categoria do Serviço:</label>
                <select id="select-categoria" name="FK_id_TB_categoria" onchange="filtrarServicos()" required>
                    <option value="" disabled selected>Selecione uma categoria...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['PK_id_TB_categoria'] ?>"><?= htmlspecialchars($cat['nome_TB_categoria']) ?></option>
                    <?php endforeach; ?>
                </select>

                <br><br>
                <button type="button" onclick="proximoPasso(1, 2)">Próximo</button>
            </div>

            <!-- PASSO 2: Especificar Serviço, Urgência e Orçamento -->
            <div id="passo-2" class="etapa-wizard">
                <h2>2. Detalhes e Urgência</h2>
                
                <label for="servico">Serviço Específico:</label>
                <select id="select-servico" name="FK_id_TB_servico" required>
                    <option value="" disabled selected>Selecione primeiro o serviço</option>
                    <?php foreach ($todosServicos as $servico): ?>
                        <!-- Guardamos a categoria no atributo 'data-categoria' para o JS filtrar -->
                        <option value="<?= $servico['PK_id_TB_servico'] ?>" data-categoria="<?= $servico['FK_id_TB_categoria'] ?>" style="display:none;">
                            <?= htmlspecialchars($servico['nome_TB_servico']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Urgência do Serviço:</label>
                <select name="urgencia" required>
                    <option value="urgente">Urgente (Ainda Hoje / Emergência)</option>
                    <option value="moderada" selected>Moderada (Nos próximos 2 a 3 dias)</option>
                    <option value="flexivel">Flexível (Sem data fixa)</option>
                </select>

                <label for="data_agendamento">Data/Hora Desejada:</label>
                <input type="datetime-local" name="data_agendamento" required>

                <br><br>
                <label>
                    <input type="checkbox" name="solicitar_orcamento" value="1">
                    Desejo negociar um orçamento personalizado com o prestador
                </label>

                <label style="margin-top: 10px; display:block;">Descrição adicional do problema:</label>
                <textarea name="descricao" rows="3" placeholder="Descreva brevemente o que precisa ser feito..."></textarea>

                <br><br>
                <button type="button" class="btn-voltar" onclick="proximoPasso(2, 1)">Voltar</button>
                <button type="button" onclick="proximoPasso(2, 3)">Próximo</button>
            </div>

            <!-- PASSO 3: Confirmação da Busca -->
            <div id="passo-3" class="etapa-wizard">
                <h2>3. Localizar Prestadores Próximos</h2>
                <p>Usaremos o endereço cadastrado no seu perfil para encontrar os prestadores mais próximos de você.</p>
                
                <button type="button" class="btn-voltar" onclick="proximoPasso(3, 2)">Voltar</button>
                <button type="submit" class="btn-solicitar">Ver Prestadores Próximos</button>
            </div>

        </form>
    </div>
</div>

<script>
    // Filtra os serviços no select de acordo com a categoria escolhida
    function filtrarServicos() {
        const categoriaId = document.getElementById('select-categoria').value;
        const selectServico = document.getElementById('select-servico');
        const options = selectServico.querySelectorAll('option');

        selectServico.value = "";
        
        options.forEach(option => {
            if (option.getAttribute('data-categoria') === categoriaId) {
                option.style.display = 'block';
            } else if (option.value !== "") {
                option.style.display = 'none';
            }
        });
    }

    // Alterna entre as abas do Wizard
    function proximoPasso(atual, proximo) {
        document.getElementById('passo-' + atual).classList.remove('ativa');
        document.getElementById('passo-' + proximo).classList.add('ativa');
    }
</script>

</body>
</html>