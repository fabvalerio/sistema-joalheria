<?php
require '../../vendor/autoload.php';

use Dompdf\Dompdf;

if (isset($_GET['pdf'])) {
    ob_start();
}

$xml = simplexml_load_file("pages/Notas/xml/nfc-e-autorizada[99722026].xml");
$ns = $xml->getNamespaces(true);
$xml->registerXPathNamespace('nfe', $ns['']);

$infNFe = $xml->xpath('//nfe:infNFe')[0];
$emit = $infNFe->emit;
$enderEmit = $emit->enderEmit;
$prod = $infNFe->det->prod;
$total = $infNFe->total->ICMSTot;
$pag = $infNFe->pag->detPag;
$qrCode = $xml->NFe->infNFeSupl->qrCode ?? '';
$chave = $xml->infProt->chNFe;
$nProt = $xml->infProt->nProt;
$dhEmi = date('d/m/Y H:i:s', strtotime((string)$infNFe->ide->dhEmi));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>DANFE NFC-e</title>
<style>
  body { font-family: monospace; max-width: 300px; margin: auto; font-size: 12px; }
  .center { text-align: center; }
  hr { border: none; border-top: 1px dashed #000; margin: 5px 0; }
  .qr { text-align: center; margin: 10px 0; }
  .btns { margin: 10px 0; text-align: center; }
  .btns a { padding: 5px 10px; background: #333; color: white; text-decoration: none; margin: 5px; display: inline-block; border-radius: 5px; }
</style>
</head>
<body>

<?php if (!isset($_GET['pdf'])): ?>
<div class="btns">
  <a href="#" onclick="window.print()">🖨️ Imprimir</a>
  <a href="?pdf=1" target="_blank">📄 Gerar PDF</a>
</div>
<?php endif; ?>

<div class="center">
  <strong><?= $emit->xFant ?></strong><br>
  <?= $enderEmit->xLgr . ", " . $enderEmit->nro . " - " . $enderEmit->xCpl ?><br>
  <?= $enderEmit->xBairro . " - " . $enderEmit->xMun ?>/<?= $enderEmit->UF ?> - CEP: <?= $enderEmit->CEP ?><br>
  CNPJ: <?= $emit->CNPJ ?> - IE: <?= $emit->IE ?><br>
</div>

<hr>
<div class="center">
  NFC-e - Nota Fiscal de Consumidor Eletrônica<br>
  Série <?= $infNFe->ide->serie ?> - Nº <?= $infNFe->ide->nNF ?><br>
  Data: <?= $dhEmi ?><br>
</div>

<hr>
CPF/CNPJ do Consumidor: NÃO IDENTIFICADO<br>
<hr>
COD&nbsp;&nbsp;&nbsp;&nbsp;DESC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;VL ITEM R$<br>
<?= $prod->cProd ?>&nbsp;&nbsp;&nbsp;&nbsp;<?= substr($prod->xProd, 0, 30) ?>&nbsp;&nbsp;&nbsp;&nbsp;<?= number_format((float)$prod->vProd, 2, ',', '.') ?><br>
<hr>
TOTAL R$: <?= number_format((float)$total->vNF, 2, ',', '.') ?><br>
<?= $pag->tPag == '01' ? 'Dinheiro' : 'Cartão de Débito' ?>: <?= number_format((float)$pag->vPag, 2, ',', '.') ?><br>
<hr>
ICMS: Regime Simples Nacional - CSOSN 102<br>
Valor dos Tributos Aproximado: R$ 0,00<br>
<hr>
Forma de Pagamento: <?= $pag->tPag == '01' ? 'Dinheiro' : 'Cartão de Débito' ?><br>
Troco: R$ 0,00<br>
<hr>
Protocolo de Autorização:<br>
<?= $nProt ?><br>
<?= $chave ?><br>
<div class="qr">
  <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($qrCode) ?>&size=150x150"><br>
</div>
<div class="center">
  Consulte a validade em:<br>
  <a href="https://www.nfce.fazenda.sp.gov.br" target="_blank">nfce.fazenda.sp.gov.br</a>
</div>

</body>
</html>

<?php
// Se for para PDF, gera o arquivo com DOMPDF
if (isset($_GET['pdf'])) {
    $html = ob_get_clean();
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper([0, 0, 226.77, 1000]); // Tamanho bobina 80mm em pontos
    $dompdf->render();
    $dompdf->stream("danfe_nfce_99722026.pdf", ["Attachment" => false]);
    exit;
}
?>
