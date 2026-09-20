<?php

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

    public function editServicos() {
        $data = $_POST;
        PrestadorModel::editServicos($data);

        header("Location: ?route=perfil");
    }

    public function listaServicos() {
        $servicos = solicitacaoModel::getSolicitacao($_SESSION["id_prestador"]);
        require_once "app/Views/prestador/servicos.php";
    }

    public function concluirServico() {
        $solicitacaoId = $_POST["servico_id"];
        $pin = $_POST["pin"];

        if (solicitacaoModel::concluirServico($solicitacaoId, $pin)) {
            header("Location: ?route=dashboard");
            return;
        }

        header("Location: ?route=lista-servicos");
    }
}
