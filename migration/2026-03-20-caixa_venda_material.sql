-- Adicionar VendaMaterial ao enum tipo em caixa_movimentos

ALTER TABLE `caixa_movimentos`
MODIFY COLUMN `tipo` enum(
    'VendaDinheiro',
    'VendaCheque',
    'VendaPix',
    'VendaCartao',
    'VendaMaterial',
    'RecebimentoConta',
    'PagamentoConta',
    'Sangria',
    'Reforco',
    'Ajuste'
) NOT NULL;
