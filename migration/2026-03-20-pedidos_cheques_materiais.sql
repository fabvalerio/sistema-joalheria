-- Tabelas auxiliares para pedidos: cheques e materiais

-- pedidos_cheques: números de cheque por parcela
CREATE TABLE IF NOT EXISTS `pedidos_cheques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `parcela_numero` int(11) NOT NULL,
  `numero_cheque` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedidos_cheques_pedido` (`pedido_id`),
  CONSTRAINT `fk_pedidos_cheques_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- pedidos_materiais: itens de material por pedido
CREATE TABLE IF NOT EXISTS `pedidos_materiais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `forma_pagamento_material_id` int(11) NOT NULL,
  `gramas` decimal(10,3) NOT NULL DEFAULT 0.000,
  `valor_calculado` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_pedidos_materiais_pedido` (`pedido_id`),
  KEY `idx_pedidos_materiais_material` (`forma_pagamento_material_id`),
  CONSTRAINT `fk_pedidos_materiais_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_materiais_material` FOREIGN KEY (`forma_pagamento_material_id`) REFERENCES `forma_pagamento_material` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
