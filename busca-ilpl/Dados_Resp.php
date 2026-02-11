<?php
    // Conecta ao banco
    include "conexao.php";

    try {
        // Captura os dados do formulário
        $nome           = $_POST['txtNome'];
        $email          = $_POST['txtEmail'];
        $endereco       = $_POST['txtEnd'];
        $bairro         = $_POST['txtBairro'];
        $numero         = $_POST['txtNum'];
        $complemento    = $_POST['txtComp'];
        $cep            = $_POST['txtCep'];
        $rg             = $_POST['txtRg'];
        $cpf            = $_POST['txtCpf'];
        $tipo_sangue    = $_POST['tipo_sanguineo'];
        $paren          = $_POST['paren'];
        $outro_paren    = isset($_POST['outro_paren']) ? $_POST['outro_paren'] : null;
        $cell           = $_POST['txtCell'];

        // Insere no banco
        $sql = "INSERT INTO dados_responsavel 
                (nome, email, cell, endereco, bairro, numero, complemento, cep, rg, cpf, tipo_sanguineo, parentesco, outro_parentesco) 
                VALUES 
                (:nome, :email, :cell, :endereco, :bairro, :numero, :complemento, :cep, :rg, :cpf, :tipo_sanguineo, :parentesco, :outro_parentesco)";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':cell', $cell);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':rg', $rg);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':tipo_sanguineo', $tipo_sangue);
        $stmt->bindValue(':parentesco', $paren);
        $stmt->bindValue(':outro_parentesco', $outro_paren);

        $resultado = $stmt->execute();

        if ($resultado) {
            header("Location: Pequisa.html");
            exit();
        } else {
            echo "<div id='erro'><h2>Erro ao gravar os dados.</h2></div>";
            echo "<br/>";
            echo "<button onclick='history.back()'>Voltar</button>";
        }

    } catch (PDOException $e) {
        echo "<div id='erro'><h2>Erro no banco de dados: " . $e->getMessage() . "</h2></div>";
        echo "<br/>";
        echo "<button onclick='history.back()'>Voltar</button>";
    }
?>
