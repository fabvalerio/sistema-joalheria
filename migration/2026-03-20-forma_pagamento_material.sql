-- Tabela forma_pagamento_material
-- Tipos de material para pagamento com valor por grama

CREATE TABLE IF NOT EXISTS `forma_pagamento_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_material` varchar(200) NOT NULL,
  `valor_por_grama` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
