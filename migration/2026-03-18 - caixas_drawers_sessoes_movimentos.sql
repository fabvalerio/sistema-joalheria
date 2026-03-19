-- Tabelas para controle operacional de Caixa por loja e por gaveta (caixa #)
-- Inclui abertura/fechamento (sessões) e movimentações (vendas, contas, sangria/reforço)

-- -------------------------------------------------------------------
-- caixa_drawers: configura quantidade de gavetas por loja
-- -------------------------------------------------------------------
CREATE TABLE `caixa_drawers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loja_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `status` enum('Ativo','Inativo') DEFAULT 'Ativo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_caixa_drawers_loja_numero` (`loja_id`, `numero`),
  KEY `idx_caixa_drawers_loja_id` (`loja_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------------
-- caixa_sessoes: abertura/fechamento por gaveta e data
-- -------------------------------------------------------------------
CREATE TABLE `caixa_sessoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loja_id` int(11) NOT NULL,
  `caixa_drawer_id` int(11) NOT NULL,
  `data_caixa` date NOT NULL,
  `troco_abertura` decimal(10,2) DEFAULT 0.00,
  `saldo_fisico_informado` decimal(10,2) DEFAULT NULL,
  `saldo_esperado` decimal(10,2) DEFAULT NULL,
  `diferenca` decimal(10,2) DEFAULT NULL,
  `status` enum('Aberta','Fechada') DEFAULT 'Aberta',
  `operador_id` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_hora_abertura` datetime NOT NULL DEFAULT current_timestamp(),
  `data_hora_fechamento` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_caixa_sessoes_unica_por_data_drawer` (`loja_id`, `caixa_drawer_id`, `data_caixa`),
  KEY `idx_caixa_sessoes_loja_data` (`loja_id`, `data_caixa`),
  KEY `idx_caixa_sessoes_drawer` (`caixa_drawer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------------
-- caixa_movimentos: entradas/saídas por sessão
-- valor pode ser negativo para saídas (ex: sangria)
-- -------------------------------------------------------------------
CREATE TABLE `caixa_movimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caixa_sessao_id` int(11) NOT NULL,
  `loja_id` int(11) NOT NULL,
  `caixa_drawer_id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `tipo` enum(
    'VendaDinheiro',
    'VendaCheque',
    'VendaPix',
    'VendaCartao',
    'RecebimentoConta',
    'PagamentoConta',
    'Sangria',
    'Reforco',
    'Ajuste'
  ) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('Ativo','Revertido') DEFAULT 'Ativo',
  `origem_tipo` enum('Pedido','Conta','Manual') NOT NULL,
  `origem_id` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_hora_reversao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_caixa_movimentos_origem_sessao` (`origem_tipo`, `origem_id`, `caixa_sessao_id`),
  KEY `idx_caixa_movimentos_sessao` (`caixa_sessao_id`),
  KEY `idx_caixa_movimentos_loja_data` (`loja_id`, `data_hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

