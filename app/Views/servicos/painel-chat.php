<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Central de Mensagens</title>
    <link rel="stylesheet" href="app/css/styleCad.css">
     <link rel="stylesheet" href="app/css/chat.css">
</head>
<body>

    <div class="container">
        <header class="header">
            <a href="?route=">
                <img src="app/css/img/logo.png" alt="" class="logo">
            </a>
            <!-- Lado Esquerdo -->
            <h2>Central de mensagens</h2>
            
            <!-- Lado Direito (Agrupado em uma div) -->
            <div class="header-acoes">
                <a href="?route=notificacoes" class="sino-link" title="Notificações">
                    <img src="app/css/img_dashboard/icone-sino.png" alt="Sino" class="icone-sino">
                </a>

                <?php if (isset($_SESSION["id"])): ?>
                    <span class="user-name"><?= htmlspecialchars($_SESSION['nome']) ?></span>
                <?php endif; ?>

                <img class="verificado" src="app/css/img_dashboard/icone-check.png" alt="Verificado">
            </div>
        </header>
    </div>

    <div class="painel-chat-container">
        
        <!-- BARRA LATERAL -->
        <div class="sidebar-chats">
            <div class="sidebar-header">Suas Conversas</div>
            
            <?php if (!empty($meusChats)): ?>
                <?php foreach ($meusChats as $chat): ?>
                    <?php $isAtivo = (isset($solicitacaoAtiva) && $solicitacaoAtiva == $chat['id_solicitacao']); ?>
                    
                    <a href="?route=chat&id_solicitacao=<?= $chat['id_solicitacao'] ?>" class="item-chat <?= $isAtivo ? 'ativo' : '' ?>">
                        <div class="info-chat-lista">
