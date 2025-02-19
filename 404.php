<?php if (isset($link[1]) && $link[1] == 'naopermitido') { ?>
  <!-- 404 Error Text -->
  <div class="container">
    <div class="row justify-content-center align-items-center mt-5 ">
      <div class="col-md-6 text-center">
        <!-- Ícone ou número de erro -->
        <div class="display-1 fw-bold text-secondary mb-4" style="
    color: #ff1f1f !important;!i;!;
">
          Acesso Negado
        </div>
        <!-- Título da mensagem -->
        <h1 class="h3 mb-3 text-gray-800">
          OPS!!!
        </h1>
        <!-- Descrição da mensagem -->
        <p class="lead text-gray-800 mb-4">
          Parece que você tentou acessar uma página que não existe ou não tem permissão para visualizar.
        </p>
        <!-- Ações sugeridas -->
        <div class="d-grid gap-2 d-md-block">
          <a href="/" class="btn btn-primary btn-lg">Voltar para a página inicial</a>
          <a href="mailto:admin@example.com" class="btn btn-outline-secondary btn-lg">Contactar o administrador</a>
        </div>
      </div>
    </div>
  </div>
<?php } else { ?>
  <!-- 404 Error Text -->
  <div class="text-center">
    <div class="error mx-auto" data-text="404">404</div>
    <p class="lead text-gray-800 mb-5">Página não localizada</p>
    <p class="text-gray-500 mb-0">Volte para a página inicial ou contactar o administrador</p>
  </div>
<?php } ?>