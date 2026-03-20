<?php
// $podeVer e $podeManipular vêm de App/php/permissoes.php (incluído no index)
?>
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

    <?php $temPedidosOrc = $podeVer('Pedidos') || $podeVer('Orcamento'); if ($temPedidosOrc): ?>
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
        <div id="operacoesVendas" class="collapse <?php echo in_array($link[1] ?? '', ['Pedidos', 'Orcamento']) ? 'show' : ''; ?>" aria-labelledby="headingOperacoesVendas" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if ($podeManipular('Pedidos')): ?><a class="collapse-item" href="<?php echo "{$url}!/Pedidos/cadastro" ?>">Nova Venda</a><?php endif; ?>
                <?php if ($podeVer('Pedidos')): ?><a class="collapse-item" href="<?php echo "{$url}!/Pedidos/listar" ?>">Todas Vendas</a><?php endif; ?>
                <?php if ($podeManipular('Orcamento')): ?><a class="collapse-item" href="<?php echo "{$url}!/Orcamento/cadastro" ?>">Novo Pedido</a><?php endif; ?>
                <?php if ($podeVer('Orcamento')): ?><a class="collapse-item" href="<?php echo "{$url}!/Orcamento/listar" ?>">Todos Pedidos</a><?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php $temServicosExtras = $podeVer('ImpressaoEtiquetas') || $podeVer('Consignacao'); if ($temServicosExtras): ?>
    <!-- Serviços Adicionais -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#servicosExtras" 
           aria-expanded="false" aria-controls="servicosExtras">
            <i class="fas fa-fw fa-print"></i>
            <span>Serviços Extras</span>
        </a>
        <div id="servicosExtras" class="collapse <?php echo in_array($link[1] ?? '', ['ImpressaoEtiquetas', 'Consignacao']) ? 'show' : ''; ?>" aria-labelledby="headingServicosExtras" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if ($podeVer('ImpressaoEtiquetas')): ?><a class="collapse-item" href="<?php echo "{$url}!/ImpressaoEtiquetas/listar" ?>">Impressão de Etiquetas</a><?php endif; ?>
                <?php if ($podeVer('Consignacao')): ?>
                <a class="collapse-item" href="<?php echo "{$url}!/Consignacao/listar" ?>">Consignação</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Consignacao/vendedoras" ?>">Produtos por Vendedora</a>
                <?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>
    <?php if ($temPedidosOrc || $temServicosExtras): ?><hr class="sidebar-divider" /><?php endif; ?>

    <?php if ($podeVer('Estoque')): ?>
    <!-- Seção: Estoque (Admin e usuários da loja visualizam quantidade por loja) -->
    <div class="sidebar-heading">
        Estoque
    </div>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo "{$url}!/Estoque/listar" ?>">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Estoque por Loja</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if ($podeVer('Inventario')): ?>
    <!-- Seção: Inventário (Devoluções) -->
    <?php if (!$podeVer('Estoque')): ?><hr class="sidebar-divider" /><?php endif; ?>
    <div class="sidebar-heading">
        Inventário
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#menuInventario" 
           aria-expanded="false" aria-controls="menuInventario">
            <i class="fas fa-fw fa-undo"></i>
            <span>Inventário</span>
        </a>
        <div id="menuInventario" class="collapse <?php echo ($link[1] ?? '') === 'Inventario' ? 'show' : ''; ?>" aria-labelledby="headingInventario" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if ($podeManipular('Inventario')): ?><a class="collapse-item" href="<?php echo "{$url}!/Inventario/cadastro" ?>">Novo Registro</a><?php endif; ?>
                <a class="collapse-item" href="<?php echo "{$url}!/Inventario/listar" ?>">Devoluções</a>
                <?php if ($ehAdmin || $podeVer('Relatorios')): ?><a class="collapse-item" href="<?php echo "{$url}!/Inventario/relatorio" ?>">Relatório por Período</a><?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>
    <?php if ($podeVer('Estoque') || $podeVer('Inventario')): ?><hr class="sidebar-divider" /><?php endif; ?>

    <!-- Seção: CD - Centro de Distribuição (Admin ou quem tem permissão CD) -->
    <?php if ((isset($_COOKIE['nivel_acesso']) && $_COOKIE['nivel_acesso'] === 'Administrador') || $podeVer('CD') || $podeManipular('CD')): ?>
    <?php if (!$podeVer('Estoque')): ?><hr class="sidebar-divider" /><?php endif; ?>
    <div class="sidebar-heading">
        CD - Centro de Distribuição
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#menuCD" 
           aria-expanded="false" aria-controls="menuCD">
            <i class="fas fa-fw fa-warehouse"></i>
            <span>Centro de Distribuição</span>
        </a>
        <div id="menuCD" class="collapse <?php echo in_array($link[1] ?? '', ['CD']) ? 'show' : ''; ?>" aria-labelledby="headingCD" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/CD/estoque" ?>">Estoque do CD</a>
                <a class="collapse-item" href="<?php echo "{$url}!/CD/transferir" ?>">Transferir para Lojas</a>
                <a class="collapse-item" href="<?php echo "{$url}!/CD/movimentacoes" ?>">Movimentações</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider" />
    <?php endif; ?>

    <?php $temProdutos = $podeVer('Produtos') || $podeVer('Insumos') || $podeVer('GrupoProdutos') || $podeVer('SubGrupoProdutos') || $podeVer('Definicoes') || $podeVer('Cotacoes') || $podeVer('EntradaMercadorias'); if ($temProdutos): ?>
    <hr class="sidebar-divider" />
    <!-- Seção: Produtos e Cadastros -->
    <div class="sidebar-heading">
        Produtos e Cadastros
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#produtosEstoque" 
           aria-expanded="false" aria-controls="produtosEstoque">
            <i class="fas fa-fw fa-box"></i>
            <span>Produtos</span>
        </a>
        <div id="produtosEstoque" class="collapse <?php echo in_array($link[1] ?? '', ['Produtos', 'Insumos', 'GrupoProdutos', 'SubGrupoProdutos', 'Definicoes', 'Cotacoes', 'EntradaMercadorias', 'MovimentacaoEstoque', 'TransferenciaEstoque']) ? 'show' : ''; ?>" aria-labelledby="headingProdutosEstoque" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if ($podeVer('Produtos')): ?><a class="collapse-item" href="<?php echo "{$url}!/Produtos/listar" ?>">Produtos</a><?php endif; ?>
                <?php if ($podeVer('Insumos')): ?><a class="collapse-item" href="<?php echo "{$url}!/Insumos/listar" ?>">Insumos</a><?php endif; ?>
                <?php if ($podeVer('GrupoProdutos')): ?><a class="collapse-item" href="<?php echo "{$url}!/GrupoProdutos/listar" ?>">Grupo de Produtos</a><?php endif; ?>
                <?php if ($podeVer('SubGrupoProdutos')): ?><a class="collapse-item" href="<?php echo "{$url}!/SubGrupoProdutos/listar" ?>">Subgrupo de Produtos</a><?php endif; ?>
                <?php if ($podeVer('Definicoes')): ?><a class="collapse-item" href="<?php echo "{$url}!/Definicoes/listar" ?>">Definições</a><?php endif; ?>
                <?php if ($podeVer('Cotacoes')): ?><a class="collapse-item" href="<?php echo "{$url}!/Cotacoes/listar" ?>">Cotações</a><?php endif; ?>
                <?php if ($podeVer('EntradaMercadorias')): ?><a class="collapse-item" href="<?php echo "{$url}!/EntradaMercadorias/listar" ?>">Entrada de Mercadorias</a><?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php $temFinanceiro = $podeVer('Caixa') || $podeVer('Contas') || $podeVer('CategoriaDespesa') || $podeVer('Cartoes') || $podeVer('Cheques') || $podeVer('MaterialPagamento') || $podeVer('ComissaoVendedor'); if ($temFinanceiro): ?>
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
        <div id="financeiro" class="collapse <?php echo in_array($link[1] ?? '', ['Contas', 'CategoriaDespesa', 'Cartoes', 'Cheques', 'MaterialPagamento', 'ComissaoVendedor', 'Caixa']) ? 'show' : ''; ?>" aria-labelledby="headingFinanceiro" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if ($podeVer('Caixa')): ?><a class="collapse-item" href="<?php echo "{$url}!/Caixa/lista" ?>">Fluxo de Caixa</a><?php endif; ?>
                <?php if ($podeVer('Contas')): ?>
                <a class="collapse-item" href="<?php echo "{$url}!/Contas/listar/P" ?>">Contas a Pagar</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Contas/listar/R" ?>">Contas a Receber</a>
                <?php endif; ?>
                <?php if ($podeVer('CategoriaDespesa')): ?><a class="collapse-item" href="<?php echo "{$url}!/CategoriaDespesa/listar" ?>">Categoria de Despesa</a><?php endif; ?>
                <?php if ($podeVer('Cartoes')): ?><a class="collapse-item" href="<?php echo "{$url}!/Cartoes/listar" ?>">Cartões</a><?php endif; ?>
                <?php if ($podeVer('Cheques')): ?><a class="collapse-item" href="<?php echo "{$url}!/Cheques/listar" ?>">Cheques</a><?php endif; ?>
                <?php if ($podeVer('MaterialPagamento')): ?><a class="collapse-item" href="<?php echo "{$url}!/MaterialPagamento/listar" ?>">Materiais (Pagamento)</a><?php endif; ?>
                <?php if ($podeManipular('ComissaoVendedor')): ?><a class="collapse-item" href="<?php echo "{$url}!/ComissaoVendedor/cadastro" ?>">Comissão</a><?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php $temCadastros = $podeVer('Clientes') || $podeVer('GrupoClientes') || $podeVer('Fornecedores') || $podeVer('Usuarios') || $podeVer('Cargos') || $podeVer('Feriados') || $podeVer('Material') || $podeVer('Categoria'); if ($temCadastros): ?>
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
        <div id="cadastros" class="collapse <?php echo in_array($link[1] ?? '', ['Clientes', 'GrupoClientes', 'Fornecedores', 'Usuarios', 'Cargos', 'Feriados', 'Material', 'Categoria']) ? 'show' : ''; ?>" aria-labelledby="headingCadastros" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if ($podeVer('Clientes')): ?><a class="collapse-item" href="<?php echo "{$url}!/Clientes/listar" ?>">Clientes</a><?php endif; ?>
                <?php if ($podeVer('GrupoClientes')): ?><a class="collapse-item" href="<?php echo "{$url}!/GrupoClientes/listar" ?>">Grupo de Clientes</a><?php endif; ?>
                <?php if ($podeVer('Fornecedores')): ?><a class="collapse-item" href="<?php echo "{$url}!/Fornecedores/listar" ?>">Fornecedores</a><?php endif; ?>
                <?php if ($podeVer('Usuarios')): ?><a class="collapse-item" href="<?php echo "{$url}!/Usuarios/listar" ?>">Usuários</a><?php endif; ?>
                <?php if ($podeVer('Cargos')): ?><a class="collapse-item" href="<?php echo "{$url}!/Cargos/listar" ?>">Cargos</a><?php endif; ?>
                <?php if ($podeVer('Feriados')): ?><a class="collapse-item" href="<?php echo "{$url}!/Feriados/listar" ?>">Feriados</a><?php endif; ?>
                <?php if ($podeVer('Material')): ?><a class="collapse-item" href="<?php echo "{$url}!/Material/listar" ?>">Materiais</a><?php endif; ?>
                <?php if ($podeVer('Categoria')): ?><a class="collapse-item" href="<?php echo "{$url}!/Categoria/listar" ?>">Categorias</a><?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <?php if ($podeVer('Loja')): ?>
    <hr class="sidebar-divider" />
    <!-- Seção: Loja -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo "{$url}!/Loja/listar" ?>">
            <i class="fas fa-fw fa-store"></i>
            <span>Lojas</span>  
        </a>
    </li>
    <?php endif; ?>

    <?php $temRelatoriosFabrica = $podeVer('Relatorios') || $podeVer('Fabrica'); if ($temRelatoriosFabrica): ?>
    <hr class="sidebar-divider" />
    <!-- Seção: Relatórios e Fábrica -->
    <div class="sidebar-heading">
        Relatórios & Fábrica
    </div>
    <?php if ($podeVer('Relatorios')): ?>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#relatorios" 
           aria-expanded="false" aria-controls="relatorios">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Relatórios</span>
        </a>
        <div id="relatorios" class="collapse <?php echo ($link[1] ?? '') == 'Relatorios' ? 'show' : ''; ?>" aria-labelledby="headingRelatorios" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Relatorios/vendas" ?>">Vendas</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Relatorios/financeiros" ?>">Financeiros</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Relatorios/estoque" ?>">Estoque</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Relatorios/consignacao" ?>">Consignação</a>
            </div>
        </div>
    </li>
    <?php endif; ?>
    <?php if ($podeVer('Fabrica')): ?>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#fabrica" 
           aria-expanded="false" aria-controls="fabrica">
            <i class="fas fa-fw fa-industry"></i>
            <span>Fábrica</span>
        </a>
        <div id="fabrica" class="collapse <?php echo ($link[1] ?? '') == 'Fabrica' ? 'show' : ''; ?>" aria-labelledby="headingFabrica" data-bs-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo "{$url}!/Fabrica/aberto" ?>">Aberto</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Fabrica/andamento" ?>">Andamento</a>
                <a class="collapse-item" href="<?php echo "{$url}!/Fabrica/finalizado" ?>">Finalizado</a>
            </div>
        </div>
    </li>
    <?php endif; ?>
    <?php endif; ?>
    <hr class="sidebar-divider d-none d-md-block" />

    <?php
    $certStatus = verificarCertificadoDigital();
    if ($certStatus['status'] === 'vencido' || $certStatus['status'] === 'proximo_vencimento' || $certStatus['status'] === 'ausente' || $certStatus['status'] === 'erro'):
        $corAlerta = ($certStatus['status'] === 'vencido' || $certStatus['status'] === 'ausente') ? 'danger' : 'warning';
        $iconeAlerta = ($certStatus['status'] === 'vencido' || $certStatus['status'] === 'ausente') ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
    ?>
    <li class="nav-item">
        <a class="nav-link text-<?= $corAlerta ?>" href="#" data-bs-toggle="modal" data-bs-target="#modalCertificado" style="font-size: 0.75rem; padding: 0.5rem 1rem;">
            <i class="fas <?= $iconeAlerta ?> fa-fw fa-beat"></i>
            <span><?= $certStatus['mensagem'] ?></span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Seção: Certificado Digital -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalCertificado">
            <i class="fas fa-fw fa-certificate"></i>
            <span>Certificado Digital</span>
        </a>
    </li>

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



