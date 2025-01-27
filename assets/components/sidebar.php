<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo $url?>">
        <div class="sidebar-brand-text mx-3">
            <img src="<?php echo $url ?>assets/logo.png" alt="Joalheria" style="width: 100px;">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0" />

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="<?php echo $url?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Meu Painel</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider" />

    <!-- OPERACOES -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#menuOperacoes" 
           aria-expanded="false" aria-controls="menuOperacoes">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>Operações</span>
        </a>
        <div id="menuOperacoes" class="collapse" aria-labelledby="headingOperacoes" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Pedidos/cadastro" ?>">Novo Pedido</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Pedidos/listar" ?>">Pedidos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Fabrica/listar" ?>">Fábrica</a>
                <a class="collapse-item" href="<?php echo "{$url}!/ImpressaoEtiquetas/listar" ?>">Impressão de Etiquetas</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Consignacao/listar" ?>">Consignação</a>
            </div>
        </div>
    </li>

    <!-- GERENCIAMENTO DE PRODUTOS -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#gerenciamentoProdutos" 
           aria-expanded="false" aria-controls="gerenciamentoProdutos">
            <i class="fas fa-fw fa-box"></i>
            <span>Gerenciamento Produtos</span>
        </a>
        <div id="gerenciamentoProdutos" class="collapse" aria-labelledby="headingGerProdutos" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Produtos/listar" ?>">Produtos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/GrupoProdutos/listar" ?>">Grupo de Produtos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/SubGrupoProdutos/listar" ?>">Subgrupo de Produtos</a>
                <!-- <a class="collapse-item" href="<?php echo "{$url}!/Tipos/listar" ?>">Tipos</a> -->
                <a class="collapse-item" href="<?php echo "{$url}!/Cotacoes/listar" ?>">Cotações</a>
                <a class="collapse-item" href="<?php echo "{$url}!/EntradaMercadorias/listar" ?>">Entrada de Mercadoria</a>
                <a class="collapse-item" href="<?php echo "{$url}!/MovimentacaoEstoque/listar" ?>">Movimentação de Estoque</a>
            </div>
        </div>
    </li>

    <!-- FINANCEIRO -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#financeiro" 
           aria-expanded="false" aria-controls="financeiro">
            <i class="fas fa-fw fa-dollar-sign"></i>
            <span>Financeiro</span>
        </a>
        <div id="financeiro" class="collapse" aria-labelledby="headingFinanceiro" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Contas/listar/P" ?>">Contas a Pagar</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Contas/listar/R" ?>">Contas a Receber</a>
                <a class="collapse-item" href="<?php echo "{$url}!/CategoriaDespesa/listar" ?>">Categoria de Despesa</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Cartoes/listar" ?>">Cartões</a>
                <a class="collapse-item" href="<?php echo "{$url}!/ComissaoVendedor/cadastro" ?>">Comissão</a>
            </div>
        </div>
    </li>

    <!-- CADASTROS -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#cadastros" 
           aria-expanded="false" aria-controls="cadastros">
            <i class="fas fa-fw fa-users"></i>
            <span>Cadastros</span>
        </a>
        <div id="cadastros" class="collapse" aria-labelledby="headingCadastros" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Clientes/listar" ?>">Clientes</a>
                <a class="collapse-item" href="<?php echo "{$url}!/GrupoClientes/listar" ?>">Grupo de Clientes</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Fornecedores/listar" ?>">Fornecedores</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Usuarios/listar" ?>">Usuários</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Cargos/listar" ?>">Cargos</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Feriados/listar" ?>">Feriados</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block" />

    <!-- Sair -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo "{$url}sair.php"?>">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Sair</span>
        </a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
