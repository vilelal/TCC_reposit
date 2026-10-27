<?php

require_once __DIR__ . '/../Models/DenunciaModel.php';

class DenunciaController {

    public function registrarDenuncia() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idDenunciante = $_SESSION['id'] ?? null;
            $idDenunciado = $_POST['id_denunciado'] ?? null;
            $idSolicitacao = !empty($_POST['id_solicitacao']) ? $_POST['id_solicitacao'] : null;
            $motivo = $_POST['motivo'] ?? '';
            $descricao = $_POST['descricao'] ?? '';

            if (!$idDenunciante || !$idDenunciado || empty($motivo) || empty($descricao)) {
                $_SESSION['erro_denuncia'] = "Preencha todos os campos obrigatórios.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $sucesso = DenunciaModel::criarDenuncia($idDenunciante, $idDenunciado, $idSolicitacao, $motivo, $descricao);

            if ($sucesso) {
                $_SESSION['sucesso_denuncia'] = "Denúncia enviada com sucesso! Nossa equipe irá analisar o caso.";
            } else {
                $_SESSION['erro_denuncia'] = "Erro ao enviar denúncia. Tente novamente.";
            }

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}