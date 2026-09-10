<?php

class PrestadorController
{
    public function dashboard()
    {
        require_once "app/Views/prestador/dashboard.php";
    }

    public function editServicos() {
        $data = $_POST;
        PrestadorModel::editServicos($data);

        header("Location: ?route=perfil");
    }

    public function listaServicos() {
        $categorias = CategoriaModel::getCategorias();
        $servicos = ServiceModel::getServices();
        $servicosSelecionados = ServiceModel::getServices($_SESSION["id_prestador"]);
        require_once "app/Views/prestador/meus-servicos.php";
    }

}
