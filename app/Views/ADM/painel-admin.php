<?php
// Ícone de check usado dentro dos chips (aparece só no chip marcado)
$iconeCheck = '<svg class="chip-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>';

// Opções de status da denúncia (valor enviado => texto exibido)
$statusDenuncia = [
    'pendente'   => 'Pendente',
    'em_analise' => 'Em Análise',
    'resolvido'  => 'Resolvido',
    'rejeitado'  => 'Rejeitado',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/styleAdmPanel.css">
</head>

<body>

    <div class="admin-container">
        <h2>Painel Administrativo da FastService</h2>
        <p>Gerenciamento de usuários, denúncias e catálogo de serviços do sistema.</p>


        <!-- SEÇÃO: CRIAR NOVA CATEGORIA -->
        <div class="admin-section">
            <h3>Cadastrar Nova Categoria</h3>

            <form action="?route=admin-criar-categoria" method="POST" class="form-servico">
                <input type="text" name="nome_categoria" placeholder="Nome da nova categoria (ex: Reformas, Tecnologia, Beleza...)" required autocomplete="off">
                <button type="submit" class="btn-salvar">Adicionar Categoria</button>
            </form>
        </div>

        <!-- SEÇÃO 1: CRIAR NOVO SERVIÇO -->
        <div class="admin-section">
            <h3>Cadastrar Novo Serviço</h3>

            <form action="?route=admin-criar-servico" method="POST" class="form-servico form-servico-coluna">
                <input type="text" name="nome_servico" placeholder="Nome do novo serviço (ex: Eletricista, Encanador...)" required autocomplete="off">

                <?php if (!empty($listaCategorias)): ?>
                    <div class="campo-chips">
                        <span class="rotulo-grupo" id="rotulo-categoria">Categoria</span>
                        <div class="chips" role="radiogroup" aria-labelledby="rotulo-categoria">
                            <?php foreach ($listaCategorias as $cat): ?>
                                <label class="chip">
                                    <input type="radio" name="id_categoria" value="<?= (int) $cat['PK_id_TB_categoria'] ?>" required>
                                    <span class="chip-corpo"><?= $iconeCheck ?><?= htmlspecialchars($cat['nome_TB_categoria']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="aviso-vazio">Cadastre uma categoria antes de adicionar serviços.</p>
                <?php endif; ?>

                <button type="submit" class="btn-salvar">Adicionar Serviço</button>
            </form>
        </div>

        <!-- SEÇÃO 2: GERENCIAR DENÚNCIAS -->
        <div class="admin-section">
            <h3>Central de Denúncias</h3>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Denunciante</th>
                        <th>Denunciado</th>
                        <th>Motivo e Detalhes</th>
                        <th>Status</th>
                        <th>Ações de Banimento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listaDenuncias)): ?>
                        <?php foreach ($listaDenuncias as $denuncia): ?>
                            <tr>
                                <td data-label="ID">#<?= (int) $denuncia['PK_id_TB_denuncia'] ?></td>

                                <td data-label="Denunciante">
                                    <strong><?= htmlspecialchars($denuncia['email_denunciante']) ?></strong><br>
                                    <small style="color: #666;">ID: #<?= (int) $denuncia['FK_id_TB_denunciante'] ?></small>
                                </td>

                                <td data-label="Denunciado">
                                    <strong><?= htmlspecialchars($denuncia['email_denunciado']) ?></strong><br>
                                    <small style="color: #666;">ID: #<?= (int) $denuncia['FK_id_TB_denunciado'] ?></small>
                                </td>

                                <td data-label="Motivo e Detalhes" style="max-width: 250px;">
                                    <strong style="color: #dc3545;"><?= htmlspecialchars($denuncia['motivo_TB_denuncia']) ?></strong>
                                    <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #444;"><?= htmlspecialchars($denuncia['descricao_TB_denuncia']) ?></p>
                                    <small style="color: #888;"><?= date('d/m/Y H:i', strtotime($denuncia['data_TB_denuncia'])) ?></small>
                                </td>

                                <td data-label="Status">
                                    <!-- Clicar em um status já salva a alteração -->
                                    <form action="?route=atualizar-status-denuncia" method="POST" style="margin: 0;">
                                        <input type="hidden" name="id_denuncia" value="<?= (int) $denuncia['PK_id_TB_denuncia'] ?>">
                                        <div class="chips chips-status" role="radiogroup" aria-label="Status da denúncia #<?= (int) $denuncia['PK_id_TB_denuncia'] ?>">
                                            <?php foreach ($statusDenuncia as $valor => $rotulo): ?>
                                                <label class="chip chip-sm">
                                                    <input type="radio" name="status" value="<?= $valor ?>" onchange="this.form.submit()" <?= $denuncia['status_TB_denuncia'] === $valor ? 'checked' : '' ?>>
                                                    <span class="chip-corpo"><?= $iconeCheck ?><?= $rotulo ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </form>
                                </td>

                                <td data-label="Ações">
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <!-- Banir Denunciado -->
                                        <form action="?route=banir-via-denuncia" method="POST" onsubmit="return confirm('Confirma o banimento do USUÁRIO DENUNCIADO?');" style="margin: 0;">
                                            <input type="hidden" name="id_denuncia" value="<?= (int) $denuncia['PK_id_TB_denuncia'] ?>">
                                            <input type="hidden" name="id_usuario_alvo" value="<?= (int) $denuncia['FK_id_TB_denunciado'] ?>">
                                            <button type="submit" class="btn-banir-denunciado">Banir Denunciado</button>
                                        </form>

                                        <!-- Banir Denunciante (Falsa Denúncia) -->
                                        <form action="?route=banir-via-denuncia" method="POST" onsubmit="return confirm('Confirma o banimento do DENUNCIANTE por má-fé / falsa denúncia?');" style="margin: 0;">
                                            <input type="hidden" name="id_denuncia" value="<?= (int) $denuncia['PK_id_TB_denuncia'] ?>">
                                            <input type="hidden" name="id_usuario_alvo" value="<?= (int) $denuncia['FK_id_TB_denunciante'] ?>">
                                            <button type="submit" class="btn-banir-denunciante">Banir Denunciante</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #888;">Nenhuma denúncia registrada até o momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- SEÇÃO 3: GERENCIAR USUÁRIOS -->
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
                                <td data-label="ID">#<?= (int) $usuario['PK_id_TB_usuario'] ?></td>
                                <td data-label="Email"><?= htmlspecialchars($usuario['email_TB_usuario']) ?></td>
                                <td data-label="Tipo"><strong><?= htmlspecialchars($usuario['tipo_TB_usuario']) ?></strong></td>
                                <td data-label="Ações">
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
        (function() {
            const POR_PAGINA = 10;
            const tabela = document.getElementById('tabelaUsuarios');
            if (!tabela) return;

            const corpo = tabela.tBodies[0];
            const linhasOriginais = Array.from(corpo.querySelectorAll('tr'))
                .filter(tr => !tr.querySelector('td[colspan]'))
                .sort((a, b) => {
                    const emailA = a.cells[1].textContent.trim();
                    const emailB = b.cells[1].textContent.trim();
                    return emailA.localeCompare(emailB, 'pt-BR', {
                        sensitivity: 'base'
                    });
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