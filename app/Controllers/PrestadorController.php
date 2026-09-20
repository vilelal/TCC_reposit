<?php

class PrestadorController
{
    public function dashboard()
    {
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
}
