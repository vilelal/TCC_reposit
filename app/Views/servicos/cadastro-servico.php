<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Serviço e Prestador</title>
    <link rel="stylesheet" href="app/css/styleCad.css">
</head>

<body>

<div class="container">
    <div class="cadastro-etapa">
        <h1>Escolha o Serviço e o Prestador</h1>

        <!-- Passo 1: Filtrar por Categoria / Serviço -->
        <form action="?route=filtrar-prestadores" method="GET">
            <input type="hidden" name="route" value="filtrar-prestadores">
            
            <label for="categoria">Categoria do Serviço:</label>
            <select name="FK_id_TB_categoria" onchange="this.form.submit()" required>
                <option value="" disabled selected>Selecione uma categoria</option>
                <?php foreach ($string_categorias as $cat): ?>
                    <option value="<?= $cat['PK_id_TB_categoria'] ?>" <?= (isset($_GET['FK_id_TB_categoria']) && $_GET['FK_id_TB_categoria'] == $cat['PK_id_TB_categoria']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nome_TB_categoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Passo 2: Listagem de Prestadores Disponíveis na Região -->
        <?php if (!empty($prestadoresDisponiveis)): ?>
            <div class="lista-prestadores">
                <h3>Prestadores Disponíveis na sua Região</h3>
                
                <?php foreach ($prestadoresDisponiveis as $prestador): ?>
                    <div class="card-prestador" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                        <!-- Etiqueta de Área/Especialidade -->
                        <span class="etiqueta-area" style="background: #ff6600; color: #fff; padding: 3px 8px; font-size: 12px; border-radius: 4px;">
                            <?= htmlspecialchars($prestador['cidade_TB_prestadorPerfil']) ?>
                        </span>

                        <h4><?= htmlspecialchars($prestador['nome_TB_prestador']) ?></h4>
                        <p><strong>Serviço:</strong> <?= htmlspecialchars($prestador['nome_TB_servico']) ?></p>
                        <p><strong>Preço Padrão:</strong> R$ <?= number_format($prestador['preco_customizado_TB_prestadorServico'] ?? $prestador['precoPadrao_TB_servico'], 2, ',', '.') ?></p>

                        <!-- Formulário para Solicitação / Orçamento -->
                        <form action="?route=salvar-pedido" method="POST">
                            <input type="hidden" name="FK_id_TB_prestadorServico" value="<?= $prestador['PK_id_TB_prestadorServico'] ?>">
                            
                            <label>
                                <input type="checkbox" name="solicitar_orcamento" value="1"> Desejo solicitar orçamento personalizado
                            </label>

                            <br><br>
                            <label for="data_agendamento">Data desejada:</label>
                            <input type="datetime-local" name="data_agendamento" required>

                            <button type="submit" class="btn-solicitar">Contratar / Solicitar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($_GET['FK_id_TB_categoria'])): ?>
            <p>Nenhum prestador encontrado para esta categoria na sua região.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>