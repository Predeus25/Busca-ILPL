<?php
    // Conecta ao banco
    include "conexao.php";

    try {
        // Captura os dados do formulário
        $nome           = $_POST['txtNome'];
        $rg             = $_POST['txtRg'];
        $cpf            = $_POST['txtCpf'];
        $tipo_sangue    = $_POST['tipo_sanguineo'];
        $doenca         = $_POST['doenca'];
        $sexo           = $_POST['txtSexo'];
        $outra_doenca   = isset($_POST['outra_doenca']) ? $_POST['outra_doenca'] : null;
        $sexo           = $_POST['txtSexo'];

        // Converte a data para formato SQL (de dd/mm/aaaa para aaaa-mm-dd)
        $data_nascimento = DateTime::createFromFormat('d/m/Y', $_POST['data'])->format('Y-m-d');

        // Insere no banco
        $sql = "INSERT INTO dados_idoso 
                (nome, rg, cpf, tipo_sanguineo, doenca, outra_doenca, data_nascimento, sexo) 
                VALUES 
                (:nome, :rg, :cpf, :tipo_sanguineo, :doenca, :outra_doenca, :data_nascimento, :sexo)";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':rg', $rg);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':tipo_sanguineo', $tipo_sangue);
        $stmt->bindValue(':doenca', $doenca);
        $stmt->bindValue(':outra_doenca', $outra_doenca);
        $stmt->bindValue(':data_nascimento', $data_nascimento);
        $stmt->bindValue(':sexo', $sexo);

        $resultado = $stmt->execute();

        if ($resultado) {
            header("Location: Cadastro_3.html");
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
