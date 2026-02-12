# Configuração da Impressora Elgin L42Pro

## 📋 Especificações das Etiquetas

### Dimensões da Etiqueta:
- **Largura:** 40mm (4cm)
- **Altura:** 25mm (2.5cm)
- **Layout:** Vertical com texto no topo e código de barras embaixo

### Estrutura da Etiqueta:
```
┌─────────────────────┐
│                     │
│  Confec. Peças Ouro │ ← Texto do produto (8pt, negrito)
│     00.10 A 00.10G  │ ← Código do produto (7pt)
│                     │
│   ║║║║║║║║║║║║║     │ ← Código de barras EAN-13
│   1234567890123     │
│                     │
└─────────────────────┘
```

## ⚙️ Configurações da Impressora

### No Windows:

1. **Abrir Configurações da Impressora:**
   - Painel de Controle → Dispositivos e Impressoras
   - Botão direito na impressora Elgin L42Pro → Preferências de impressão

2. **Configurar Tamanho do Papel:**
   - **Tamanho:** Personalizado
   - **Largura:** 40mm
   - **Altura:** 25mm
   - **Orientação:** Retrato

3. **Configurar Margens:**
   - **Todas as margens:** 0mm
   - Desmarcar "Ajustar ao tamanho da página"

4. **Qualidade de Impressão:**
   - **Densidade:** Média-Alta (para melhor legibilidade do código de barras)
   - **Velocidade:** Média (evita borrões)

### No Navegador (ao imprimir):

1. **Abrir Diálogo de Impressão** (Ctrl+P)

2. **Selecionar Impressora:**
   - Escolher "Elgin L42Pro"

3. **Configurações:**
   - **Margens:** Nenhuma
   - **Escala:** 100% (sem ajuste)
   - **Páginas por folha:** 1
   - **Orientação:** Retrato

4. **Desmarcar:**
   - ❌ Cabeçalhos e rodapés
   - ❌ Gráficos de fundo

## 🔧 Configuração do Driver Elgin

### Software Elgin Utility:

1. **Instalar o driver mais recente:**
   - Baixar de: https://elgin.com.br/suporte
   - Modelo: L42Pro

2. **Configurar via Elgin Utility:**
   ```
   Tamanho da etiqueta: 40mm x 25mm
   Velocidade: 4 (padrão)
   Densidade: 8 (média)
   Modo de impressão: Térmico direto
   Sensor: Gap (espaço entre etiquetas)
   ```

3. **Calibração:**
   - Executar calibração automática após trocar o rolo de etiquetas
   - Menu → Calibrar sensor → Iniciar

## 📝 Configurações no Código

O arquivo já está otimizado com:

### CSS (@page):
```css
@page {
    size: 40mm 25mm;
    margin: 0;
}
```

### Código de Barras:
```javascript
JsBarcode("#barcode", "1234567890123", {
    format: "EAN13",
    width: 1.5,      // Espessura das barras
    height: 35,      // Altura do código
    displayValue: true,
    fontSize: 10,
    margin: 0
});
```

## 🎯 Solução de Problemas

### Etiqueta saindo cortada:
- ✅ Verificar se o tamanho do papel está correto (40x25mm)
- ✅ Confirmar margens zeradas
- ✅ Calibrar a impressora

### Código de barras não lê:
- ✅ Aumentar densidade de impressão
- ✅ Reduzir velocidade de impressão
- ✅ Verificar qualidade das etiquetas

### Texto muito pequeno:
- ✅ Ajustar `font-size` no CSS
- ✅ Aumentar `fontSize` no JsBarcode

### Espaçamento incorreto entre etiquetas:
- ✅ Calibrar sensor de gap
- ✅ Verificar tipo de sensor (gap ou marca preta)

## 📞 Suporte Técnico Elgin

- **Site:** https://elgin.com.br
- **Suporte:** suporte@elgin.com.br
- **Telefone:** 0800 940 0009

## ✅ Checklist Final

Antes de imprimir, verificar:

- [ ] Impressora Elgin L42Pro selecionada
- [ ] Tamanho do papel: 40mm x 25mm
- [ ] Margens: 0mm em todos os lados
- [ ] Escala: 100%
- [ ] Cabeçalhos/rodapés desativados
- [ ] Impressora calibrada
- [ ] Etiquetas corretas no rolo

## 🚀 Teste de Impressão

1. Selecione apenas 1 produto para teste
2. Visualize antes de imprimir
3. Ajuste densidade se necessário
4. Teste o código de barras com leitor
5. Se ok, imprima o lote completo
