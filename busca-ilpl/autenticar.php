<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    session_start();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = isset($_POST["email"]) ? $_POST["email"] : "";
        $senha = isset($_POST["senha"]) ? $_POST["senha"] : "";
        
        if (empty($email) || empty($senha)) {
            $mensagem = "Por favor, preencha todos os campos.";
            header("Location: Login.php?erro=" . urlencode($mensagem));
            exit();
        }
        
        try {
            include "conexao.php";
            
            $consulta = $conexao->prepare("SELECT id, email, senha FROM registros WHERE email = :email");
            $consulta->bindValue(':email', $email);
            $consulta->execute();
            
            if ($consulta->rowCount() > 0) {
                $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($senha, $usuario['senha'])) {
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_senha'] = $usuario['senha'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    
                    header("Location: Pequisa.html");
                    exit();
                } 
                else if ($senha === $usuario['senha']) {
                    $nova_senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $atualizar = $conexao->prepare("UPDATE registros SET senha = :senha WHERE id = :id");
                    $atualizar->bindValue(':senha', $nova_senha_hash);
                    $atualizar->bindValue(':id', $usuario['id']);
                    $atualizar->execute();
                    
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'] ?? 'Usuário';
                    $_SESSION['usuario_email'] = $usuario['email'];
                    
                    header("Location: Pequisa.html");
                    exit();
                } 
                else {
                    $mensagem = "Senha incorreta. Por favor, tente novamente.";
                    header("Location: Login.php?erro=" . urlencode($mensagem));
                    exit();
                }
            } else {
                $mensagem = "E-mail não cadastrado. Por favor, verifique ou registre-se.";
                header("Location: Login.php?erro=" . urlencode($mensagem));
                exit();
            }
        } catch (PDOException $e) {
            $mensagem = "Erro no banco de dados: " . $e->getMessage();
            header("Location: Login.php?erro=" . urlencode($mensagem));
            exit();
        }
    } else {
        header("Location: Login.php");
        exit();
    }
?>
