CREATE DATABASE banco_montink DEFAULT CHARACTER SET utf8mb4;
USE banco_montink;


CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL
);


CREATE TABLE variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);


CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variacao_id INT NOT NULL,
    quantidade INT NOT NULL,
    FOREIGN KEY (variacao_id) REFERENCES variacoes(id) ON DELETE CASCADE
);


CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    desconto DECIMAL(10,2) NOT NULL,
    minimo DECIMAL(10,2) NOT NULL,
    validade DATE NOT NULL
);


CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    cep VARCHAR(9),
    endereco TEXT,
    email VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pendente',
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    variacao_id INT,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (variacao_id) REFERENCES variacoes(id)
);
