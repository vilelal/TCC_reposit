<?php

class AdminController
{
    // Exibe o painel administrativo com usuários, categorias e denúncias
    public function exibirPainel()
    {
        if (!isset($_SESSION['tipo']) || strtolower($_SESSION['tipo']) !== 'admin') {
            header('Location: ?route=login-form');
            exit;
        }

        $listaUsuarios   = AdminModel::listarTodosUsuarios();
        $listaCategorias  = AdminModel::listarTodasCategorias();
        $listaDenuncias  = AdminModel::listarTodasDenuncias(); // Carrega todas as denúncias

        include __DIR__ . '/../Views/ADM/painel-admin.php';
    }

    // Cria uma nova categoria no sistema
    public function criarCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeCategoria = trim($_POST['nome_categoria'] ?? '');

            if (!empty($nomeCategoria)) {
                AdminModel::criarCategoria($nomeCategoria);
            }
        }

        header('Location: ?route=painel-admin');
        exit;
    }

    // Cria um novo serviço associado a uma categoria
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

    // Bani/Exclui um usuário diretamente da lista geral de usuários
    public function banirUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_POST['id_usuario'] ?? null;

            // Impede que o admin bana a si mesmo
            if (!empty($idUsuario) && $idUsuario != $_SESSION['id']) {
                AdminModel::banirUsuario($idUsuario);
            }
        }

        header('Location: ?route=painel-admin');
        exit;
    }

    // Atualiza o status de uma denúncia (ex: 'em_analise', 'resolvido', 'rejeitado')
    public function atualizarStatusDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idDenuncia = $_POST['id_denuncia'] ?? null;
            $novoStatus = $_POST['status'] ?? null;

            if (!empty($idDenuncia) && !empty($novoStatus)) {
                AdminModel::atualizarStatusDenuncia($idDenuncia, $novoStatus);
            }
        }

        header('Location: ?route=painel-admin');
        exit;
    }

    // Ação acionada a partir da análise de uma denúncia:
    // Bani o usuário (denunciado ou denunciante em caso de falsa denúncia) e marca a denúncia como 'resolvido'
    public function processarBanimentoPorDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuarioAlvo = $_POST['id_usuario_alvo'] ?? null; // Pode ser o id do denunciado ou do denunciante
            $idDenuncia    = $_POST['id_denuncia'] ?? null;

            // Garante que o admin não possa banir a si mesmo
            if (!empty($idUsuarioAlvo) && $idUsuarioAlvo != $_SESSION['id']) {
                AdminModel::banirUsuario($idUsuarioAlvo);

                // Marca automaticamente a denúncia como 'resolvido' após aplicar o banimento
                if (!empty($idDenuncia)) {
                    AdminModel::atualizarStatusDenuncia($idDenuncia, 'resolvido');
                }
            }
        }

        header('Location: ?route=painel-admin');
        exit;
    }
}