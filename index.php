<?php
// Garante que não há saída antes do início da sessão
ob_start();

ini_set('memory_limit', '-1');

// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado
if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    // Redireciona para a página de login se não estiver autenticado
    $url = "https://" . $_SERVER['HTTP_HOST'] . "/sistema-joias/";
    header("Location: " . $url . "login.php");
    exit();
}

// Incluir arquivos necessários APÓS verificar a sessão
include 'db/db.class.php';
include 'App/php/htaccess.php';
include 'App/php/function.php';
include 'App/php/notify.php';

// Controlador e ação padrão
$controller = $_GET['controller'] ?? 'Home';
$action = $_GET['action'] ?? 'index';

// Finaliza o buffer de saída para evitar erros
ob_end_flush();


?>
<?php
// Módulo CD - Centro de Distribuição: apenas Administrador pode acessar
$moduloAtual = $link[1] ?? '';
if ($moduloAtual === 'CD' && ($_COOKIE['nivel_acesso'] ?? '') !== 'Administrador') {
    header("Location: {$url}!/naopermitido");
    exit;
}

if ($_COOKIE['nivel_acesso'] != "Administrador") {

    if (isset($link[2]) && $link[2] != "") {

        // Obtém o JSON de permissões do cookie (ou usa um JSON vazio se não existir)
        $permissoes_json = $_COOKIE['permissoes'] ?? '{}';

        // Primeiro `json_decode()` para remover a barra invertida (\)
        $permissoes_json = json_decode($permissoes_json, true);

        // Segundo `json_decode()` para converter a string JSON em array associativo
        $permissoes = json_decode($permissoes_json, true);

        // Debug: Verifica se o JSON foi realmente convertido para um array
        if (!is_array($permissoes) || empty($permissoes)) {
            echo "NÃO PERMITIDO (Permissões não encontradas).";
            exit();
        }

        // Obtém a URL atual e extrai o nome do módulo e da página (ação)
        $uri = $_SERVER['REQUEST_URI']; // Exemplo: "/!/Cargos/listar"
        $link = explode("/", trim($uri, "/")); // Divide a URL

        // Verifica se há pelo menos 3 partes na URL (para evitar erros)
        if (count($link) < 3) {
            echo "NÃO PERMITIDO (URL inválida).";
            exit();
        }

        $modulo_atual = $link[1]; // O nome do módulo está sempre na posição 1
        $acao = $link[2]; // Ação está sempre na posição 2 (listar, editar, etc.)

        // Estoque: sempre permitido para visualizar (usuário vê estoque da sua loja ou todas se admin)
        if ($modulo_atual === 'Estoque' && in_array($acao, ['listar', 'ver'])) {
            $permitido = "SIM";
        } else if (!isset($permissoes[$modulo_atual])) {
            header("Location: {$url}!/naopermitido");
            exit;
        } else {
            // Obtém as permissões do módulo atual
            $modulo_permissoes = $permissoes[$modulo_atual];
            $visualizar = $modulo_permissoes['visualizar'] ?? false;
            $manipular = $modulo_permissoes['manipular'] ?? false;

            // **Regra de Permissão**:
            if ($manipular) {
                $permitido = "SIM";
            } else {
                $permitido = ($visualizar && in_array($acao, ["listar", "ver"])) ? "SIM" : "NÃO";
            }
        }
        if ($permitido != "SIM") {
            header("Location: {$url}!/naopermitido");
            exit;
        }
    }

}


?>





<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Vortex Comunicação">

    <title>Meu Painel | Joalheria</title>

    <!-- Custom fonts for this template-->
    <link href="<?php echo $url; ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $url; ?>assets/css/sb-admin-2.css" rel="stylesheet">

    <!-- <script src="<?php $url; ?>vendor/jquery/jquery.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link href="<?php echo $url; ?>dist/styles.css" rel="stylesheet">
    <!-- <script src="<?php echo $url; ?>dist/bundle.js"></script> -->


    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $url; ?>assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $url; ?>assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $url; ?>assets/favicon-16x16.png">


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'assets/components/sidebar.php' ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'assets/components/topbar.php' ?>

                <!-- Begin Page Content -->
                <div class="container-fluid h-z">
                    <?php include $paginaExibi ?>
                </div>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'assets/components/footer.php' ?>

        </div>

    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="<?php echo $url; ?>#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="<?php echo $url; ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

    <!-- Core plugin JavaScript-->
    <!-- <script src="<?php echo $url; ?>vendor/jquery-easing/jquery.easing.min.js"></script> -->

    <!-- Custom scripts for all pages-->
    <script src="<?php echo $url; ?>assets/js/sb-admin-2.min.js?v=1"></script>

    <!-- Page level plugins -->
    <?PHP if( empty($link[1]) ){ ?>
    <script src="<?php echo $url; ?>vendor/chart.js/Chart.min.js"></script>
    <?PHP } ?>

    <!-- Page level custom scripts -->
    <!-- <script src="<?php echo $url; ?>assets/js/demo/chart-area-demo.js?v=1"></script> -->
    <!-- <script src="<?php echo $url; ?>assets/js/demo/chart-pie-demo.js?v=1"></script> -->

    <!-- VIACEPN API  -->
    <script src="<?php echo $url; ?>assets/js/viacep.js"></script>


    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.querySelector("form");
        
        // Verifica se o formulário existe na página antes de adicionar o evento
        if (form) {
            form.addEventListener("keypress", function(event) {
                // Verifica se a tecla pressionada é "Enter"
                if (event.key === "Enter") {
                    event.preventDefault(); // Bloqueia o comportamento padrão
                }
            });
        }
    });
