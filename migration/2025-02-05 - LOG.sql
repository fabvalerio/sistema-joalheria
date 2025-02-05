CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50),
    ip VARCHAR(45),
    acao TEXT,
    valor_anterior TEXT,
    valor_atual TEXT,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);