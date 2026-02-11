<?php
    $enderecoBD = "localhost"; 
    $banco = "registro";
    $usuarioBD = "root";
    $senhaBD = "";

    try {
        $conexao = new PDO("mysql:host=$enderecoBD;dbname=$banco;charset=utf8", $usuarioBD, $senhaBD);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }

    
?>