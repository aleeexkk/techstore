-- =====================================================
-- TechStore - Banco de Dados E-commerce
-- Disciplina: Modelagem e Banco de Dados
-- =====================================================

CREATE DATABASE IF NOT EXISTS loja_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE loja_db;

-- =====================================================
-- Tabela 1: categorias
-- =====================================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Tabela 2: tags (para relacionamento N:N com produtos)
-- =====================================================
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Tabela 3: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    endereco TEXT,
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Tabela 4: produtos
-- Chave estrangeira: categoria_id -> categorias(id)
-- =====================================================
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    preco_promocional DECIMAL(10,2) DEFAULT NULL,
    estoque INT DEFAULT 0,
    imagem VARCHAR(255),
    destaque TINYINT(1) DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    categoria_id INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- =====================================================
-- Tabela 5: pedidos
-- Chave estrangeira: usuario_id -> usuarios(id)
-- =====================================================
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    total DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) DEFAULT 0.00,
    desconto DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pendente','pago','enviado','entregue','cancelado') DEFAULT 'pendente',
    observacao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- =====================================================
-- Tabela 6: itens_pedido
-- Relacionamento N:N entre pedidos e produtos
-- (1 pedido tem muitos produtos; 1 produto aparece em muitos pedidos)
-- =====================================================
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) GENERATED ALWAYS AS (quantidade * preco_unitario) STORED,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
);

-- =====================================================
-- Tabela 7: produto_tags
-- Relacionamento N:N entre produtos e tags
-- (1 produto tem muitas tags; 1 tag está em muitos produtos)
-- =====================================================
CREATE TABLE IF NOT EXISTS produto_tags (
    produto_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (produto_id, tag_id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- =====================================================
-- DADOS DE EXEMPLO
-- =====================================================

INSERT INTO categorias (nome, descricao) VALUES
('Eletrônicos', 'Smartphones, notebooks e gadgets'),
('Informática', 'Periféricos, componentes e acessórios'),
('Games', 'Consoles, jogos e controles'),
('Áudio', 'Fones, caixas de som e headsets'),
('Fotografia', 'Câmeras, lentes e acessórios');

INSERT INTO tags (nome) VALUES
('Novo'),('Oferta'),('Mais Vendido'),('Importado'),('Frete Grátis'),('Lançamento');

INSERT INTO usuarios (nome, email, senha, telefone) VALUES
('Admin TechStore', 'admin@techstore.com', '$2y$10$exemplo_hash_senha', '(44) 99999-0000'),
('João Silva', 'joao@email.com', '$2y$10$exemplo_hash_senha', '(44) 98888-1111'),
('Maria Santos', 'maria@email.com', '$2y$10$exemplo_hash_senha', '(44) 97777-2222');

INSERT INTO produtos (nome, descricao, preco, preco_promocional, estoque, destaque, categoria_id) VALUES
('Smartphone Galaxy A55', 'Tela 6.6" Super AMOLED, 128GB, Câmera tripla 50MP', 1899.00, 1699.00, 15, 1, 1),
('Notebook Ideapad', 'Intel Core i5 12ª geração, 8GB RAM, SSD 256GB', 2499.00, NULL, 8, 1, 2),
('Headset Gamer RGB', 'Som surround 7.1, microfone com cancelamento de ruído', 299.00, 249.00, 25, 0, 4),
('Controle PS5 DualSense', 'Gatilhos adaptáveis, feedback háptico, sem fio', 449.00, NULL, 12, 1, 3),
('Mouse Gamer 16000 DPI', '7 botões programáveis, iluminação RGB, polling 1000Hz', 189.00, 159.00, 30, 0, 2),
('Câmera Mirrorless Sony', 'Sensor APS-C 26MP, vídeo 4K, Wi-Fi integrado', 3299.00, NULL, 5, 1, 5),
('Teclado Mecânico TKL', 'Switch blue, RGB por tecla, sem numérico', 349.00, 299.00, 20, 0, 2),
('Caixa de Som Bluetooth', '30W RMS, à prova d''água IPX7, 24h de bateria', 399.00, NULL, 18, 1, 4),
('Webcam Full HD 1080p', 'Autofoco, microfone embutido, USB plug-and-play', 219.00, 189.00, 22, 0, 2),
('SSD 1TB NVMe M.2', 'Leitura 3500 MB/s, compatível PCIe 4.0', 499.00, 449.00, 10, 0, 2);

INSERT INTO produto_tags (produto_id, tag_id) VALUES
(1, 1),(1, 5),(2, 3),(2, 5),
(3, 2),(3, 3),(4, 1),(4, 6),
(5, 2),(6, 4),(7, 2),(8, 1),
(9, 3),(10, 2),(10, 5);
