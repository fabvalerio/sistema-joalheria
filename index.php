
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include 'db/db.class.php';
include 'App/php/htaccess.php';
include 'App/php/function.php';
include 'App/php/notify.php';

$controller = $_GET['controller'] ?? 'Home';
$action = $_GET['action'] ?? 'index';


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
    <link href="<?php echo $url;?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $url;?>assets/css/sb-admin-2.css" rel="stylesheet">

    <!-- <script src="<?php $url; ?>vendor/jquery/jquery.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link href="<?php echo $url;?>dist/styles.css" rel="stylesheet">
    <script src="<?php echo $url;?>dist/bundle.js"></script>

    
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $url;?>assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $url;?>assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $url;?>assets/favicon-16x16.png">
    <link rel="manifest" href="<?php echo $url;?>assets/site.webmanifest">


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'assets/components/sidebar.php'?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'assets/components/topbar.php'?>

                <!-- Begin Page Content -->
                <div class="container-fluid h-z">
                    <?php include $paginaExibi?>
                </div>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'assets/components/footer.php'?>

        </div>

    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="<?php echo $url;?>#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="<?php $url; ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

    <!-- Core plugin JavaScript-->
    <script src="<?php $url; ?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php $url; ?>assets/js/sb-admin-2.min.js?v=1"></script>

    <!-- Page level plugins -->
    <script src="<?php $url; ?>vendor/chart.js/Chart.min.js?v=1"></script>

    <!-- Page level custom scripts -->
    <script src="<?php $url; ?>assets/js/demo/chart-area-demo.js?v=1"></script>
    <script src="<?php $url; ?>assets/js/demo/chart-pie-demo.js?v=1"></script>

    <!-- VIACEPN API  -->
    <script src="<?php echo $url;?>assets/js/viacep.js"></script>


    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("keypress", function (event) {
        // Verifica se a tecla pressionada é "Enter"
        if (event.key === "Enter") {
            event.preventDefault(); // Bloqueia o comportamento padrão
        }
    });
});
</script>


</body>

</html>