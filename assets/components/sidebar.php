<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Seção: Início -->
    <li class="nav-item brand">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo $url?>">
            <div class="sidebar-brand-text mx-3">
                <img src="<?php echo $url ?>assets/logo.png" alt="Joalheria" style="width: 100px;">
            </div>
        </a>
    </li>
    <hr class="sidebar-divider my-0" />
    <li class="nav-item active">
        <a class="nav-link" href="<?php echo $url?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Meu Painel</span>
        </a>
    </li>
    <hr class="sidebar-divider" />

    <!-- Seção: Vendas e Operações -->
    <div class="sidebar-heading">
        Vendas e Operações
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#operacoesVendas" 
           aria-expanded="false" aria-controls="operacoesVendas">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Pedidos & Orçamentos</span>
        </a>
        <div id="operacoesVendas" class="collapse <?php echo in_array($link[1], ['Pedidos', 'Orcamento']) ? 'show' : ''; ?>" aria-labelledby="headingOperacoesVendas" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Pedidos/cadastro" ?>">Nova Venda</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Pedidos/listar" ?>">Todas Vendas</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Orcamento/cadastro" ?>">Novo Pedido</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Orcamento/listar" ?>">Todos Pedidos</a>
            </div>
        </div>
    </li>
    <!-- Serviços Adicionais -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#servicosExtras" 
           aria-expanded="false" aria-controls="servicosExtras">
            <i class="fas fa-fw fa-print"></i>
            <span>Serviços Extras</span>
        </a>
        <div id="servicosExtras" class="collapse <?php echo in_array($link[1], ['ImpressaoEtiquetas', 'Consignacao']) ? 'show' : ''; ?>" aria-labelledby="headingServicosExtras" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/ImpressaoEtiquetas/listar" ?>">Impressão de Etiquetas</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Consignacao/listar" ?>">Consignação</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider" />

    <!-- Seção: Estoque e Produtos -->
    <div class="sidebar-heading">
        Estoque e Produtos
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#produtosEstoque" 
           aria-expanded="false" aria-controls="produtosEstoque">
            <i class="fas fa-fw fa-box"></i>
            <span>Produtos & Estoque</span>
        </a>
        <div id="produtosEstoque" class="collapse <?php echo in_array($link[1], ['Produtos', 'Insumos', 'GrupoProdutos', 'SubGrupoProdutos', 'Definicoes', 'Cotacoes', 'EntradaMercadorias', 'MovimentacaoEstoque']) ? 'show' : ''; ?>" aria-labelledby="headingProdutosEstoque" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Produtos/listar" ?>">Produtos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Insumos/listar" ?>">Insumos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/GrupoProdutos/listar" ?>">Grupo de Produtos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/SubGrupoProdutos/listar" ?>">Subgrupo de Produtos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Definicoes/listar" ?>">Definições</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Cotacoes/listar" ?>">Cotações</a>
                <a class="collapse-item" href="<?php echo "{$url}!/EntradaMercadorias/listar" ?>">Entrada de Mercadorias</a>
                <a class="collapse-item" href="<?php echo "{$url}!/MovimentacaoEstoque/listar" ?>">Estoque</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider" />

    <!-- Seção: Financeiro -->
    <div class="sidebar-heading">
        Financeiro
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#financeiro" 
           aria-expanded="false" aria-controls="financeiro">
            <i class="fas fa-fw fa-dollar-sign"></i>
            <span>Financeiro</span>
        </a>
        <div id="financeiro" class="collapse <?php echo in_array($link[1], ['Contas', 'CategoriaDespesa', 'Cartoes', 'ComissaoVendedor', 'Caixa']) ? 'show' : ''; ?>" aria-labelledby="headingFinanceiro" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Caixa/lista" ?>">Fluxo de Caixa</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Contas/listar/P" ?>">Contas a Pagar</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Contas/listar/R" ?>">Contas a Receber</a>
                <a class="collapse-item" href="<?php echo "{$url}!/CategoriaDespesa/listar" ?>">Categoria de Despesa</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Cartoes/listar" ?>">Cartões</a>
                <a class="collapse-item" href="<?php echo "{$url}!/ComissaoVendedor/cadastro" ?>">Comissão</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider" />

    <!-- Seção: Cadastros -->
    <div class="sidebar-heading">
        Cadastros
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#cadastros" 
           aria-expanded="false" aria-controls="cadastros">
            <i class="fas fa-fw fa-users"></i>
            <span>Cadastros</span>
        </a>
        <div id="cadastros" class="collapse <?php echo in_array($link[1], ['Clientes', 'GrupoClientes', 'Fornecedores', 'Usuarios', 'Cargos', 'Feriados', 'Material', 'Categoria']) ? 'show' : ''; ?>" aria-labelledby="headingCadastros" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Clientes/listar" ?>">Clientes</a>
                <a class="collapse-item" href="<?php echo "{$url}!/GrupoClientes/listar" ?>">Grupo de Clientes</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Fornecedores/listar" ?>">Fornecedores</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Usuarios/listar" ?>">Usuários</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Cargos/listar" ?>">Cargos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Feriados/listar" ?>">Feriados</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Material/listar" ?>">Materiais</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Categoria/listar" ?>">Categorias</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider" />

    <!-- Seção: Loja -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo "{$url}!/Loja/listar" ?>">
            <i class="fas fa-fw fa-store"></i>
            <span>Lojas</span>  
        </a>
    </li>
    <hr class="sidebar-divider" />

    <!-- Seção: Relatórios e Fábrica -->
    <div class="sidebar-heading">
        Relatórios & Fábrica
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#relatorios" 
           aria-expanded="false" aria-controls="relatorios">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Relatórios</span>
        </a>
        <div id="relatorios" class="collapse <?php echo $link[1] == 'Relatorios' ? 'show' : ''; ?>" aria-labelledby="headingRelatorios" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Relatorios/vendas" ?>">Vendas</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Relatorios/financeiros" ?>">Financeiros</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Relatorios/estoque" ?>">Estoque</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#fabrica" 
           aria-expanded="false" aria-controls="fabrica">
            <i class="fas fa-fw fa-industry"></i>
            <span>Fábrica</span>
        </a>
        <div id="fabrica" class="collapse <?php echo $link[1] == 'Fabrica' ? 'show' : ''; ?>" aria-labelledby="headingFabrica" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Fabrica/aberto" ?>">Aberto</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Fabrica/andamento" ?>">Andamento</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Fabrica/finalizado" ?>">Finalizado</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider d-none d-md-block" />

    <!-- Seção: Sair -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo "{$url}sair.php"?>">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Sair</span>
        </a>
    </li>
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!--se o $link[1] nao existir nao coloca o js-->
<?php if (isset($link[1])) { ?>
<script>
  document.getElementById("sidebarToggle").addEventListener("click", function() {
    document.getElementById("accordionSidebar").classList.toggle("toggled");
  });
</script>
<?php } ?>



