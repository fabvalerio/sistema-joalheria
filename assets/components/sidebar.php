<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo $url?>">
    <div class="sidebar-brand-text mx-3">
        <img src="<?php echo $url ?>assets/logo.png" alt="Joalheria" style="width: 100px;">
    </div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item active">
    <a class="nav-link" href="<?php echo $url?>">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Meu Painel</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">


<div class="sidebar-heading">
    Interface
</div>

 <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#configuracao" aria-expanded="false" aria-controls="configuracao">
        <i class="fas fa-fw fa-cog"></i>
        <span>Configuração</span>
    </a>
    <div id="configuracao" class="collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="<?php echo "{$url}!/Bancos/listar"?>">Bancos</a>
            <a class="collapse-item" href="<?php echo "{$url}!/MetasAgentes/listar"?>">Metas de Agentes</a>
            <a class="collapse-item" href="<?php echo "{$url}!/ParametrosSistema/listar"?>">Parametros</a>
        </div>
    </div>
</li> 


<li class="nav-item">
    <a class="nav-link" href="<?php echo "{$url}!/Empresas/listar"?>">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Usuários</span></a>
</li>


<!-- Nav Item - Tables -->
<li class="nav-item">
    <a class="nav-link" href="<?php echo "{$url}sair.php"?>">
        <i class="fas fa-fw fa-table"></i>
        <span>Sair</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>