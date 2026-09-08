<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestadores Encontrados</title>
    <style>
        .cards-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }
        .card-prestador {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 18px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .info-prestador {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .foto-prestador {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }
        .detalhes-prestador h3 {
            margin: 0 0 5px 0;
        }
        .detalhes-prestador p {
            margin: 3px 0;
            color: #555;
        }
        .btn-selecionar {
            background-color: #28a745;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-selecionar:hover {
            background-color: #218838;
        }
        .sem-resultados {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Prestadores Encontrados na sua Região</h1>
    <p>Selecione um profissional abaixo para confirmar a sua solicitação de serviço.</p>

    <?php if (!empty($prestadoresEncontrados)): ?>
        <div class="cards-container">
            <?php foreach ($prestadoresEncontrados as $prestador): ?>
                <div class="card-prestador">
                    <div class="info-prestador">
                        <img 
                            src="<?= !empty($prestador['foto']) ? htmlspecialchars($prestador['foto']) : 'app/images/default-avatar.png' ?>" 
                            alt="Foto do Prestador" 
                            class="foto-prestador"
                        >
                        <div class="detalhes-prestador">
                            <h3><?= htmlspecialchars($prestador['nome'] ?? $prestador['nome_prestador'] ?? 'Prestador') ?></h3>
                            <p><strong>Cidade/Bairro:</strong> <?= htmlspecialchars($prestador['cidade'] ?? 'Não informada') ?></p>
                            <p><strong>Avaliação:</strong> ⭐ <?= htmlspecialchars($prestador['avaliacao'] ?? '5.0') ?></p>
                            <p><strong>Valor Estimado:</strong> R$ <?= number_format($prestador['preco_base'] ?? $prestador['valor_total'] ?? 0, 2, ',', '.') ?></p>
                        </div>
                    </div>

                    <form action="?route=confirmar-solicitacao" method="POST">
                        <input type="hidden" name="FK_id_TB_prestadorServico" value="<?= $prestador['PK_id_TB_prestadorServico'] ?? $prestador['id'] ?>">
                        <input type="hidden" name="valor_total" value="<?= $prestador['preco_base'] ?? $prestador['valor_total'] ?? 0 ?>">
                        <button type="submit" class="btn-selecionar">Confirmar e Solicitar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="sem-resultados">
            <h3>Nenhum prestador encontrado na sua cidade no momento.</h3>
            <p>Tente selecionar outro serviço ou verifique o endereço do seu perfil.</p>
            <br>
            <a href="?route=solicitar-servico" class="btn-voltar" style="padding: 10px 15px; text-decoration: none; color: white; background: #6c757d; border-radius: 5px;">Voltar ao Formulário</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>