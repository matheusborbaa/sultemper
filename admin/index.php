<?php
require __DIR__ . '/config.php';

if (estaLogado()) {
    header('Location: painel.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pequena proteção contra força bruta
    $_SESSION['tentativas'] = ($_SESSION['tentativas'] ?? 0) + 1;
    if ($_SESSION['tentativas'] > 5) {
        sleep(3);
    }

    $cred = lerCredenciais();
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if ($usuario === $cred['usuario'] && password_verify($senha, $cred['hash'])) {
        session_regenerate_id(true);
        $_SESSION['sultemper_admin'] = true;
        $_SESSION['tentativas'] = 0;
        header('Location: painel.php');
        exit;
    }
    $erro = 'Usuário ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel Sultemper — Login</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #000927 0%, #0d4377 100%);
    }
    .login {
      width: 380px;
      background: #fff;
      border-radius: 20px;
      padding: 40px 36px;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
    }
    .login h1 { font-size: 22px; color: #0d4377; margin-bottom: 4px; }
    .login p.sub { font-size: 14px; color: #667; margin-bottom: 24px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #333; margin: 14px 0 6px; }
    input {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #dcdfe4;
      border-radius: 10px;
      font-size: 15px;
      outline: none;
    }
    input:focus { border-color: #30a9ff; }
    button {
      width: 100%;
      margin-top: 22px;
      padding: 13px;
      border: none;
      border-radius: 999px;
      background: linear-gradient(100deg, #116ab2, #37a6ff);
      color: #fff;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
    }
    button:hover { filter: brightness(1.08); }
    .erro {
      margin-top: 14px;
      padding: 10px 14px;
      background: #fdeaea;
      border: 1px solid #f2c5c5;
      color: #9c2b2b;
      border-radius: 10px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <form class="login" method="post" autocomplete="off">
    <h1>Painel Sultemper</h1>
    <p class="sub">Acesse para editar o conteúdo do site</p>
    <label for="usuario">Usuário</label>
    <input type="text" id="usuario" name="usuario" required autofocus />
    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" required />
    <button type="submit">Entrar</button>
    <?php if ($erro): ?><div class="erro"><?= e($erro) ?></div><?php endif; ?>
  </form>
</body>
</html>
