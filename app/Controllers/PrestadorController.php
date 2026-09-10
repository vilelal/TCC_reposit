<?php

class PrestadorController
{
    public function dashboard()
    {
        require_once "app/Views/prestador/dashboard.php";
    }

    public function editPerfil() {}

    public function listaServicos() {
        require_once "app/Views/prestador/servicos.php";
    }

}
