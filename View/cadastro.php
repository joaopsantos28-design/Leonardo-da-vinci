<?php

require_once "../vendor/autoload.php";

use Model\User;

session_start();

$userModel = new User();
$erroCadastro = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $confirmarSenha = $_POST["confirmarSenha"] ?? "";

    if ($nome === "" || $email === "" || $senha === "") {
        $erroCadastro = "Preencha nome, e-mail e senha.";
    } elseif (strlen($senha) < 6) {
        $erroCadastro = "A senha precisa ter no mínimo 6 caracteres.";
    } elseif ($senha !== $confirmarSenha) {
        $erroCadastro = "As senhas não coincidem.";
    } elseif ($userModel->emailExists($email)) {
        $erroCadastro = "Já existe uma conta com esse e-mail.";
    } else {

        $senhaHash = password_hash($senha, PASSWORD_ARGON2ID, [
            "memory_cost" => 1 << 17,
            "time_cost" => 4,
            "threads" => 2
        ]);

        $idUsuario = $userModel->createUser($nome, $email, $senhaHash);

        $_SESSION["id_usuario"] = $idUsuario;

        header("Location: ../View/home.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar conta — Leonardo da Vinci</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../templates/css/style.css">
</head>
<body>

  <div class="tela-auth">

    <aside class="tela-auth__lateral">
      <div class="marca">
        <span class="marca__monograma">LV</span>
        <span class="marca__nome">Galeria Leonardo<small>seu cantinho de arte</small></span>
      </div>

      <svg class="tela-auth__figura" viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustração de um caderno de estudos renascentista">
        <rect x="40" y="26" width="140" height="168" stroke="#ede6d6" stroke-width="1" opacity="0.6"/>
        <line x1="40" y1="52" x2="180" y2="52" stroke="#ede6d6" stroke-width="1" opacity="0.5"/>
        <line x1="58" y1="72" x2="162" y2="72" stroke="#ede6d6" stroke-width="1" opacity="0.4"/>
        <line x1="58" y1="88" x2="162" y2="88" stroke="#ede6d6" stroke-width="1" opacity="0.4"/>
        <line x1="58" y1="104" x2="130" y2="104" stroke="#ede6d6" stroke-width="1" opacity="0.4"/>
        <circle cx="110" cy="140" r="28" stroke="#ede6d6" stroke-width="1" opacity="0.5"/>
        <line x1="86" y1="140" x2="134" y2="140" stroke="#ede6d6" stroke-width="1" opacity="0.5"/>
        <line x1="110" y1="116" x2="110" y2="164" stroke="#ede6d6" stroke-width="1" opacity="0.5"/>
      </svg>

      <blockquote class="tela-auth__citacao">
        “Quem pouco pensa, muito erra.” 📓
        <span>Leonardo da Vinci</span>
      </blockquote>
    </aside>

    <div class="tela-auth__conteudo">
      <div class="cartao-auth">
        <div class="cartao-auth__topo">
          <span class="marca__monograma">LV</span>
          <h1>Criar conta</h1>
          <p>Só um minutinho e você já entra na galeria 💫</p>
        </div>

        <div class="mensagem-flash" id="flash-cadastro"><?= $erroCadastro ? htmlspecialchars($erroCadastro) : "" ?></div>

        <form id="form-cadastro" method="POST" novalidate>
          <div class="campo">
            <label for="cadastro-nome">Nome completo</label>
            <input type="text" id="cadastro-nome" name="nome" placeholder="Seu nome completo" autocomplete="name">
            <span class="campo__erro" id="erro-cadastro-nome"></span>
          </div>

          <div class="campo">
            <label for="cadastro-email">E-mail</label>
            <input type="email" id="cadastro-email" name="email" placeholder="seu@email.com" autocomplete="email">
            <span class="campo__erro" id="erro-cadastro-email"></span>
          </div>

          <div class="campo campo--senha">
            <label for="cadastro-senha">Senha</label>
            <input type="password" id="cadastro-senha" name="senha" placeholder="Mínimo de 6 caracteres" autocomplete="new-password">
            <span class="campo__erro" id="erro-cadastro-senha"></span>
          </div>

          <div class="campo campo--senha">
            <label for="cadastro-confirmar-senha">Confirmar senha</label>
            <input type="password" id="cadastro-confirmar-senha" name="confirmarSenha" placeholder="Repita a senha" autocomplete="new-password">
            <span class="campo__erro" id="erro-cadastro-confirmar-senha"></span>
          </div>

          <button type="submit" class="btn btn--primario btn--bloco">Criar conta</button>
        </form>

        <div class="cartao-auth__rodape">
          Já tenho uma conta <a href="../index.php">Entrar</a>
        </div>
      </div>
    </div>

  </div>

</body>
</html>
