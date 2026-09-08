<?php
require_once __DIR__ . "/../Models/UserModel.php";

require_once __DIR__ . "/CriptoController.php";

class ServiceController {

    // 1. Exibe o formulário Wizard
    public function exibirWizard() {
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
    
    // 3. Chamado quando a solicitação é aceita
    public function confirmarEAceitarSolicitacao() {
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

    public function listaPrestador(){

        require_once "app/views/servicos/lista-prestador.php";
    }

    public function filtroPrestadores(){
    if (!isset($_SESSION["id"])) {
        
    }        
    }
}