</script>


<!-- Modal Certificado Digital -->
<div class="modal fade" id="modalCertificado" tabindex="-1" aria-labelledby="modalCertificadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCertificadoLabel">
                    <i class="fas fa-certificate me-2"></i>Certificado Digital
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="certStatusArea" class="mb-3">
                    <?php
                    $certInfo = $certStatus ?? verificarCertificadoDigital();
                    $badgeClass = match($certInfo['status']) {
                        'valido' => 'success',
                        'proximo_vencimento' => 'warning',
                        'vencido' => 'danger',
                        'ausente' => 'danger',
                        default => 'secondary'
                    };
                    $statusLabel = match($certInfo['status']) {
                        'valido' => 'Válido',
                        'proximo_vencimento' => 'Próximo do vencimento',
                        'vencido' => 'Vencido',
                        'ausente' => 'Não encontrado',
                        default => 'Erro'
                    };
                    ?>
                    <div class="card border-<?= $badgeClass ?>">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Status Atual</h6>
                            <span class="badge bg-<?= $badgeClass ?> fs-6 mb-2"><?= $statusLabel ?></span>
                            <?php if ($certInfo['validade']): ?>
                                <p class="mb-1"><strong>Validade:</strong> <?= $certInfo['validade'] ?></p>
                            <?php endif; ?>
                            <?php if ($certInfo['dias'] !== null): ?>
                                <p class="mb-0 text-<?= $badgeClass ?>">
                                    <?php if ($certInfo['dias'] < 0): ?>
                                        Vencido há <?= abs($certInfo['dias']) ?> dias
                                    <?php else: ?>
                                        <?= $certInfo['dias'] ?> dias restantes
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($_COOKIE['nivel_acesso'] == 'Administrador'): ?>
                <hr>
                <h6 class="mb-3"><i class="fas fa-upload me-1"></i> Enviar novo certificado</h6>
                <form id="formCertificado" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="inputCertificado" class="form-label">Arquivo do certificado (.pfx ou .p12)</label>
                        <input type="file" class="form-control" id="inputCertificado" name="certificado" accept=".pfx,.p12" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputSenhaCert" class="form-label">Senha do certificado</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="inputSenhaCert" name="senha" placeholder="Digite a senha do certificado" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleSenhaCert">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div id="certUploadMsg" class="mb-3" style="display:none;"></div>
                    <button type="submit" class="btn btn-primary w-100" id="btnEnviarCert">
                        <i class="fas fa-upload me-1"></i> Enviar Certificado
                    </button>
                </form>
                <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Entre em contato com o administrador do sistema para atualizar o certificado digital.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('toggleSenhaCert');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var input = document.getElementById('inputSenhaCert');
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }

    var form = document.getElementById('formCertificado');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = document.getElementById('btnEnviarCert');
            var msgDiv = document.getElementById('certUploadMsg');
            var formData = new FormData(this);
            formData.append('action', 'upload');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';
            msgDiv.style.display = 'none';

            fetch('<?= $url ?>App/php/certificado_api.php?action=upload', {
                method: 'POST',
                body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                msgDiv.style.display = 'block';
                if (data.success) {
                    msgDiv.className = 'mb-3 alert alert-success';
                    msgDiv.innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + data.mensagem;
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    msgDiv.className = 'mb-3 alert alert-danger';
                    msgDiv.innerHTML = '<i class="fas fa-times-circle me-1"></i> ' + data.mensagem;
                }
            })
            .catch(function() {
                msgDiv.style.display = 'block';
                msgDiv.className = 'mb-3 alert alert-danger';
                msgDiv.innerHTML = '<i class="fas fa-times-circle me-1"></i> Erro de conexão com o servidor';
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-upload me-1"></i> Enviar Certificado';
            });
        });
    }
});
</script>

</body>

</html>