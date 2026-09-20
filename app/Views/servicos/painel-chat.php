<?php
$meusChats = $meusChats ?? [];

// Localiza a conversa aberta para exibir contato e serviço no cabeçalho
$chatAtivo = null;
if (!empty($solicitacaoAtiva)) {
    foreach ($meusChats as $chat) {
        if ($chat['id_solicitacao'] == $solicitacaoAtiva) {
            $chatAtivo = $chat;
            break;
        }
    }
}
$fotoPadrao = 'app/css/img/icone-user.png';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Central de Mensagens</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/chat.css">
</head>
<body>

<div class="chat-page">
    <header class="header">
        <div class="header-esquerda">
            <a href="?route=">
                <img src="app/css/img/logo.png" alt="Início" class="logo">
            </a>
            <h2>Central de mensagens</h2>
        </div>
        <div class="header-acoes">
            <a href="?route=notificacoes" class="sino-link" title="Notificações">
                <img src="app/css/img_dashboard/icone-sino.png" alt="Notificações" class="icone-sino">
            </a>
            <?php if (isset($_SESSION["id"])): ?>
                <span class="user-name"><?= htmlspecialchars($_SESSION['nome']) ?></span>
            <?php endif; ?>
            <img class="verificado" src="app/css/img_dashboard/icone-check.png" alt="Verificado">
        </div>
    </header>

    <main class="chat-main">
        <div class="painel-chat-container <?= !empty($solicitacaoAtiva) ? 'tem-chat-ativo' : '' ?>">

            <!-- BARRA LATERAL -->
            <aside class="sidebar-chats">
                <div class="sidebar-header">
                    <span>Suas conversas</span>
                    <?php if (!empty($meusChats)): ?>
                        <span class="sidebar-contador"><?= count($meusChats) ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($meusChats)): ?>
                    <?php foreach ($meusChats as $chat): ?>
                        <?php $isAtivo = (isset($solicitacaoAtiva) && $solicitacaoAtiva == $chat['id_solicitacao']); ?>

                        <a href="?route=chat&id_solicitacao=<?= (int) $chat['id_solicitacao'] ?>"
                           class="item-chat <?= $isAtivo ? 'ativo' : '' ?>">
                            <div class="info-chat-lista">
                                <img src="<?= !empty($chat['foto_contato']) ? htmlspecialchars($chat['foto_contato']) . '?v=' . time() : $fotoPadrao ?>"
                                     class="foto-chat" alt="Foto de <?= htmlspecialchars($chat['nome_contato']) ?>">

                                <div class="texto-chat">
                                    <div class="nome-e-role">
                                        <strong><?= htmlspecialchars($chat['nome_contato']) ?></strong>
                                        <?php if (!empty($chat['tipo_TB_usuario'])): ?>
                                            <span class="badge-role"><?= htmlspecialchars($chat['tipo_TB_usuario']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p><?= htmlspecialchars($chat['ultima_mensagem'] ?? 'Sem mensagens...') ?></p>
                                </div>

                                <?php if ($chat['nao_lidas'] > 0): ?>
                                    <span class="badge-nao-lida"><?= $chat['nao_lidas'] ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="estado-vazio">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <h3>Nenhuma conversa</h3>
                        <p>Suas conversas aparecerão aqui quando houver uma solicitação de serviço.</p>
                    </div>
                <?php endif; ?>
            </aside>

            <!-- ÁREA PRINCIPAL: CHAT ATIVO -->
            <?php if (isset($solicitacaoAtiva) && $solicitacaoAtiva != null): ?>
                <section class="area-chat-ativo">
                    <div class="header-chat-ativo">
                        <a href="?route=chat" class="btn-voltar" title="Voltar às conversas" aria-label="Voltar às conversas">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                        </a>

                        <?php if ($chatAtivo): ?>
                            <img src="<?= !empty($chatAtivo['foto_contato']) ? htmlspecialchars($chatAtivo['foto_contato']) . '?v=' . time() : $fotoPadrao ?>"
                                 class="foto-chat" alt="">
                            <div class="header-chat-info">
                                <div class="header-chat-nome"><?= htmlspecialchars($chatAtivo['nome_contato']) ?></div>
                                <div class="header-chat-sub">
                                    <?= htmlspecialchars($chatAtivo['nome_TB_servico']) ?> · Solicitação #<?= (int) $solicitacaoAtiva ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="header-chat-info">
                                <div class="header-chat-nome">Solicitação #<?= (int) $solicitacaoAtiva ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="chat-box" class="chat-box"></div>

                    <div id="area-preview" class="area-preview">
                        <div class="preview-container">
                            <img id="img-preview" src="" alt="Pré-visualização">
                            <button type="button" id="btn-remover-preview" class="btn-remover-preview" title="Remover imagem" aria-label="Remover imagem">×</button>
                        </div>
                    </div>

                    <form id="form-msg" class="form-chat">
                        <label for="imagem-mensagem" class="btn-imagem" title="Enviar imagem">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </label>
                        <input type="file" id="imagem-mensagem" accept="image/*" hidden>

                        <input type="text" id="texto-mensagem" placeholder="Digite sua mensagem..." autocomplete="off">
                        <button type="submit" id="btn-enviar" class="btn-enviar" disabled>
                            <span>Enviar</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4z"/></svg>
                        </button>
                    </form>
                </section>

                <script>
                    const solicitacaoId = <?= json_encode($solicitacaoAtiva) ?>;
                    const meuId = <?= json_encode($_SESSION['id']) ?>;

                    const chatBox = document.getElementById('chat-box');
                    const inputTexto = document.getElementById('texto-mensagem');
                    const btnEnviar = document.getElementById('btn-enviar');

                    // Variáveis do Preview
                    const inputImagem = document.getElementById('imagem-mensagem');
                    const areaPreview = document.getElementById('area-preview');
                    const imgPreview = document.getElementById('img-preview');
                    const btnRemoverPreview = document.getElementById('btn-remover-preview');

                    let ultimaAssinatura = null;

                    function atualizarBotaoEnviar() {
                        btnEnviar.disabled = !inputTexto.value.trim() && !inputImagem.files[0];
                    }

                    function limparPreview() {
                        inputImagem.value = '';
                        areaPreview.style.display = 'none';
                        imgPreview.src = '';
                        atualizarBotaoEnviar();
                    }

                    inputTexto.addEventListener('input', atualizarBotaoEnviar);

                    inputImagem.addEventListener('change', function() {
                        const file = this.files[0];
                        if (file) {
                            imgPreview.src = URL.createObjectURL(file);
                            areaPreview.style.display = 'block';
                        }
                        atualizarBotaoEnviar();
                    });

                    btnRemoverPreview.addEventListener('click', limparPreview);

                    function parseData(valor) {
                        return new Date(String(valor).replace(' ', 'T'));
                    }

                    function rotuloDia(data) {
                        const hoje = new Date();
                        const ontem = new Date();
                        ontem.setDate(hoje.getDate() - 1);

                        if (data.toDateString() === hoje.toDateString()) return 'Hoje';
                        if (data.toDateString() === ontem.toDateString()) return 'Ontem';

                        const opcoes = { day: 'numeric', month: 'long' };
                        if (data.getFullYear() !== hoje.getFullYear()) opcoes.year = 'numeric';
                        return data.toLocaleDateString('pt-BR', opcoes);
                    }

                    function criarEstadoVazio() {
                        const div = document.createElement('div');
                        div.className = 'estado-vazio';
                        div.innerHTML = `
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <h3>Nenhuma mensagem ainda</h3>
                            <p>Envie a primeira mensagem para começar a conversa.</p>`;
                        return div;
                    }

                    function criarMensagem(msg) {
                        const div = document.createElement('div');
                        div.className = `msg ${msg.FK_id_TB_remetente == meuId ? 'minha' : 'outra'}`;

                        if (msg.imagem_TB_mensagem && msg.imagem_TB_mensagem.trim() !== '') {
                            const link = document.createElement('a');
                            link.href = msg.imagem_TB_mensagem;
                            link.target = '_blank';
                            link.rel = 'noopener';

                            const img = document.createElement('img');
                            img.src = msg.imagem_TB_mensagem;
                            img.className = 'msg-img';
                            img.alt = 'Imagem enviada';

                            link.appendChild(img);
                            div.appendChild(link);
                        }

                        if (msg.mensagem_TB_mensagem && msg.mensagem_TB_mensagem.trim() !== '') {
                            const texto = document.createElement('div');
                            texto.className = 'msg-texto';
                            texto.textContent = msg.mensagem_TB_mensagem;
                            div.appendChild(texto);
                        }

                        const info = document.createElement('div');
                        info.className = 'msg-info';
                        info.textContent = parseData(msg.data_envio_TB_mensagem)
                            .toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                        div.appendChild(info);

                        return div;
                    }

                    async function carregarMensagens(forcarRolagem = false) {
                        let mensagens;
                        try {
                            const response = await fetch(`?route=carregar-mensagens-json&id_solicitacao=${solicitacaoId}`);
                            mensagens = await response.json();
                        } catch (erro) {
                            return;
                        }

                        // Evita redesenhar (e piscar) quando nada mudou
                        const assinatura = mensagens.length + '|' + JSON.stringify(mensagens[mensagens.length - 1] || null);
                        if (assinatura === ultimaAssinatura && !forcarRolagem) return;

                        const primeiraCarga = ultimaAssinatura === null;
                        const pertoDoFim = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 80;
                        ultimaAssinatura = assinatura;

                        chatBox.innerHTML = '';

                        if (mensagens.length === 0) {
                            chatBox.appendChild(criarEstadoVazio());
                            return;
                        }

                        let diaAnterior = null;
                        mensagens.forEach(msg => {
                            const data = parseData(msg.data_envio_TB_mensagem);
                            const dia = data.toDateString();

                            if (dia !== diaAnterior) {
                                const sep = document.createElement('div');
                                sep.className = 'separador-dia';
                                sep.textContent = rotuloDia(data);
                                chatBox.appendChild(sep);
                                diaAnterior = dia;
                            }

                            chatBox.appendChild(criarMensagem(msg));
                        });

                        if (primeiraCarga || forcarRolagem || pertoDoFim) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    }

                    document.getElementById('form-msg').addEventListener('submit', async (e) => {
                        e.preventDefault();

                        const msgText = inputTexto.value.trim();
                        const imagemFile = inputImagem.files[0];

                        if (!msgText && !imagemFile) return;

                        const formData = new FormData();
                        formData.append('id_solicitacao', solicitacaoId);

                        if (msgText) formData.append('mensagem', msgText);
                        if (imagemFile) formData.append('imagem', imagemFile);

                        inputTexto.value = '';
                        limparPreview();

                        await fetch('?route=enviar-mensagem', { method: 'POST', body: formData });
                        carregarMensagens(true);
                    });

                    setInterval(carregarMensagens, 2000);
                    carregarMensagens();
                </script>

            <?php else: ?>
                <section class="area-chat-ativo">
                    <div class="chat-placeholder">
                        <div class="estado-vazio">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <h3>Bem-vindo às suas mensagens</h3>
                            <p>Selecione uma conversa na barra lateral para começar a conversar.</p>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </main>
</div>
</body>
</html>
