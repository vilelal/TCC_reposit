<?php

class AdminController
{
    // Garante que a sessão está ativa
    private function verificarAutenticacaoAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ajuste os nomes das chaves de sessão caso você utilize outros nomes no Login
        $tipoUsuario = $_SESSION['tipo'] ?? $_SESSION['tipo_TB_usuario'] ?? '';

        if (strtolower($tipoUsuario) !== 'admin') {
            header('Location: index.php?route=login-form');
            exit;
        }
    }

    // Exibe o painel administrativo
    public function exibirPainel()
    {
        $this->verificarAutenticacaoAdmin();

        $listaUsuarios  = AdminModel::listarTodosUsuarios();
        $listaCategorias = AdminModel::listarTodasCategorias();
        $listaDenuncias = AdminModel::listarTodasDenuncias();

        include __DIR__ . '/../Views/ADM/painel-admin.php';
    }

    // Cria uma nova categoria
    public function criarCategoria()
    {
        $this->verificarAutenticacaoAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeCategoria = trim($_POST['nome_categoria'] ?? '');

            if (!empty($nomeCategoria)) {
                AdminModel::criarCategoria($nomeCategoria);
            }
        }

        header('Location: index.php?route=painel-admin');
        exit;
    }

    // Cria um novo serviço
    public function criarServico()
    {
        $this->verificarAutenticacaoAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeServico = trim($_POST['nome_servico'] ?? '');
            $idCategoria = $_POST['id_categoria'] ?? null;

            if (!empty($nomeServico) && !empty($idCategoria)) {
                AdminModel::criarServicoComCategoria($nomeServico, $idCategoria);
            }
        }

        header('Location: index.php?route=painel-admin');
        exit;
    }

    // Banir usuário
    public function banirUsuario()
    {
        $this->verificarAutenticacaoAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_POST['id_usuario'] ?? null;
            $idAdminLogado = $_SESSION['id'] ?? $_SESSION['PK_id_TB_usuario'] ?? null;

            if (!empty($idUsuario) && $idUsuario != $idAdminLogado) {
                AdminModel::banirUsuario($idUsuario);
            }
        }

        header('Location: index.php?route=painel-admin');
        exit;
    }

    // Atualiza status da denúncia
    public function atualizarStatusDenuncia()
    {
        $this->verificarAutenticacaoAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idDenuncia = $_POST['id_denuncia'] ?? null;
            $novoStatus = $_POST['status'] ?? null;

            if (!empty($idDenuncia) && !empty($novoStatus)) {
                AdminModel::atualizarStatusDenuncia($idDenuncia, $novoStatus);
            }
        }

        header('Location: index.php?route=painel-admin');
        exit;
    }

    // Processa banimento por denúncia
    public function processarBanimentoPorDenuncia()
    {
        $this->verificarAutenticacaoAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuarioAlvo = $_POST['id_usuario_alvo'] ?? null;
            $idDenuncia    = $_POST['id_denuncia'] ?? null;
            $idAdminLogado = $_SESSION['id'] ?? $_SESSION['PK_id_TB_usuario'] ?? null;

            if (!empty($idUsuarioAlvo) && $idUsuarioAlvo != $idAdminLogado) {
                AdminModel::banirUsuario($idUsuarioAlvo);

                if (!empty($idDenuncia)) {
                    AdminModel::atualizarStatusDenuncia($idDenuncia, 'resolvido');
                }
            }
        }

        header('Location: index.php?route=painel-admin');
        exit;
    }
}