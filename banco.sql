CREATE DATABASE IF NOT EXISTS mr_logistica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mr_logistica;
CREATE TABLE IF NOT EXISTS produtos (
 id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120) NOT NULL, descricao TEXT,
 peso DECIMAL(10,2) NOT NULL, valor DECIMAL(12,2) NOT NULL DEFAULT 0,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS enderecos (
 id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120) NOT NULL,
 tipo ENUM('Origem','Destino','Ambos') NOT NULL DEFAULT 'Ambos',
 rua VARCHAR(160) NOT NULL, numero VARCHAR(20) NOT NULL, bairro VARCHAR(100),
 cidade VARCHAR(100) NOT NULL, estado CHAR(2) NOT NULL, cep VARCHAR(10),
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS transportadores (
 id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120) NOT NULL,
 documento VARCHAR(20), telefone VARCHAR(25), veiculo VARCHAR(100), placa VARCHAR(12),
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS fretes (
 id INT AUTO_INCREMENT PRIMARY KEY, transportador_id INT NOT NULL, origem_id INT NOT NULL,
 destino_id INT NOT NULL, data_frete DATE NOT NULL, status ENUM('Pendente','Em trânsito','Entregue') NOT NULL DEFAULT 'Pendente',
 observacoes TEXT, criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (transportador_id) REFERENCES transportadores(id),
 FOREIGN KEY (origem_id) REFERENCES enderecos(id),
 FOREIGN KEY (destino_id) REFERENCES enderecos(id)
);
CREATE TABLE IF NOT EXISTS frete_produtos (
 id INT AUTO_INCREMENT PRIMARY KEY, frete_id INT NOT NULL, produto_id INT NOT NULL,
 quantidade INT NOT NULL DEFAULT 1,
 FOREIGN KEY (frete_id) REFERENCES fretes(id) ON DELETE CASCADE,
 FOREIGN KEY (produto_id) REFERENCES produtos(id),
 UNIQUE KEY uq_frete_produto (frete_id,produto_id)
);
