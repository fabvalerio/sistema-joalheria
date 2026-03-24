-- Pagamentos compostos do pedido (1:N)
CREATE TABLE IF NOT EXISTS `pedido_pagamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `forma` varchar(100) NOT NULL COMMENT 'rótulo ex.: Dinheiro, Cartão de Crédito',
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `parcelas` int(11) DEFAULT 1,
  `observacao` varchar(255) DEFAULT NULL,
  `cartao_id` int(11) DEFAULT NULL COMMENT 'referência a cartoes.id (sem FK para compatibilidade)',
  PRIMARY KEY (`id`),
  KEY `idx_pedido_pagamentos_pedido` (`pedido_id`),
  CONSTRAINT `fk_pedido_pagamentos_pedido`
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
