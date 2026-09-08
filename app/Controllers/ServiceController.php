<?php
require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/CriptoController.php";


class ServiceController
{

    public function salvarPedido()
    {
        // Garante que o cliente está logado e o ID existe na sessão
        if (!isset($_SESSION["id_cliente"])) {
            header("Location: ?route=login-form");
            exit;
        }

        $data = $_POST;
        $data["FK_id_TB_cliente"] = $_SESSION["id_cliente"];

        $sucesso = ServiceModel::criarSolicitacao($data);

        if (!$sucesso) {
            echo "Erro ao realizar o pedido. Tente novamente.";
            return;
        }

        header("Location: ?route=meus-pedidos");
        exit;
    }


    function buscarCoordenadasPorCep($cep) {
    // Remove caracteres especiais do CEP (deixa só números)
    $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

    // API pública da AwesomeAPI (retorna lat/lng direto do CEP)
    $url = "https://cep.awesomeapi.com.br/json/{$cepLimpo}";

    // Configuração de cabeçalho necessária para requisições em PHP
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: FastServiceApp/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    $resposta = @file_get_contents($url, false, $context);

    if ($resposta === false) {
        return null; // Caso ocorra erro ou CEP não exista
    }

    $dados = json_decode($resposta, true);

    // Retorna um array com lat e lng
    if (isset($dados['lat']) && isset($dados['lng'])) {
        return [
            'latitude'  => (float) $dados['lat'],
            'longitude' => (float) $dados['lng']
        ];
    }

    return null;
}
    public function filtrarPrestadores()
    {
        // Verifica se o cliente está logado para saber a cidade dele
        if (!isset($_SESSION["id"])) {
            header("Location: ?route=login-form");
            exit;
        }

        // Carrega a view passando os dados
        require_once "app/Views/servicos/escolher-prestador.php";
    }

    public function listaPrestador()
    {
        require_once "app/Views/servicos/lista-prestador.php";
    }


}