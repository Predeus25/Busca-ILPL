
<?php
    // Captura os dados do formulário
    $email = $_REQUEST["txtEmail"];
    $senha = $_REQUEST["txtSenha"];

    // Conecta ao banco
    include "conexao.php";

    try {
        // Verifica se o email já está cadastrado
        $verifica = $conexao->prepare("SELECT COUNT(*) FROM registros WHERE email = :email");
        $verifica->bindValue(':email', $email);
        $verifica->execute();
        $existe = $verifica->fetchColumn();

        if ($existe > 0) {
            echo "<div id='erro'><h2>Email já cadastrado!</h2></div>";
            echo "<br/>";
            echo "<button onclick='history.back()'>Voltar</button>";
            exit();
        }

        // Criptografa a senha
        $senhaSegura = password_hash($senha, PASSWORD_DEFAULT);

        // Prepara e executa o INSERT
        $sql = "INSERT INTO registros (email, senha) VALUES (:email, :senha)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(':email', $email);
        $stm->bindValue(':senha', $senhaSegura);

        $resultado = $stm->execute();

        if ($resultado) {
            header("Location: Cadastro_2.html");
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