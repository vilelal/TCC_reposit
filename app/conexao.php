<?php

function conectarBanco() {
    $env = parse_ini_file(__DIR__ . "/../.env");

    $host = $env["DB_HOST"];
    $user = $env["DB_USER"];
    $password = $env["DB_PASSWORD"];
    $database = $env["DB_DATABASE"];

    $conexao = new mysqli($host, $user, $password, $database);


    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    // Define o charset para evitar problemas com caracteres especiais
    $conexao->set_charset("utf8mb4");

    return $conexao;
}