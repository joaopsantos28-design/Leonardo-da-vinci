<?php

require_once __DIR__ . "/vendor/autoload.php";

use Model\User;
use Model\Obra;
use Controller\UserController;
use Controller\ObraController;

// se a rota for /usuarios ou /obras, quem responde e a API (json), nao a tela de login
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", trim($path, "/"));
$resource = $parts[0] ?? null;
$id = $parts[1] ?? null;

if (in_array($resource, ["usuarios", "obras"], true)) {
    header("Content-Type: application/json; charset=UTF-8");

    match ($resource) {
        "usuarios" => (new UserController(new User()))->ProcessRequest($_SERVER["REQUEST_METHOD"], $id),
        "obras" => (new ObraController(new Obra()))->ProcessRequest($_SERVER["REQUEST_METHOD"], $id),
    };

    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar — Leonardo da Vinci</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="templates/css/style.css">
</head>
<body>

  <div class="tela-auth">

    <!-- Lateral ilustrativa -->
    <aside class="tela-auth__lateral">
      <div class="marca">
        <span class="marca__monograma">LV</span>
        <span class="marca__nome">Galeria Leonardo<small>seu cantinho de arte</small></span>
      </div>

      <!-- Ilustração do Homem Vitruviano em traço simples (SVG próprio) -->
      <svg class="tela-auth__figura" viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustração inspirada no Homem Vitruviano">
        <circle cx="110" cy="110" r="95" stroke="#ede6d6" stroke-width="1" opacity="0.6"/>
        <rect x="30" y="30" width="160" height="160" stroke="#ede6d6" stroke-width="1" opacity="0.6"/>
        <circle cx="110" cy="62" r="14" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="76" x2="110" y2="150" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="95" x2="45" y2="80" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="95" x2="175" y2="80" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="95" x2="38" y2="115" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="95" x2="182" y2="115" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="150" x2="75" y2="205" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="150" x2="145" y2="205" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="150" x2="60" y2="200" stroke="#ede6d6" stroke-width="1.4"/>
        <line x1="110" y1="150" x2="160" y2="200" stroke="#ede6d6" stroke-width="1.4"/>
      </svg>

      <blockquote class="tela-auth__citacao">
        “A pintura é uma poesia que se vê em vez de sentir.” 🎨
        <span>Leonardo da Vinci</span>
      </blockquote>
    </aside>

    <!-- Formulário de login -->
    <div class="tela-auth__conteudo">
      <div class="cartao-auth">
        <div class="cartao-auth__topo">
          <span class="marca__monograma">LV</span>
          <h1>Leonardo da Vinci</h1>
          <p>Entra e vem espiar as obras mais queridinhas do Renascimento ✨</p>
        </div>

        <div class="mensagem-flash" id="flash-login"></div>

        <form id="form-login" novalidate>
          <div class="campo">
            <label for="login-email">E-mail</label>
            <input type="email" id="login-email" name="email" placeholder="seu@email.com" autocomplete="email">
            <span class="campo__erro" id="erro-login-email"></span>
          </div>

          <div class="campo campo--senha">
            <label for="login-senha">Senha</label>
            <input type="password" id="login-senha" name="senha" placeholder="••••••••" autocomplete="current-password">
            <span class="campo__erro" id="erro-login-senha"></span>
          </div>

          <button type="submit" class="btn btn--primario btn--bloco">Entrar</button>
        </form>

        <div class="cartao-auth__rodape">
          Ainda não tem conta? <a href="../View/cadastro.php">Bora criar uma 🎉</a>
        </div>
      </div>
    </div>

  </div>
  
</body>
</html>
