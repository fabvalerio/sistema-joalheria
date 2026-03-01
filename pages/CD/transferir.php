<?php

use App\Models\TransferenciaEstoque\Controller;

$controller = new Controller();
$lojas = $controller->listarLojas();

// Separa CD e Lojas para orientar o admin
$cds = array_filter($lojas, fn($l) => ($l['tipo'] ?? '') === 'CD');
$lojasOnly = array_filter($lojas, fn($l) => ($l['tipo'] ?? '') !== 'CD');
?>

<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-truck-loading me-2"></i>Transferir do CD para Lojas
        </h3>
        <a href="<?= $url ?>!/CD/estoque" class="btn btn-outline-light btn-sm">
            <i class="fas fa-warehouse me-1"></i>Ver Estoque do CD
        </a>
    </div>
    <div class="card-body">
        <p class="text-muted">
            Utilize a tela de <strong>Transferência de Estoque</strong> para enviar produtos do Centro de Distribuição (CD) para as lojas, 
            ou entre uma loja e outra.
        </p>
        <a href="<?= $url ?>!/TransferenciaEstoque/cadastro" class="btn btn-primary btn-lg">
            <i class="fas fa-exchange-alt me-2"></i>Abrir Transferência de Estoque
        </a>
        <hr>
        <h6>Lojas disponíveis para transferência:</h6>
        <ul class="list-group">
            <?php foreach ($lojas as $l): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($l['nome']) ?>
                    <span class="badge bg-<?= ($l['tipo'] ?? '') === 'CD' ? 'secondary' : 'primary' ?>"><?= $l['tipo'] ?? 'Loja' ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
