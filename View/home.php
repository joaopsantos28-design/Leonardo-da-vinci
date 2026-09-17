<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Model\Obra;

session_start();

$obras = (new Obra())->readAllObras();

$obras = array_reverse($obras);

$obraSalva = isset($_GET["salva"]);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leonardo da Vinci — Galeria Digital</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../templates/css/style.css">
</head>
<body>

  <!-- Cabeçalho -->
  <header class="cabecalho">
    <div class="cabecalho__faixa">

        <a href="../View/home.php" class="marca">
            <span class="marca__monograma">LV</span>

            <span class="marca__nome">
                Leonardo da Vinci
                <small id="saudacao-usuario">seu cantinho de arte</small>
            </span>
        </a>

        <button
            class="nav-alternar"
            aria-label="Abrir menu"
            aria-expanded="false">
            ☰
        </button>

        <nav class="nav-principal">
            <a href="../View/home.php" class="ativo">Início</a>
            <a href="#galeria">Obras</a>
            <a href="#sobre">Sobre Leonardo</a>
            <a href="../View/cadastrar-obra.php">Cadastrar obra</a>
            <a href="../index.php" data-sair>Sair</a>
        </nav>

    </div>
</header>

  <!-- Hero -->
  <section class="heroi">
    <div class="envolucro heroi__grade">
      <div>
       <p class="heroi__rotulo">
  FLORENÇA, 1452 → AMBOISE, 1519 · UMA VIDA GUIADA PELA CURIOSIDADE
</p>

<h1>
  Uma vida inteira. Poucas pinturas. <em>Um legado imenso.</em>
</h1>

<p>
  Leonardo da Vinci dedicou sua vida à curiosidade, à observação e à criação.
  Mesmo tendo deixado relativamente poucas pinturas concluídas, suas obras e
  estudos atravessaram séculos e continuam despertando perguntas até hoje.
</p>
        <div class="heroi__acoes">
          <a href="#galeria" class="btn btn--primario">Bora ver as obras 🎨</a>
          <a href="#sobre" class="btn btn--contorno-claro">Conhecer o artista</a>
        </div>
      </div>

      <figure class="heroi__moldura">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Mona_Lisa.jpg/500px-Mona_Lisa.jpg" alt="Mona Lisa, de Leonardo da Vinci">
        <figcaption class="heroi__legenda">Mona Lisa, c. 1503–1506</figcaption>
      </figure>
    </div>
  </section>

  <!-- Galeria de obras -->
  <section class="secao" id="galeria">
    <div class="envolucro">
      <div class="secao-titulo">
        <div>
         <h2>Poucas obras. Muitas histórias.</h2>

<p>
  Leonardo não deixou uma grande quantidade de pinturas concluídas.
  Mas cada uma delas revela uma parte de sua maneira única de observar o mundo.
</p>
        </div>
      </div>

      <div class="galeria" id="grade-galeria">
        <?php if (empty($obras)): ?>

          <div class="vazio">
            <p>Nenhuma obra por aqui ainda 🖼️</p>
            <p>Cadastre a primeira e ela aparece nesta galeria.</p>
          </div>

        <?php else: ?>
          <?php foreach ($obras as $obra): ?>

            <?php
              $imagem = trim((string) $obra["imagem"]);
              $descricao = trim((string) $obra["descricao"]);
              $resumo = mb_strimwidth($descricao, 0, 140, "…");
            ?>

            <article class="obra">
              <div class="obra__imagem">
                <?php if ($imagem !== ""): ?>
                  <img src="<?= htmlspecialchars($imagem) ?>"
                       alt="<?= htmlspecialchars($obra["nome"]) ?>"
                       loading="lazy">
                <?php endif; ?>
              </div>

              <div class="obra__corpo">
                <p class="obra__ano"><?= htmlspecialchars($obra["ano"] ?: "Ano não informado") ?></p>

                <h3 class="obra__titulo"><?= htmlspecialchars($obra["nome"]) ?></h3>

                <p class="obra__descricao"><?= htmlspecialchars($resumo ?: "Sem descrição cadastrada.") ?></p>

                <div class="obra__rodape">
                  <span class="etiqueta"><?= htmlspecialchars($obra["tecnica"] ?: "Técnica não informada") ?></span>

                  <button type="button"
                          class="btn btn--fantasma"
                          data-abrir-obra
                          data-nome="<?= htmlspecialchars($obra["nome"]) ?>"
                          data-ano="<?= htmlspecialchars($obra["ano"]) ?>"
                          data-tecnica="<?= htmlspecialchars($obra["tecnica"]) ?>"
                          data-local="<?= htmlspecialchars($obra["localizacao"]) ?>"
                          data-imagem="<?= htmlspecialchars($imagem) ?>"
                          data-descricao="<?= htmlspecialchars($descricao) ?>">
                    Ver detalhes
                  </button>
                </div>
              </div>
            </article>

          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="envolucro">
      <div class="cta-cadastro">
        <p>Conhece uma obra que ainda não está por aqui?</p>
        <a href="cadastrar-obra.php" class="btn btn--primario">
    + Cadastrar nova obra
</a>
    </div>
  </section>

  <section class="frase-museu">
  <div class="envolucro">
    <p class="frase-museu__principal">
      “Poucas obras chegaram até nós.
      <em>Mas poucas pessoas deixaram tanto para o mundo.</em>”
    </p>

    <span>LEONARDO DA VINCI · 1452—1519</span>
  </div>
