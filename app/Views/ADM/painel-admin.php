<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="app/css/styleCad.css">
    <style>
        .admin-container { max-width: 1000px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .admin-section { margin-bottom: 40px; }
        .admin-section h3 { margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 5px; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 0.9rem; }
        th { background: #f8f9fa; font-weight: bold; }
        
        .btn-banir { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; }
        .btn-banir:hover { background: #c82333; }
        
        .form-servico { display: flex; gap: 10px; align-items: center; background: #f8f9fa; padding: 15px; border-radius: 6px; }
        .form-servico input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .btn-salvar { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-salvar:hover { background: #218838; }
        
        .alerta { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 0.9rem; }
        .alerta-sucesso { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="admin-container">
    <h2>Painel Administrativo</h2>
    <p style="color: #666; margin-bottom: 20px;">Gerenciamento de usuários e catálogo de serviços do sistema.</p>

    <!-- SEÇÃO 1: CRIAR NOVO SERVIÇO -->
    <div class="admin-section">
        <h3>Cadastrar Novo Serviço</h3>
        
        <form action="?route=admin-criar-servico" method="POST" class="form-servico">
            <input type="text" name="nome_servico" placeholder="Nome do novo serviço (ex: Eletricista, Encanador...)" required autocomplete="off">
            <button type="submit" class="btn-salvar">Adicionar Serviço</button>
        </form>
    </div>

    <!-- SEÇÃO 2: GERENCIAR USUÁRIOS -->
    <div class="admin-section">
        <h3>Usuários Cadastrados</h3>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email / Identificação</th>
                    <th>Tipo (Role)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($listaUsuarios)): ?>
                    <?php foreach ($listaUsuarios as $usuario): ?>
                        <tr>
                            <td>#<?= $usuario['PK_id_TB_usuario'] ?></td>
                            <td><?= htmlspecialchars($usuario['email_TB_usuario']) ?></td>
                            <td><strong><?= htmlspecialchars($usuario['tipo_TB_usuario']) ?></strong></td>
                            <td>
                                <!-- Evita que o admin bane a si mesmo -->
                                <?php if ($usuario['PK_id_TB_usuario'] != $_SESSION['id']): ?>
                                    <form action="?route=admin-banir-usuario" method="POST" onsubmit="return confirm('Tem certeza que deseja banir este usuário?');" style="margin: 0;">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['PK_id_TB_usuario'] ?>">
                                        <button type="submit" class="btn-banir">Banir / Excluir</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #888; font-size: 0.8rem;">(Você)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #888;">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>