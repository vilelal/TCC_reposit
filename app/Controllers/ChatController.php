<?php

class ChatController
{

    public function exibirChat()
    {
        if (!isset($_SESSION["id"])) {
            header("Location: ?route=login-form");
            exit;
        }

        $usuarioId = $_SESSION["id"];
        $solicitacaoAtiva = $_GET["id_solicitacao"] ?? null;

        // Se ele clicou em um chat específico, marca as mensagens como lidas
        if ($solicitacaoAtiva) {
            ChatModel::marcarMensagensComoLidas($solicitacaoAtiva, $usuarioId);
        }

        // Busca todos os chats para preencher a barra lateral (esquerda)
        $meusChats = ChatModel::buscarMeusChats($usuarioId);

        // Carrega a nova View do painel completo
        require_once "app/Views/servicos/painel-chat.php";
    }

   public function enviarMensagem() {
    if (!isset($_SESSION["id"])) return;

    $usuarioId = $_SESSION["id"];
    $solicitacaoId = $_POST["id_solicitacao"] ?? null;
    $mensagem = $_POST["mensagem"] ?? ""; 
    $caminhoImagem = null;

    if (!$solicitacaoId) return;

    // Verifica se uma imagem foi enviada
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $diretorioDestino = 'app/uploads/chats/';
        
        // Cria a pasta se ela não existir
        if (!is_dir($diretorioDestino)) {
            mkdir($diretorioDestino, 0777, true);
        }
        
        // Cria um nome único para a imagem para não sobrepor outras
        $nomeExtensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . '_' . time() . '.' . $nomeExtensao;
        $caminhoCompleto = $diretorioDestino . $nomeArquivo;
        
        // Move a imagem da pasta temporária do servidor para a nossa pasta
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
            $caminhoImagem = $caminhoCompleto;
        }
    }

    // Só salva se tiver texto OU imagem
    if (!empty(trim($mensagem)) || $caminhoImagem !== null) {
        ChatModel::salvarMensagem($solicitacaoId, $usuarioId, $mensagem, $caminhoImagem);
    }
    
    echo json_encode(["status" => "sucesso"]);
}

    public function carregarMensagensJSON()
    {
        $solicitacaoId = $_GET["id_solicitacao"] ?? null;
        if ($solicitacaoId) {
            $mensagens = ChatModel::buscarMensagens($solicitacaoId);
            header('Content-Type: application/json');
            echo json_encode($mensagens);
        }
        exit;
    }
    public function enviarOrcamento()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idSolicitacao = $_POST['id_solicitacao'] ?? null;
        $novoValor = $_POST['novo_valor'] ?? null;

        if ($idSolicitacao && $novoValor !== null) {
            // 1. Atualiza o valor e o status da solicitação no banco
            $sucesso = ServiceModel::atualizarOrcamentoSolicitacao($idSolicitacao, $novoValor);

            if ($sucesso) {
                // 2. Insere uma mensagem automática no chat informando o novo orçamento
                $mensagemTexto = "O prestador enviou uma nova proposta de orçamento: R$ " . number_format((float)$novoValor, 2, ',', '.');
                ChatModel::enviarMensagemAutomatica($idSolicitacao, $_SESSION['id'], $mensagemTexto);

                // 3. Envia uma notificação para o cliente aceitar/recusar
                // (Busca o ID do usuário cliente através da solicitação)
                $idClienteUsuario = ServiceModel::getIdUsuarioClientePorSolicitacao($idSolicitacao);
                if ($idClienteUsuario) {
                    NotificacaoModel::enviarNotificacao([
                        'titulo' => 'Novo orçamento recebido',
                        'mensagem' => 'O prestador enviou um orçamento ajustado para o seu serviço. Acesse o chat para revisar e aceitar.',
                        'user_id' => $idClienteUsuario
                    ]);
                }

                // Redireciona de volta para o chat com sucesso
                header("Location: ?route=chat&id=" . $idSolicitacao . "&status=orcamento_enviado");
                exit;
            }
        }
    }
    header("Location: ?route=servicos&erro=falha_orcamento");
    exit;
}




}
