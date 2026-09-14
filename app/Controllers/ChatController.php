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

    public function enviarMensagem()
    {
        if (!isset($_SESSION["id"])) {
            http_response_code(403);
            echo json_encode(["status" => "error", "mensagem" => "Não autorizado"]);
            exit;
        }

        $solicitacaoId = $_POST["id_solicitacao"] ?? null;
        $mensagem = trim($_POST["mensagem"] ?? "");
        $remetenteId = $_SESSION["id"];

        if ($solicitacaoId && !empty($mensagem)) {
            ChatModel::salvarMensagem($solicitacaoId, $remetenteId, $mensagem);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "mensagem" => "Dados inválidos"]);
        }
        exit;
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
}
