<?php
require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/CriptoController.php";


class ServiceController {
    
public function salvarPedido() {
    // Garante que o cliente está logado e o ID existe na sessão
    if (!isset($_SESSION["id"])) {
        header("Location: ?route=login-form");
        exit;
    }

    $data = $_POST;
    $data["FK_id_TB_cliente"] = $_SESSION["id"]; 

    $sucesso = ServiceModel::criarSolicitacao($data);

    if (!$sucesso) {
        echo "Erro ao realizar o pedido. Tente novamente.";
        return;
    }

    header("Location: ?route=meus-pedidos");
    exit;
}
public function filtrarPrestadores() {
    // Verifica se o cliente está logado para saber a cidade dele
    if (!isset($_SESSION["id_cliente"])) {
        header("Location: ?route=login-form");
        exit;
    }

    // Pega a categoria selecionada no select do formulário HTML
    $categoriaId = $_GET["FK_id_TB_categoria"] ?? null;
    $clienteId = $_SESSION["id_cliente"];

    // Busca as categorias para preencher o <select>
    $string_categorias = ServiceModel::listarCategorias(); 

    $prestadoresDisponiveis = [];
    if ($categoriaId) {
        // Busca os prestadores da mesma categoria e cidade do cliente (ou região)
        $prestadoresDisponiveis = ServiceModel::buscarPrestadoresPorCategoriaEEspacos($categoriaId, $clienteId);
    }

    // Carrega a view passando os dados
    require_once "app/Views/servicos/escolher-prestador.php";
}

}