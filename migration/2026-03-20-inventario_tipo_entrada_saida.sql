-- Adiciona coluna tipo (Entrada/Saida) na tabela inventario_devolucoes
-- Execute se a tabela já existir. Registros antigos recebem 'Entrada' como padrão.
-- Seguro para executar várias vezes: só adiciona se a coluna não existir.

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'inventario_devolucoes'
  AND COLUMN_NAME = 'tipo');

SET @sql = IF(@col_exists = 0,
  'ALTER TABLE inventario_devolucoes ADD COLUMN tipo ENUM(''Entrada'', ''Saida'') NOT NULL DEFAULT ''Entrada'' COMMENT ''Entrada = produto retorna ao estoque; Saida = produto sai do estoque'' AFTER quantidade',
  'SELECT ''Coluna tipo já existe em inventario_devolucoes'' AS info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
