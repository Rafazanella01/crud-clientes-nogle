CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    endereco VARCHAR(150) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    numero INT(10) NOT NULL,
    complemento VARCHAR(100),
    ativo BOOLEAN DEFAULT TRUE
);
