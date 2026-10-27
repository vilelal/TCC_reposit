<?php

require_once __DIR__ . "/CriptoController.php";

class PrestadorController
{
    public function dashboard()
    {
        $user = UserModel::getPrestadorById($_SESSION["id"]);
        $avaliacoes = AvaliacaoModel::getMediaAvaliacoes($_SESSION["id"]);
        require_once "app/Views/prestador/dashboard.php";
    }

    public function relatorio()
    {
        $idprestador = $_SESSION["id_prestador"];
        $dadosrelatorio = PrestadorModel::servicosConcluidos($idprestador);

        $trampo = $dadosrelatorio["total_concluidos"];
        $money = $dadosrelatorio ["faturamento_total"];                 // olhga aquiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
        require_once "app/Views/prestador/relatorio.php";
    }

    // Tela "Meus serviços" (acessada pelo perfil): serviços que o prestador oferece
    public function meusServicos()
    {
        $categorias = CategoriaModel::getCategorias();
        $servicos = ServiceModel::getServices();
        $servicosSelecionados = ServiceModel::getServices($_SESSION["id_prestador"]);
        require_once "app/Views/prestador/meus-servicos.php";
    }

    public function editServicos()
    {
        $data = $_POST;
        PrestadorModel::editServicos($data);

        header("Location: ?route=perfil");
    }

    public function listaServicos()
    {
        $servicos = solicitacaoModel::getSolicitacao($_SESSION["id_prestador"]);
        require_once "app/Views/prestador/servicos.php";
    }

    public function concluirServico()
    {
        $solicitacaoId = $_POST["servico_id"];
        $pin = $_POST["pin"];
        $servico = $_POST["servico"];
        $prestador = $_POST["prestador"];
        $userCliente = $_POST["id_user_cliente"];
        $userPrestador = $_POST["id_user_prestador"];

        if (solicitacaoModel::concluirServico($solicitacaoId, $pin)) {
            $notificacao = [
                "titulo" => "Serviço concluído",
                "mensagem" => "O serviço $servico foi concluído, avalie o prestador $prestador #$userPrestador",
                "user_id" => $userCliente
            ];

            NotificacaoModel::enviarNotificacao($notificacao);
            require_once "app/Views/servicos/avaliar.php";
            return;
        }

        header("Location: ?route=lista-servicos");
    }

    public function listarPrestadores()
    {
        $cliente = $_SESSION["id_cliente"];
        $servico = $_POST["FK_id_TB_servico"];
        $data = $_POST["data_agendamento"];

        $prestadores = UserModel::buscarPrestadoresProximos($cliente, $servico);
        require_once "app/Views/servicos/lista-prestadores.php";
    }

    public function solicitarServico()
    {
        $cliente = $_SESSION["id_cliente"];
        $prestador = $_POST["prestador"];
        $servico = $_POST["servico"];
        $valor = $_POST["valor"];
        $data = str_replace("T", " ", $_POST["data"]);
        $user = UserModel::getClientById($_SESSION["id"]);
        $tel = CriptoController::decrypt($user["tel_TB_cliente"]);
        $pin = substr($tel, -4);

        $solicitacao = solicitacaoModel::criarSolicitacaoFinal($cliente, $prestador, $servico, $data, $valor, $pin);
        if (!$solicitacao) {
            header("Location: ?route=buscar-proximo");
            return;
        }

        header("Location: ?route=lista-servicos-cliente");
        return;
    }



    
}
