

CREATE TABLE `produto_definicoes` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE produtos ADD COLUMN url TEXT;