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

    public function perfil() {
        $user = PrestadorModel::getPrestadorById($_SESSION["id"]);
        $user["tel_TB_prestador"] = CriptoController::decrypt($user["tel_TB_prestador"]);
        $user["cpf_cnpj_TB_prestador"] = CriptoController::decrypt($user["cpf_cnpj_TB_prestador"]);
        require_once "app/Views/prestador/perfil.php";
    }
}
