-- Tabela de devoluções do inventário
-- Registra produtos devolvidos (defeito, cliente não gostou, etc.) e incrementa o estoque
-- NOTA: loja_id não tem FK pois a tabela loja usa MyISAM (InnoDB não suporta FK para MyISAM)

CREATE TABLE IF NOT EXISTS inventario_devolucoes (
  id INT NOT NULL AUTO_INCREMENT,
  produto_id INT NOT NULL,
  usuario_responsavel_id INT NOT NULL,
  data_devolucao DATE NOT NULL,
  hora_devolucao TIME NOT NULL,
  pedido_id INT NULL DEFAULT NULL,
  motivo VARCHAR(255) NOT NULL,
  loja_id INT NOT NULL COMMENT 'CD ou Loja - qual estoque recebeu',
  quantidade DECIMAL(10,2) NOT NULL,
  tipo ENUM('Entrada', 'Saida') NOT NULL DEFAULT 'Entrada' COMMENT 'Entrada = produto retorna ao estoque; Saida = produto sai do estoque',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_data (data_devolucao),
  INDEX idx_produto (produto_id),
  INDEX idx_usuario (usuario_responsavel_id),
  INDEX idx_loja (loja_id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (usuario_responsavel_id) REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
