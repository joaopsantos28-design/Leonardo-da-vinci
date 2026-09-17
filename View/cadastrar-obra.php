<?php

require_once "../vendor/autoload.php";

use Model\Obra;

session_start();

$obraModel = new Obra();
$erroObra = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");

    if ($nome === "") {
        $erroObra = "O nome da obra é obrigatório.";
    } else {

        $obraModel->createObra(
            $nome,
            $_POST["ano"] ?? "",
            $_POST["tecnica"] ?? "",
            $_POST["localizacao"] ?? "",
            $_POST["imagem"] ?? "",
            $_POST["descricao"] ?? "",
            $_SESSION["id_usuario"] ?? null
        );

        header("Location: home.php?salva=1#galeria");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar obra — Leonardo da Vinci</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../templates/css/style.css">
</head>
<body>

  <header class="cabecalho">
    <div class="cabecalho__faixa">
      <a href="../View/home.php" class="marca">
        <span class="marca__monograma">LV</span>
        <span class="marca__nome">Leonardo da Vinci<small id="saudacao-usuario">seu cantinho de arte</small></span>
      </a>

      <button class="nav-alternar" aria-label="Abrir menu" aria-expanded="false">☰</button>

      <nav class="nav-principal">
        <a href="../View/home.php">Início</a>
        <a href="../View/home.php#galeria">Obras</a>
        <a href="../View/home.php#sobre">Sobre Leonardo</a>
        <a href="../View/cadastrar-obra.php" class="ativo">Cadastrar obra</a>
        <a href="../index.php" data-sair>Sair</a>
      </nav>
    </div>
  </header>

  <main class="pagina-formulario">
    <div class="formulario-topo">
      <div class="envolucro">
        <h1>Cadastrar nova obra</h1>
        <p>Encontrou uma obra incrível? Adiciona ela aqui! 🖼️</p>
        <?php if ($erroObra): ?>
          <div class="mensagem-flash"><?= htmlspecialchars($erroObra) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="envolucro">
      <div class="cartao-formulario">
        <form id="form-obra" method="POST" novalidate>
          <div class="grade-formulario">
            <div class="campo">
              <label for="obra-nome">Nome da obra</label>
              <input type="text" id="obra-nome" name="nome" placeholder="Ex: Retrato de um Músico">
              <span class="campo__erro" id="erro-obra-nome"></span>
            </div>

            <div class="campo">
              <label for="obra-ano">Ano</label>
              <input type="text" id="obra-ano" name="ano" placeholder="Ex: 1485">
            </div>

            <div class="campo">
              <label for="obra-tecnica">Técnica</label>
              <input type="text" id="obra-tecnica" name="tecnica" placeholder="Ex: Óleo sobre madeira">
            </div>

            <div class="campo">
              <label for="obra-local">Localização atual</label>
              <input type="text" id="obra-local" name="localizacao" placeholder="Ex: Pinacoteca Ambrosiana, Milão">
            </div>

            <div class="campo campo--completo">
              <label for="obra-imagem">URL da imagem</label>
              <input type="url" id="obra-imagem" name="imagem" placeholder="https://...">
            </div>

            <div class="campo campo--completo">
              <div class="previa-imagem" id="previa-imagem">
                <span id="previa-imagem-texto">A prévia da imagem aparece aqui ✨</span>
              </div>
            </div>

            <div class="campo campo--completo">
              <label for="obra-descricao">Descrição</label>
              <textarea id="obra-descricao" name="descricao" placeholder="Breve descrição sobre a obra..."></textarea>
            </div>
          </div>

          <div class="formulario-acoes">
            <button type="button" class="btn btn--fantasma" id="cancelar-obra">Cancelar</button>
            <button type="submit" class="btn btn--primario">Cadastrar obra</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <footer class="rodape">
    <div class="envolucro rodape__faixa">
      <span>Feito com 💛 — Galeria Leonardo da Vinci, projeto de demonstração acadêmica.</span>
      <a href="home.php">Voltar à galeria</a>
    </div>
  </footer>

</body>
</html>