<img src="<?= !empty($chat['foto_contato']) ? htmlspecialchars($chat['foto_contato']) . '?v=' . time() : 'app/css/img/default-user.png' ?>" class="foto-chat" alt="Foto de <?= htmlspecialchars($chat['nome_contato']) ?>">                            <div class="texto-chat">
                                <!-- NOVO: Agrupamento do Nome e da Role -->
                                <div class="nome-e-role">
                                    <strong><?= htmlspecialchars($chat['nome_contato']) ?></strong>
                                    
                                    <!-- Exibe a role se ela existir no banco -->
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
                <div style="padding: 20px; text-align: center; color: #888; font-size: 0.9rem;">
                    Nenhuma conversa encontrada.
                </div>
            <?php endif; ?>
        </div>

        <!-- ÁREA PRINCIPAL: CHAT ATIVO -->
        <?php if (isset($solicitacaoAtiva) && $solicitacaoAtiva != null): ?>
            <div class="area-chat-ativo">
                <div class="header-chat-ativo">Conversa da Solicitação #<?= htmlspecialchars($solicitacaoAtiva) ?></div>
                
                <div id="chat-box" class="chat-box"></div>

                <div id="area-preview" class="area-preview">
                    <div class="preview-container">
                        <img id="img-preview" src="" alt="Pré-visualização">
                        <button type="button" id="btn-remover-preview" class="btn-remover-preview" title="Remover imagem">×</button>
                    </div>
                </div>

                <form id="form-msg" class="form-chat">
                    <label for="imagem-mensagem" class="btn-imagem" title="Enviar imagem">+</label>
                    <input type="file" id="imagem-mensagem" accept="image/*" style="display: none;">

                    <input type="text" id="texto-mensagem" placeholder="Digite sua mensagem..." autocomplete="off">
                    <button type="submit" class="btn-enviar">Enviar</button>
                </form>
            </div>
            
            <script>
                const solicitacaoId = <?= json_encode($solicitacaoAtiva) ?>;
                const meuId = <?= json_encode($_SESSION['id']) ?>;

                // Variáveis do Preview
                const inputImagem = document.getElementById('imagem-mensagem');
                const areaPreview = document.getElementById('area-preview');
                const imgPreview = document.getElementById('img-preview');
                const btnRemoverPreview = document.getElementById('btn-remover-preview');

                inputImagem.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        imgPreview.src = URL.createObjectURL(file);
                        areaPreview.style.display = 'block';
                    }
                });

                btnRemoverPreview.addEventListener('click', function() {
                    inputImagem.value = ''; 
                    areaPreview.style.display = 'none'; 
                    imgPreview.src = '';
                });

                async function carregarMensagens() {
                    const response = await fetch(`?route=carregar-mensagens-json&id_solicitacao=${solicitacaoId}`);
                    const mensagens = await response.json();
                    
                    const chatBox = document.getElementById('chat-box');
                    const estavaNoFinal = chatBox.scrollHeight - chatBox.scrollTop === chatBox.clientHeight;
                    
                    chatBox.innerHTML = '';

                    if (mensagens.length === 0) {
                        chatBox.innerHTML = `<p style="text-align:center; color:#888; margin-top:20px;">Envie a primeira mensagem!</p>`;
                        return;
                    }

                    mensagens.forEach(msg => {
                        const div = document.createElement('div');
                        const ehMinha = msg.FK_id_TB_remetente == meuId;
                        div.className = `msg ${ehMinha ? 'minha' : 'outra'}`;
                        
                        const dataFormatada = new Date(msg.data_envio_TB_mensagem).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        let conteudoHTML = '';
                        
                        if (msg.imagem_TB_mensagem && msg.imagem_TB_mensagem.trim() !== "") {
                            conteudoHTML += `<img src="${msg.imagem_TB_mensagem}" style="max-width: 100%; max-height: 250px; border-radius: 8px; margin-bottom: 8px; display: block; object-fit: contain;" alt="Imagem enviada">`;
                        }
                        
                        if (msg.mensagem_TB_mensagem && msg.mensagem_TB_mensagem.trim() !== "") {
                            conteudoHTML += `<span>${msg.mensagem_TB_mensagem}</span>`;
                        }
                        
// Obtém a foto do remetente e adiciona o parâmetro de versão para evitar cache antigo
const timestamp = new Date().getTime();
const fotoRemetente = (msg.foto_TB_usuario && msg.foto_TB_usuario.trim() !== "") 
    ? `${msg.foto_TB_usuario}?v=${timestamp}` 
    : 'app/css/img/default-user.png';


// Insere a foto junto do conteúdo e do horário da mensagem
div.innerHTML = `
    <div class="msg-conteudo">
        ${conteudoHTML}
        <div class="msg-info">${dataFormatada}</div>
    </div>
`;

chatBox.appendChild(div);
                    });

                    if (estavaNoFinal || mensagens.length > 0) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                }

                document.getElementById('form-msg').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const inputTexto = document.getElementById('texto-mensagem');
                    const msgText = inputTexto.value.trim();
                    const imagemFile = inputImagem.files[0];

                    if (!msgText && !imagemFile) return;

                    const formData = new FormData();
                    formData.append('id_solicitacao', solicitacaoId);
                    
                    if (msgText) formData.append('mensagem', msgText);
                    if (imagemFile) formData.append('imagem', imagemFile);

                    inputTexto.value = '';
                    inputImagem.value = ''; 
                    areaPreview.style.display = 'none'; 
                    imgPreview.src = '';
                    
                    await fetch('?route=enviar-mensagem', { method: 'POST', body: formData });
                    carregarMensagens();
                });

                setInterval(carregarMensagens, 2000);
                carregarMensagens();
            </script>

        <?php else: ?>
            <div class="area-chat-ativo">
                <div class="chat-placeholder">
                    <div style="text-align: center;">
                        <h3 style="color: #555;">Bem-vindo às suas Mensagens</h3>
                        <p>Selecione um chat na barra lateral para começar a conversar.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>