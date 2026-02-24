<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
    <i class="fa fa-bars"></i>
</button>

<div class="lead ms-4">
    Seja bem-vindo <?php echo $_COOKIE['nome']; ?>
</div>

<?php if (!empty($_COOKIE['loja_nome'])): ?>
<div class="ms-auto me-4">
    <span class="badge bg-primary fs-6">
        <i class="fas fa-store"></i> <?= htmlspecialchars($_COOKIE['loja_nome']) ?>
    </span>
</div>
<?php endif; ?>

</nav>