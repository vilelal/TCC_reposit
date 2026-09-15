    <?php

class GeocodingService 
{
    public static function buscarCoordenadasPorEndereco($rua, $numero, $cidade, $cep) 
    {
        $enderecoCompleto = "{$rua}, {$numero}, {$cidade}, {$cep}, Brasil"; //aqui concatena o endereço completo de alguém
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($enderecoCompleto); //deixa bom para url e json manipula mais facil

        $opts = [
            "http" => [
                "header" => "User-Agent: FastServiceTCC/1.0\r\n"
            ]
        ]; // aqui é mais uma segurança, para não sobrecarregar muito o servidor e não dar erro
        $context = stream_context_create($opts);//meio que identifica nós para usar a api ta ligado, tipo um bilhete de entrada
        $resposta = @file_get_contents($url, false, $context); //envia requisição web para url da api, guarda resultado em texto na variave
        
        if ($resposta === false) {  //caso a resquisição falhe mesmooo
            return null;
        }

        $dados = json_decode($resposta, true); //tiro do json e deixo em array pgp

        if (!empty($dados) && isset($dados[0]['lat']) && isset($dados[0]['lon'])) { // verifica se tem dados reais, pega o primeiro que possui mais precisão e ve se aparecesse longitude nessa primeira
            return [
                'latitude'  => $dados[0]['lat'], // aqui o array pega a lat do primeiro resultado 0
                'longitude' => $dados[0]['lon'] // aqui o array pega a lon do primeiro resultado 0
            ];
        }

        return null; // basicamente se der errado esse if acima, e não salvar nada no banco depois
    }
}