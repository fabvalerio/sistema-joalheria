<?php
// Dados do certificado digital (caminho e senha)
$certificado_pfx = 'certificado.pfx';
$senha_certificado = '351aaf34-bff5-438e-b964-cece8da1987d';

// Configurações da SEFAZ-SP (Homologação ou Produção)
$ambiente = 'homologacao'; // Ou 'producao'
$url = ($ambiente == 'homologacao') 
    ? 'https://homologacao.nfce.fazenda.sp.gov.br/NFCeWS/WS/NfeAutorizacao.asmx' 
    : 'https://nfce.fazenda.sp.gov.br/NFCeWS/WS/NfeAutorizacao.asmx';

// Dados da NFC-e (XML mínimo exemplo)
$xml_nfce = '
<enviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
    <idLote>1</idLote>
    <NFe xmlns="http://www.portalfiscal.inf.br/nfe">
        <infNFe Id="NFe123456789" versao="4.00">
            <ide>
                <cUF>35</cUF> <!-- Código de SP -->
                <natOp>Venda de produto</natOp>
                <mod>65</mod> <!-- Modelo NFC-e -->
                <serie>1</serie>
                <nNF>123456</nNF>
                <dhEmis>' . date('Y-m-d\TH:i:sP') . '</dhEmis>
                <tpNF>1</tpNF> <!-- 1=Saída -->
                <idDest>1</idDest> <!-- 1=Consumidor Final -->
                <tpAmb>2</tpAmb> <!-- 2=Homologação -->
                <tpEmis>1</tpEmis> <!-- 1=Normal -->
                <cDV>1</cDV>
                <finNFe>1</finNFe> <!-- 1=Normal -->
                <indFinal>1</indFinal> <!-- 1=Operação final -->
                <indPres>1</indPres> <!-- 1=Presencial -->
            </ide>
            <emit>
                <CNPJ>12345678000199</CNPJ>
                <xNome>Empresa Teste Ltda</xNome>
                <xFant>Loja Teste</xFant>
                <enderEmit>
                    <xLgr>Rua Teste</xLgr>
                    <nro>123</nro>
                    <xBairro>Centro</xBairro>
                    <cMun>3550308</cMun> <!-- Código da cidade (Ex: São Paulo) -->
                    <xMun>SÃO PAULO</xMun>
                    <UF>SP</UF>
                    <CEP>01001000</CEP>
                </enderEmit>
                <IE>123456789</IE>
                <IM>12345</IM>
                <CNAE>6201500</CNAE>
                <CRT>1</CRT> <!-- 1=Simples Nacional -->
            </emit>
            <dest>
                <CNPJ>99999999000199</CNPJ> <!-- Ou CPF para pessoa física -->
                <xNome>Cliente Teste</xNome>
                <enderDest>
                    <xLgr>Rua Cliente</xLgr>
                    <nro>456</nro>
                    <xBairro>Centro</xBairro>
                    <cMun>3550308</cMun>
                    <xMun>SÃO PAULO</xMun>
                    <UF>SP</UF>
                </enderDest>
                <indIEDest>9</indIEDest> <!-- 9=Não contribuinte -->
            </dest>
            <det nItem="1">
                <prod>
                    <cProd>001</cProd>
                    <xProd>Produto Teste</xProd>
                    <NCM>99999999</NCM>
                    <CFOP>5102</CFOP>
                    <uCom>UN</uCom>
                    <qCom>1.0000</qCom>
                    <vUnCom>100.00</vUnCom>
                    <vProd>100.00</vProd>
                </prod>
                <imposto>
                    <ICMS>
                        <ICMS00>
                            <orig>0</orig>
                            <CST>00</CST>
                            <modBC>3</modBC>
                            <vBC>100.00</vBC>
                            <pICMS>18.00</pICMS>
                            <vICMS>18.00</vICMS>
                        </ICMS00>
                    </ICMS>
                    <PIS>
                        <PISNT>
                            <CST>07</CST>
                        </PISNT>
                    </PIS>
                    <COFINS>
                        <COFINSNT>
                            <CST>07</CST>
                        </COFINSNT>
                    </COFINS>
                </imposto>
            </det>
            <total>
                <ICMSTot>
                    <vBC>100.00</vBC>
                    <vICMS>18.00</vICMS>
                    <vNF>100.00</vNF>
                    <vTotTrib>18.00</vTotTrib>
                </ICMSTot>
            </total>
            <transp>
                <modFrete>9</modFrete> <!-- 9=Sem frete -->
            </transp>
            <pag>
                <detPag>
                    <tPag>01</tPag> <!-- 01=Dinheiro -->
                    <vPag>100.00</vPag>
                </detPag>
            </pag>
            <infAdic>
                <infCpl>NFC-e teste, sem valor fiscal.</infCpl>
            </infAdic>
        </infNFe>
    </NFe>
</enviNFe>';

// Assinar o XML (requer OpenSSL e biblioteca como "NFePHP" ou "CertificateUtility")
// Aqui é um exemplo simplificado (na prática, use uma biblioteca específica)
$xml_assinado = assinarXML($xml_nfce, $certificado_pfx, $senha_certificado);

// Enviar para a SEFAZ-SP via SOAP
$client = new SoapClient($url, [
    'local_cert' => $certificado_pfx,
    'passphrase' => $senha_certificado,
    'stream_context' => stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ])
]);

try {
    $response = $client->nfeAutorizacaoLote([
        'nfeDadosMsg' => $xml_assinado
    ]);
    echo "<pre>Resposta SEFAZ-SP:\n" . print_r($response, true) . "</pre>";
} catch (Exception $e) {
    echo "Erro na comunicação com SEFAZ-SP: " . $e->getMessage();
}

// Função simplificada para assinar XML (apenas exemplo)
function assinarXML($xml, $cert_pfx, $senha) {
    // Na prática, use uma biblioteca como "NFePHP" ou "CertificateUtility"
    // Este é apenas um placeholder para demonstração
    openssl_pkcs12_read(file_get_contents($cert_pfx), $certs, $senha);
    $privateKey = openssl_pkey_get_private($certs['pkey']);
    openssl_sign($xml, $signature, $privateKey, OPENSSL_ALGO_SHA1);
    return $xml; // (Não assina de verdade neste exemplo)
}
?>