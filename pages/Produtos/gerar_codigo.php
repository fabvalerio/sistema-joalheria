<?php
require '../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_GET['id'])) {
    $idProduto = $_GET['id'];

    // Criar o gerador de código de barras
    $generator = new BarcodeGeneratorPNG();
    $codigoBarras = $generator->getBarcode($idProduto, $generator::TYPE_CODE_128);

    // Definir cabeçalhos para exibir a imagem
    header('Content-Type: image/png');
    echo $codigoBarras;
} else {
    echo "ID do produto não informado.";
}
?>
