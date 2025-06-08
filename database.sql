CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pontos INT NOT NULL,
    data_partida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE ligas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    palavra_chave VARCHAR(255) NOT NULL UNIQUE,
    criado_por INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criado_por) REFERENCES usuarios(id)
);

CREATE TABLE ligas_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    liga_id INT NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (liga_id) REFERENCES ligas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    UNIQUE(liga_id, usuario_id)
);

CREATE TABLE pontuacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    liga_id INT DEFAULT NULL, -- Can be NULL if it's a general score, not tied to a specific league
    pontos INT NOT NULL, -- For WPM
    accuracy DECIMAL(5,2) NULL, -- Accuracy percentage e.g. 95.50
    data_partida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (liga_id) REFERENCES ligas(id)
);
