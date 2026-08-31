<?php

/**
 * Estabelece a conexão com o banco de dados MySQL utilizando as credenciais do arquivo .env
 * 
 * @return mysqli Objeto de conexão ativa com o banco de dados
 */

function conectarBanco() {
    // Carrega e faz o parse das variáveis de ambiente localizadas no arquivo .env na raiz do projeto
    $env = parse_ini_file(__DIR__ . "/../.env");

    // Extrai as credenciais do array gerado pelo .env
    $host = $env["DB_HOST"];
    $user = $env["DB_USER"];
    $password = $env["DB_PASSWORD"];
    $database = $env["DB_DATABASE"];

    // Cria uma nova instância de conexão do MySQLi
    $conexao = new mysqli($host, $user, $password, $database);

    // Verifica se ocorreu algum erro durante a tentativa de conexão
    if ($conexao->connect_error) {
        // Interrompe a execução do script e exibe a mensagem de erro do MySQL
        die("Erro na conexão: " . $conexao->connect_error);
    }

    // Define a codificação de caracteres como UTF-8 (utf8mb4) para suportar acentuação e emojis
    $conexao->set_charset("utf8mb4");

    // Retorna a conexão pronta para uso
    return $conexao;
}