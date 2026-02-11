<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="styles_login.css">

</head>

<body>
  <div class="login-container">
    <h2>Entrar</h2>
    
    <?php
    // Exibição de mensagens de erro
    if(isset($_GET['erro'])) {
        echo '<div class="erro-mensagem">' . htmlspecialchars($_GET['erro']) . '</div>';
    }
    ?>
    
    <form action="autenticar.php" method="POST">
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <button type="submit">Entrar</button>
    </form>
    <div class="register-link">
      <p>Não tem uma conta? <a href="Cadastro_1.html">Registre-se aqui</a></p>
    </div>
  </div>
</body>
</html>
