<?php

class AdminController
{

    public function exibirPainel()
    {
        if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Admin') {
            header('Location: ?route=home');
            exit;
        }

        $listaUsuarios = AdminModel::listarTodosUsuarios();

        // NOVO: Busca também as categorias para preencher o select
        $listaCategorias = AdminModel::listarTodasCategorias();

        include __DIR__ . '/../Views/painel-admin.php';
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
