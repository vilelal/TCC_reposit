<?php

class AdminController
{

public function exibirPainel()
{

    // Valida em minúsculo para evitar falhas por causa de 'Admin' vs 'admin'
    if (!isset($_SESSION['tipo']) || strtolower($_SESSION['tipo']) !== 'admin') {
        header('Location: ?route=home');
        exit;
    }


    $listaUsuarios = AdminModel::listarTodosUsuarios();
    $listaCategorias = AdminModel::listarTodasCategorias();

    include __DIR__ . '/../Views/ADM/painel-admin.php';
}


    // Cria o novo serviço
    public function criarServico()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeServico = trim($_POST['nome_servico'] ?? '');
            $idCategoria = $_POST['id_categoria'] ?? null;

            if (!empty($nomeServico) && !empty($idCategoria)) {
                AdminModel::criarServicoComCategoria($nomeServico, $idCategoria);
            }
        }

        header('Location: ?route=painel-admin');
        exit;
    }

    // Bani/Exclui o usuário
    public function banirUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_POST['id_usuario'] ?? null;

            // Impede que o admin bani a si mesmo por segurança
            if (!empty($idUsuario) && $idUsuario != $_SESSION['id']) {
                AdminModel::banirUsuario($idUsuario);
            }
        }

        // Redireciona de volta para o painel
        header('Location: ?route=painel-admin');
        exit;
    }
}
