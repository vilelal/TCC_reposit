<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Central de Mensagens</title>
    <link rel="stylesheet" href="app/css/styleCad.css">
    <style>
        /* Layout Principal Dividido */
        .painel-chat-container {
            display: flex;
            height: 80vh;
            max-height: 800px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        /* LADO ESQUERDO: Lista de Chats */
        .sidebar-chats {
            width: 35%;
            border-right: 1px solid #ddd;
            background: #f8f9fa;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .item-chat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
        }
        .item-chat:hover { background: #e9ecef; }
        .item-chat.ativo { background: #e2e6ea; border-left: 4px solid #007bff; }
        
        .info-chat-lista { display: flex; align-items: center; gap: 10px; width: 100%; }
        .foto-chat { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .texto-chat { flex: 1; overflow: hidden; }
        .texto-chat strong { display: block; font-size: 0.95rem; }
        .texto-chat p { margin: 0; font-size: 0.8rem; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        /* Indicador de Mensagem Não Lida */
        .badge-nao-lida {
            background-color: #28a745;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 12px;
            min-width: 20px;
            text-align: center;
        }

        /* LADO DIREITO: Área do Chat Ativo */
        .area-chat-ativo {
            width: 65%;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .header-chat-ativo {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background: #f1f1f1;
            font-weight: bold;
        }
        .chat-box {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #fafafa;
        }
        .chat-placeholder {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            background: #fdfdfd;
        }
        .msg { max-width: 70%; padding: 10px 15px; border-radius: 12px; font-size: 0.95rem; }
        .msg.minha { align-self: flex-end; background-color: #007bff; color: white; border-bottom-right-radius: 2px; }
        .msg.outra { align-self: flex-start; background-color: #e9ecef; color: #333; border-bottom-left-radius: 2px; }
        .msg-info { font-size: 0.7rem; opacity: 0.8; margin-top: 5px; text-align: right; }
        
        .form-chat {
            display: flex;
            padding: 15px;
            background: #fff;
            border-top: 1px solid #ddd;
            gap: 10px;
        }
        .form-chat input { flex: 1; padding: 12px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-enviar { background: #007bff; color: white; border: none; padding: 0 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Central de Mensagens</h2>

    <div class="painel-chat-container">
        
        <!-- BARRA LATERAL: LISTA DE CHATS -->
        <div class="sidebar-chats">
            <div class="sidebar-header">Suas Conversas</div>
            
            <?php if (!empty($meusChats)): ?>
                <?php foreach ($meusChats as $chat): ?>
                    <?php $isAtivo = (isset($solicitacaoAtiva) && $solicitacaoAtiva == $chat['id_solicitacao']); ?>
                    
                    <a href="?route=chat&id_solicitacao=<?= $chat['id_solicitacao'] ?>" class="item-chat <?= $isAtivo ? 'ativo' : '' ?>">
                        <div class="info-chat-lista">
                            <img src="<?= !empty($chat['foto_contato']) ? htmlspecialchars($chat['foto_contato']) : 'app/images/default-avatar.png' ?>" class="foto-chat">
                            <div class="texto-chat">
                                <strong><?= htmlspecialchars($chat['nome_contato']) ?></strong>
                                <p><?= htmlspecialchars($chat['ultima_mensagem'] ?? 'Sem mensagens...') ?></p>
                            </div>
                            
                            <!-- Exibe a bolinha verde se houver mensagem não lida -->
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

                <form id="form-msg" class="form-chat">
                    <input type="text" id="texto-mensagem" placeholder="Digite sua mensagem..." required autocomplete="off">
                    <button type="submit" class="btn-enviar">Enviar</button>
                </form>
            </div>
            
            <script>
                const solicitacaoId = <?= json_encode($solicitacaoAtiva) ?>;
                const meuId = <?= json_encode($_SESSION['id']) ?>;

                async function carregarMensagens() {
                    const response = await fetch(`?route=carregar-mensagens-json&id_solicitacao=${solicitacaoId}`);
                    const mensagens = await response.json();
                    
                    const chatBox = document.getElementById('chat-box');
                    // Guarda se a barra de rolagem estava no final antes de atualizar
                    const estavaNoFinal = chatBox.scrollHeight - chatBox.scrollTop === chatBox.clientHeight;
                    
                    chatBox.innerHTML = '';

                    if (mensagens.length === 0) {
                        chatBox.innerHTML = `<p style="text-align:center; color:#888; margin-top:20px;">Envie a primeira mensagem!</p>`;
                        return;
                    }

                    mensagens.forEach(msg => {
                        const div = document.createElement('div');
                        const ehMinha = msg.FK_id_remetente == meuId;
                        div.className = `msg ${ehMinha ? 'minha' : 'outra'}`;
                        
                        const dataFormatada = new Date(msg.data_envio).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        div.innerHTML = `${msg.mensagem}<div class="msg-info">${dataFormatada}</div>`;
                        chatBox.appendChild(div);
                    });

                    // Desce a barra de rolagem automaticamente
                    if (estavaNoFinal || mensagens.length > 0) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                }

                document.getElementById('form-msg').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const input = document.getElementById('texto-mensagem');
                    const msgText = input.value.trim();
                    if (!msgText) return;

                    const formData = new FormData();
                    formData.append('id_solicitacao', solicitacaoId);
                    formData.append('mensagem', msgText);

                    input.value = '';
                    await fetch('?route=enviar-mensagem', { method: 'POST', body: formData });
                    carregarMensagens();
                });

                setInterval(carregarMensagens, 2000);
                carregarMensagens();
            </script>

        <?php else: ?>
            <!-- TELA DE ESPERA CASO NENHUM CHAT ESTEJA SELECIONADO -->
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