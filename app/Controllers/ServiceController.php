<?php
require_once __DIR__ . "/../Models/UserModel.php";

require_once __DIR__ . "/CriptoController.php";

class ServiceController
{

    // 1. Exibe o formulário Wizard
    public function exibirWizard()
    {
        if (!isset($_SESSION["id"])) {
            header("Location: ?route=login-form");
            exit;
        }

        // Busca as categorias e serviços cadastrados
        $categorias = solicitacaoModel::buscarTodasCategorias();
        $todosServicos = solicitacaoModel::buscarTodosServicos();

        require_once "app/Views/servicos/wizard-servico.php";
    }

    // 2. Processa o Wizard e busca prestadores próximos
    public function buscarPrestadoresProximos()
    {
        if (!isset($_SESSION["id"])) {
            header("Location: ?route=login-form");
            exit;
        }

        $clienteId = $_SESSION["id"];
        $dados = $_POST;

        // Guarda os detalhes temporariamente na sessão
        $_SESSION['proposta_temporaria'] = [
            'servico_id'          => $dados['FK_id_TB_servico'],
            'urgencia'            => $dados['urgencia'],
            'data_agendamento'    => $dados['data_agendamento'],
            'solicitar_orcamento' => isset($dados['solicitar_orcamento']) ? 1 : 0,
            'descricao'           => $dados['descricao']
        ];

        // Alterado de ServiceModel para solicitacaoModel
        $prestadoresEncontrados = solicitacaoModel::buscarPrestadoresProximos($clienteId, $dados['FK_id_TB_servico']);

        require_once "app/Views/servicos/lista-prestadores-match.php";
    }

    // 3. Chamado quando a solicitação é aceita
    public function confirmarEAceitarSolicitacao()
    {
        if (!isset($_SESSION["id"]) || !isset($_SESSION['proposta_temporaria'])) {
            header("Location: ?route=home");
            exit;
        }

        $clienteId = $_SESSION["id"];
        $proposta = $_SESSION['proposta_temporaria'];
        $prestadorServicoId = $_POST['FK_id_TB_prestadorServico'];
        $valorCombinado = $_POST['valor_total'];

        // Alterado de ServiceModel para solicitacaoModel
        $criado = solicitacaoModel::criarSolicitacaoFinal(
            $clienteId,
            $prestadorServicoId,
            $proposta['data_agendamento'],
            $valorCombinado
        );

        if ($criado) {
            unset($_SESSION['proposta_temporaria']);
            header("Location: ?route=meus-pedidos");
        } else {
            echo "Erro ao registrar solicitação aceita.";
        }
    }

    public function statusSolicitacao()
    {
        $id = $_POST["servico_id"];
        $status = $_POST["status"];
        $servico = $_POST["servico"];
        $cliente = $_POST["cliente"];
        $prestador = $_POST["prestador"];
        $user_prestador = null;
        $user_cliente = null;

        // prestador logado pega o user cliente pelo id da solicitacao e pega prestador pela session
        if (isset($cliente)) {
            $user_cliente = UserModel::getUserByIdCliente($cliente);
            $user_prestador = UserModel::getUserByIdPrestador(($_SESSION["id_prestador"]));
        }

        // cliente logado pega o user prestador pelo id da solicitacao e pega cliente pela session
        else {
            $user_prestador = UserModel::getUserByIdPrestador($prestador);
            $user_cliente = UserModel::getUserByIdCliente(($_SESSION["id_cliente"]));
        }

        solicitacaoModel::statusSolicitacao($id, $status);

        // prestador fazendo requisição
        if ($cliente) {
            if ($status == "cancelado") {
                NotificacaoModel::enviarNotificacao([
                    "titulo" => " Serviço {$servico} agendado",
                    "mensagem" => "O serviço {$servico} foi cancelado pelo prestador!",
                    "user_id" => $user_cliente["PK_id_TB_usuario"]
                ]);
                header("Location: ?route=dashboard");
                return;
            }
        }

        // cliente fazendo requisição
        else {
            if ($status == "cancelado") {
                NotificacaoModel::enviarNotificacao([
                    "titulo" => " Serviço {$servico} agendado",
                    "mensagem" => "O serviço {$servico} foi cancelado pelo cliente!",
                    "user_id" => $user_prestador["PK_id_TB_usuario"]
                ]);
                header("Location: ?route=home");
                return;
            }
        }

        // solicitação aceita notificação para ambos

        NotificacaoModel::enviarNotificacao([
            "titulo" => " Solicitação do serviço {$servico}",
            "mensagem" => "O serviço {$servico} foi aceitado pelo prestador! Confira as informações na aba de
            Minhas Solicitações no menu",
            "user_id" => $user_cliente["PK_id_TB_usuario"]
        ]);

        NotificacaoModel::enviarNotificacao([
            "titulo" => " Solicitação do serviço {$servico}",
            "mensagem" => "O serviço {$servico} foi agendado! Confira as informações na aba de
            serviços no painel",
            "user_id" => $user_prestador["PK_id_TB_usuario"]
        ]);

        if ($prestador) {
            header("Location: ?route=home");
            return;
        }

        header("Location: ?route=dashboard");
        return;
    }
}
