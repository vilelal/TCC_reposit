<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="app/css/styleAdmPanel.css">

</head>
<body>

<div class="admin-container">
    <h2>Painel Administrativo da FastService</h2>
    <p>Gerenciamento de usuários e catálogo de serviços do sistema.</p>

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

        <div class="tabela-toolbar">
            <input type="text" id="filtroUsuarios" placeholder="Pesquisar por ID, email ou tipo..." autocomplete="off">
        </div>

        <table id="tabelaUsuarios">
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
                    <?php $idAdminLogado = (int) ($_SESSION['id'] ?? 0); ?>
                    <?php foreach ($listaUsuarios as $usuario): ?>
                        <tr>
                            <td>#<?= (int) $usuario['PK_id_TB_usuario'] ?></td>
                            <td><?= htmlspecialchars($usuario['email_TB_usuario']) ?></td>
                            <td><strong><?= htmlspecialchars($usuario['tipo_TB_usuario']) ?></strong></td>
                            <td>
                                <!-- Evita que o admin bane a si mesmo -->
                                <?php if ((int) $usuario['PK_id_TB_usuario'] !== $idAdminLogado): ?>
                                    <form action="?route=admin-banir-usuario" method="POST" onsubmit="return confirm('Tem certeza que deseja banir este usuário?');" style="margin: 0;">
                                        <input type="hidden" name="id_usuario" value="<?= (int) $usuario['PK_id_TB_usuario'] ?>">
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
                        <td colspan="4">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p id="semResultado" class="sem-resultado" hidden>Nenhum usuário corresponde à pesquisa.</p>

        <div class="paginacao" id="paginacaoUsuarios"></div>
    </div>
</div>

<script>
(function () {
    const POR_PAGINA = 10;
    const tabela = document.getElementById('tabelaUsuarios');
    const corpo = tabela.tBodies[0];
    const linhasOriginais = Array.from(corpo.querySelectorAll('tr'))
        .filter(tr => !tr.querySelector('td[colspan]'))
        .sort((a, b) => {
            const emailA = a.cells[1].textContent.trim();
            const emailB = b.cells[1].textContent.trim();
            return emailA.localeCompare(emailB, 'pt-BR', { sensitivity: 'base' });
        });

    linhasOriginais.forEach(tr => corpo.appendChild(tr));
    const campoFiltro = document.getElementById('filtroUsuarios');
    const semResultado = document.getElementById('semResultado');
    const paginacao = document.getElementById('paginacaoUsuarios');

    let paginaAtual = 1;

    function linhasFiltradas() {
        const termo = campoFiltro.value.trim().toLowerCase();
        if (!termo) return linhasOriginais;
        return linhasOriginais.filter(tr => tr.textContent.toLowerCase().includes(termo));
    }

    function renderizar() {
        const resultado = linhasFiltradas();
        const totalPaginas = Math.max(1, Math.ceil(resultado.length / POR_PAGINA));
        paginaAtual = Math.min(paginaAtual, totalPaginas);

        linhasOriginais.forEach(tr => tr.style.display = 'none');

        const inicio = (paginaAtual - 1) * POR_PAGINA;
        const paginaAtualLinhas = resultado.slice(inicio, inicio + POR_PAGINA);
        paginaAtualLinhas.forEach(tr => tr.style.display = '');

        semResultado.hidden = resultado.length !== 0;
        tabela.style.display = resultado.length === 0 ? 'none' : '';

        renderizarPaginacao(totalPaginas);
    }

    function renderizarPaginacao(totalPaginas) {
        paginacao.innerHTML = '';
        if (totalPaginas <= 1) return;

        const criarBotao = (rotulo, pagina, ativo, desabilitado) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = rotulo;
            btn.className = 'pagina-btn' + (ativo ? ' ativo' : '');
            btn.disabled = !!desabilitado;
            btn.addEventListener('click', () => {
                paginaAtual = pagina;
                renderizar();
            });
            return btn;
        };

        paginacao.appendChild(criarBotao('‹', paginaAtual - 1, false, paginaAtual === 1));
        for (let p = 1; p <= totalPaginas; p++) {
            paginacao.appendChild(criarBotao(String(p), p, p === paginaAtual, false));
        }
        paginacao.appendChild(criarBotao('›', paginaAtual + 1, false, paginaAtual === totalPaginas));
    }

    campoFiltro.addEventListener('input', () => {
        paginaAtual = 1;
        renderizar();
    });

    renderizar();
})();
</script>

</body>
</html>