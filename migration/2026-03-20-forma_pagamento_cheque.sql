-- Tabela forma_pagamento_cheque (similar a cartoes)
-- Configuração de parcelas e juros para pagamento por cheque

CREATE TABLE IF NOT EXISTS `forma_pagamento_cheque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_cheque` varchar(200) NOT NULL,
  `max_parcelas` int(11) DEFAULT NULL,
  `juros_parcela_1` decimal(5,2) DEFAULT NULL,
  `juros_parcela_2` decimal(5,2) DEFAULT NULL,
  `juros_parcela_3` decimal(5,2) DEFAULT NULL,
  `juros_parcela_4` decimal(5,2) DEFAULT NULL,
  `juros_parcela_5` decimal(5,2) DEFAULT NULL,
  `juros_parcela_6` decimal(5,2) DEFAULT NULL,
  `juros_parcela_7` decimal(5,2) DEFAULT NULL,
  `juros_parcela_8` decimal(5,2) DEFAULT NULL,
  `juros_parcela_9` decimal(5,2) DEFAULT NULL,
  `juros_parcela_10` decimal(5,2) DEFAULT NULL,
  `juros_parcela_11` decimal(5,2) DEFAULT NULL,
  `juros_parcela_12` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
