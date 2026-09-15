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
        $servicos = ServiceModel::getSolicitacao($_SESSION["id_prestador"]);
        require_once "app/Views/prestador/servicos.php";
    }

}