</section>

  <!-- Sobre Leonardo -->
  <section class="secao sobre" id="sobre">
    <div class="envolucro sobre__grade">
      <div class="sobre__retrato">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/28/Francesco_Melzi_-_Portrait_of_Leonardo_-_WGA14795.jpg/400px-Francesco_Melzi_-_Portrait_of_Leonardo_-_WGA14795.jpg" alt="Retrato atribuído a Leonardo da Vinci">
        <ul class="sobre__ficha">
          <li><span>Nome completo</span><span>Leonardo di ser Piero da Vinci</span></li>
          <li><span>Nascimento</span><span>15 de abril de 1452, Vinci, Itália</span></li>
          <li><span>Falecimento</span><span>2 de maio de 1519, Amboise, França</span></li>
        </ul>
      </div>

      <div>
        <h2>Sobre Leonardo da Vinci</h2>

<p class="rotulo">
  Uma mente inquieta, curiosa e sempre em movimento 🌻
</p>
        <div class="sobre__areas">
          <span class="etiqueta">Pintura</span>
          <span class="etiqueta">Escultura</span>
          <span class="etiqueta">Arquitetura</span>
          <span class="etiqueta">Engenharia</span>
          <span class="etiqueta">Anatomia</span>
          <span class="etiqueta">Ciência</span>
        </div>

        <div class="frase-destaque">
  <p>
    “Leonardo não enxergava o mundo apenas como ele era,
    mas como algo que ainda podia ser descoberto.”
  </p>
</div>

        <div class="sobre__blocos">
          <div class="sobre__bloco">

  <h3>Biografia</h3>

  <p>
    Nascido em Vinci, na Toscana, Leonardo formou-se no ateliê de Andrea del Verrocchio,
    em Florença. Ao longo da vida, dividiu seu tempo entre Milão, Roma e a França,
    trabalhando para importantes mecenas e mantendo uma curiosidade que ultrapassava
    os limites da arte.
  </p>

  <p>
    Para Leonardo, observar, desenhar, estudar e experimentar faziam parte de um
    mesmo processo. Seus cadernos mostram uma mente que estava constantemente
    fazendo perguntas sobre o mundo ao seu redor.
  </p>
</div>

          <div class="sobre__bloco">
            <h3>Contribuições para a arte</h3>
            <p>
              Aperfeiçoou técnicas como o sfumato e o claro-escuro, elevando o realismo da pintura
              e influenciando gerações de artistas. Seus cadernos reúnem milhares de páginas com
              estudos de composição, luz e anatomia aplicados diretamente às suas telas.
            </p>
          </div>

          <div class="sobre__bloco">
            <h3>Contribuições para a ciência</h3>
            <p>
              Investigou anatomia humana, hidráulica, óptica e mecânica de voo muito antes de essas
              áreas se tornarem disciplinas formais, deixando projetos e observações que se
              antecipavam em séculos ao seu tempo.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Rodapé -->
  <footer class="rodape">
    <div class="envolucro rodape__faixa">
      <span>Feito com 💛 — Galeria Leonardo da Vinci, projeto de demonstração acadêmica.</span>
      <a href="#galeria">Voltar ao topo</a>
    </div>
  </footer>

  <!-- Modal de detalhes da obra -->
  <div class="modal-fundo" id="modal-obra">
    <div class="modal">
      <div class="modal__imagem">
        <img id="modal-imagem" src="" alt="">
      </div>
      <div class="modal__corpo">
        <button type="button" class="modal__fechar" data-fechar-modal aria-label="Fechar">×</button>
        <p class="rotulo" id="modal-ano"></p>
        <h3 id="modal-titulo"></h3>
        <p id="modal-descricao"></p>
        <ul class="modal__ficha">
          <li><span>Técnica</span><span id="modal-tecnica"></span></li>
          <li><span>Localização atual</span><span id="modal-local"></span></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Toast de confirmação -->
  <div class="toast" id="toast"></div>

  <script>
    const modal = document.getElementById("modal-obra");

    document.querySelectorAll("[data-abrir-obra]").forEach(botao => {
      botao.addEventListener("click", () => {
        const dados = botao.dataset;
        const imagem = document.getElementById("modal-imagem");

        imagem.src = dados.imagem || "";
        imagem.alt = dados.nome;

        document.getElementById("modal-ano").textContent = dados.ano || "Ano não informado";
        document.getElementById("modal-titulo").textContent = dados.nome;
        document.getElementById("modal-descricao").textContent = dados.descricao || "Sem descrição cadastrada.";
        document.getElementById("modal-tecnica").textContent = dados.tecnica || "—";
        document.getElementById("modal-local").textContent = dados.local || "—";

        modal.classList.add("aberto");
      });
    });

    function fecharModal() {
      modal.classList.remove("aberto");
    }

    document.querySelector("[data-fechar-modal]").addEventListener("click", fecharModal);

    modal.addEventListener("click", evento => {
      if (evento.target === modal) fecharModal();
    });

    document.addEventListener("keydown", evento => {
      if (evento.key === "Escape") fecharModal();
    });

    const alternar = document.querySelector(".nav-alternar");

    alternar.addEventListener("click", () => {
      const aberto = document.querySelector(".nav-principal").classList.toggle("aberto");
      alternar.setAttribute("aria-expanded", aberto);
    });

    <?php if ($obraSalva): ?>
      const toast = document.getElementById("toast");
      toast.textContent = "Obra cadastrada com sucesso 🎨";
      toast.classList.add("mostrar");
      setTimeout(() => toast.classList.remove("mostrar"), 3200);
      history.replaceState(null, "", "home.php#galeria");
    <?php endif; ?>
  </script>

</body>
</html>