# Gerando Nota Fiscal de Consumidor Eletrônica (NFC-e) em PHP com Código de Segurança

Para gerar uma NFC-e em PHP utilizando apenas o código de segurança do contribuinte (CSRT), você precisará seguir alguns passos essenciais. Vou fornecer um guia básico e um exemplo simplificado.

## Pré-requisitos

1. Credenciais de acesso à SEFAZ do seu estado
2. Certificado digital (embora você mencionou apenas o CSRT, normalmente o certificado é necessário)
3. CSRT (Código de Segurança do Responsável Técnico) fornecido pela SEFAZ

## Exemplo Básico


```php
<?php
class NfceGenerator {
    private $csrt;
    private $idCSC; // Código do CSC (normalmente 1)
  
    public function __construct($csrt, $idCSC = 1) {
        $this->csrt = $csrt;
        $this->idCSC = $idCSC;
    }
  
    /**
     * Gera o QR Code para NFC-e
     */
    public function gerarQrCode($dadosNfce) {
        // Monta a URL base conforme o estado
        $url = "http://www.sefaz.{UF}.gov.br/nfce/qrcode?";
      
        // Parâmetros obrigatórios
        $params = [
            'p' => $this->montarParametrosP($dadosNfce),
            'c' => $this->gerarHashCSRT($dadosNfce)
        ];
      
        return $url . http_build_query($params);
    }
  
    /**
     * Monta o parâmetro 'p' do QR Code
     */
    private function montarParametrosP($dados) {
        // Formato: versao|chave|ambiente|cnpj|operacao|modelo|serie|numero|
        // tpEmis|data|valor|digito|icms|codigo|fonte
        $paramP = implode('|', [
            $dados['versao'],
            $dados['chave'],
            $dados['ambiente'],
            $dados['cnpj'],
            $dados['operacao'],
            $dados['modelo'],
            $dados['serie'],
            $dados['numero'],
            $dados['tpEmis'],
            $dados['data'],
            $dados['valor'],
            $dados['digito'],
            $dados['icms'],
            $dados['codigo'],
            $dados['fonte']
        ]);
      
        return $paramP;
    }
  
    /**
     * Gera o hash CSRT para o parâmetro 'c'
     */
    private function gerarHashCSRT($dados) {
        $paramP = $this->montarParametrosP($dados);
        $hashInput = $paramP . $this->csrt;
      
        // Gera o hash SHA1 e converte para base64
        $hash = sha1($hashInput, true);
        return base64_encode($hash);
    }
  
    /**
     * Gera o XML da NFC-e (simplificado)
     */
    public function gerarXmlNfce($dados) {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<enviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
    <idLote>{$dados['idLote']}</idLote>
    <indSinc>1</indSinc>
    <NFe xmlns="http://www.portalfiscal.inf.br/nfe">
        <infNFe Id="NFe{$dados['chave']}" versao="4.00">
            <ide>
                <cUF>{$dados['cUF']}</cUF>
                <natOp>{$dados['natOp']}</natOp>
                <mod>65</mod>
                <serie>{$dados['serie']}</serie>
                <nNF>{$dados['nNF']}</nNF>
                <dhEmi>{$dados['dhEmi']}</dhEmi>
                <tpNF>1</tpNF>
                <idDest>1</idDest>
                <cMunFG>{$dados['cMunFG']}</cMunFG>
                <tpImp>4</tpImp>
                <tpEmis>1</tpEmis>
                <cDV>{$dados['cDV']}</cDV>
                <tpAmb>{$dados['tpAmb']}</tpAmb>
                <finNFe>1</finNFe>
                <indFinal>1</indFinal>
                <indPres>1</indPres>
                <procEmi>0</procEmi>
                <verProc>1.0</verProc>
            </ide>
            <!-- Continua com emitente, destinatário, produtos, etc -->
        </infNFe>
    </NFe>
</enviNFe>
XML;
      
        return $xml;
    }
}

// Exemplo de uso
$csrt = 'SEU_CODIGO_CSRT_AQUI'; // Código de segurança fornecido pela SEFAZ
$nfce = new NfceGenerator($csrt);

$dadosExemplo = [
    'versao' => '4.00',
    'chave' => '41210601234567890123550010000012341000012345',
    'ambiente' => '2',
    'cnpj' => '01234567890123',
    'operacao' => '1',
    'modelo' => '65',
    'serie' => '1',
    'numero' => '123',
    'tpEmis' => '1',
    'data' => '20230810',
    'valor' => '100.00',
    'digito' => '5',
    'icms' => '0',
    'codigo' => '123456',
    'fonte' => '0',
    // Dados para XML
    'idLote' => '123',
    'cUF' => '41',
    'natOp' => 'VENDA',
    'nNF' => '123',
    'dhEmi' => date('c'),
    'cMunFG' => '4106902',
    'cDV' => '5',
    'tpAmb' => '2'
];

$qrCode = $nfce->gerarQrCode($dadosExemplo);
$xml = $nfce->gerarXmlNfce($dadosExemplo);

echo "QR Code: " . $qrCode . "\n";
echo "XML: " . $xml . "\n";
?>
```

## Observações Importantes

1. **Integração com SEFAZ** : Este é um exemplo simplificado. Para produção, você precisará integrar com o webservice da SEFAZ do seu estado.
2. **Bibliotecas Recomendadas** : Considere usar bibliotecas como:

* [NFePHP](https://github.com/nfephp-org/sped-nfe) para uma implementação mais completa
* [PHPQRCode](http://phpqrcode.sourceforge.net/) para gerar imagens de QR Code

1. **Validação** : O XML gerado deve ser validado contra o schema XSD da NFC-e.
2. **Estado Específico** : Cada estado pode ter particularidades na geração da NFC-e. Consulte a documentação oficial da SEFAZ do seu estado.
3. **Ambiente de Homologação** : Sempre teste primeiro no ambiente de homologação antes de ir para produção.

Lembre-se que a geração de documentos fiscais eletrônicos é uma atividade complexa e sujeita a legislação específica. Recomendo consultar um contador ou especialista fiscal para garantir a conformidade legal.
