<?php

class NotificacaoController
{
    public function notificacoes()
    {
        $notificacoes = NotificacaoModel::getNotificacoes($_SESSION["id"]);
        require_once "app/Views/user/notificacoes.php";
        // apos carregar view seta as notificacoes como lidas
        NotificacaoModel::notificacaoLida($_SESSION["id"]);
    }
}