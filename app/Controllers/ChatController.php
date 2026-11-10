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
    // PRESTADOR envia o orçamento (formulário/calculadora do chat)
    public function enviarOrcamento()
    {
        if (!isset($_SESSION["id"])) {
            header("Location: ?route=login-form");
            exit;
        }

        $idSolicitacao = (int) ($_POST["id_solicitacao"] ?? 0);
        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $idSolicitacao <= 0) {
            header("Location: ?route=chat");
            exit;
        }
        $voltar = "?route=chat&id_solicitacao=" . $idSolicitacao;

        // só o prestador DESTA solicitação pode enviar orçamento
        $solicitacao = OrcamentoModel::getParticipantes($idSolicitacao);
        if (!$solicitacao || $solicitacao["id_usuario_prestador"] != $_SESSION["id"]) {
            header("Location: ?route=chat");
            exit;
        }
        if (in_array($solicitacao["status_solicitacao"], ["concluido", "cancelado"])) {
            header("Location: $voltar&msg=orcamento_encerrada");
            exit;
        }

        $valor = filter_var($_POST["novo_valor"] ?? null, FILTER_VALIDATE_FLOAT);
        if ($valor === false || $valor <= 0 || $valor > 99999999) {
            header("Location: $voltar&msg=orcamento_invalido");
            exit;
        }

        $num = function ($campo) {
            $v = filter_var($_POST[$campo] ?? 0, FILTER_VALIDATE_FLOAT);
            return ($v === false || $v < 0) ? 0.0 : (float) $v;
        };
        $dias = (int) ($_POST["dias"] ?? 0);

        $dados = [
            "valor"      => (float) $valor,
            "dias"       => $dias > 0 ? $dias : null,
            "diaria"     => $num("diaria") > 0 ? $num("diaria") : null,
            "materiais"  => $num("materiais"),
            "transporte" => $num("transporte"),
            "descricao"  => trim($_POST["descricao"] ?? "") ?: null,
        ];

        $idOrcamento = OrcamentoModel::criar($idSolicitacao, $_SESSION["id"], $dados);
        if (!$idOrcamento) {
            header("Location: $voltar&msg=orcamento_falha");
            exit;
        }

        NotificacaoModel::enviarNotificacao([
            "titulo"   => "Novo orçamento recebido",
            "mensagem" => "O prestador enviou um orçamento de R$ " . number_format($dados["valor"], 2, ",", ".") . ". Acesse o chat para aceitar ou recusar.",
            "user_id"  => $solicitacao["id_usuario_cliente"]
        ]);

        header("Location: $voltar&msg=orcamento_enviado");
        exit;
    }

    // CLIENTE aceita ou recusa o orçamento
    public function responderOrcamento()
    {
        if (!isset($_SESSION["id"])) {
            header("Location: ?route=login-form");
            exit;
        }

        $idOrcamento = (int) ($_POST["id_orcamento"] ?? 0);
        $acao = $_POST["acao"] ?? "";
        $orcamento = $idOrcamento > 0 ? OrcamentoModel::getOrcamento($idOrcamento) : null;

        // só o cliente DONO da solicitação pode responder
        if (
            $_SERVER["REQUEST_METHOD"] !== "POST" || !$orcamento
            || $orcamento["id_usuario_cliente"] != $_SESSION["id"]
            || !in_array($acao, ["aceitar", "recusar"], true)
        ) {
            header("Location: ?route=chat");
            exit;
        }

        $voltar = "?route=chat&id_solicitacao=" . (int) $orcamento["FK_id_TB_SolicitacaoServico"];
        $aceitar = ($acao === "aceitar");

        if (!OrcamentoModel::responder($orcamento, $_SESSION["id"], $aceitar)) {
            header("Location: $voltar&msg=orcamento_falha");
            exit;
        }

        NotificacaoModel::enviarNotificacao([
            "titulo"   => $aceitar ? "Orçamento aceito" : "Orçamento recusado",
            "mensagem" => "O cliente " . ($aceitar ? "aceitou" : "recusou")
                . " o seu orçamento de R$ " . number_format($orcamento["valor_TB_orcamento"], 2, ",", ".") . ".",
            "user_id"  => $orcamento["id_usuario_prestador"]
        ]);

        header("Location: $voltar&msg=" . ($aceitar ? "orcamento_aceito" : "orcamento_recusado"));
        exit;
    }
}
