CREATE DATABASE fast_service;
USE fast_service;

-- 1. Tabela de Usuários (Centraliza Login e Autenticação)
CREATE TABLE TB_usuario (
    PK_id_TB_usuario INT AUTO_INCREMENT PRIMARY KEY,
    email_TB_usuario VARCHAR(150) NOT NULL UNIQUE,
    senha_TB_usuario VARCHAR(255) NOT NULL,
    tipo_TB_usuario ENUM('cliente', 'prestador', 'admin') NOT NULL
);

-- 2. Perfil do Cliente
CREATE TABLE TB_clientePerfil (
    PK_id_TB_cliente INT AUTO_INCREMENT PRIMARY KEY,
    FK_id_TB_usuario INT NOT NULL UNIQUE,
    nome_TB_cliente VARCHAR(100) NOT NULL,
    tel_TB_cliente VARCHAR(20),
    cpf_TB_cliente VARCHAR(14) UNIQUE,
    rua_TB_cliente VARCHAR(100),
    cep_TB_cliente VARCHAR(9),
    numeroCasa_TB_cliente VARCHAR(10),
    cidade_TB_cliente VARCHAR(50),
    FOREIGN KEY (FK_id_TB_usuario) REFERENCES TB_usuario(PK_id_TB_usuario)
);

-- 3. Perfil do Prestador
CREATE TABLE TB_prestadorPerfil (
    PK_id_TB_prestadorPerfil INT AUTO_INCREMENT PRIMARY KEY,
    FK_id_TB_usuario INT NOT NULL UNIQUE,
    nome_TB_prestador VARCHAR(100) NOT NULL,
    tel_TB_prestador VARCHAR(20),
    cpf_cnpj_TB_prestador VARCHAR(18) UNIQUE,
    bio_TB_prestador TEXT,
    rua_TB_prestadorPerfil VARCHAR(100),
    cep_TB_prestadorPerfil VARCHAR(9),
    numeroCasa_TB_prestadorPerfil VARCHAR(10),
    cidade_TB_prestadorPerfil VARCHAR(50),
    FOREIGN KEY (FK_id_TB_usuario) REFERENCES TB_usuario(PK_id_TB_usuario)
);

-- 4. Categorias de Serviços
CREATE TABLE TB_categoria (
    PK_id_TB_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome_TB_categoria VARCHAR(50) NOT NULL
);

-- 5. Serviços do Catálogo
CREATE TABLE TB_servico (
    PK_id_TB_servico INT AUTO_INCREMENT PRIMARY KEY,
    FK_id_TB_categoria INT NOT NULL,
    nome_TB_servico VARCHAR(100) NOT NULL,
    descricao_TB_servico TEXT,
    precoPadrao_TB_servico DECIMAL(10,2),
    orcamento_TB_servico BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (FK_id_TB_categoria) REFERENCES TB_categoria(PK_id_TB_categoria)
);

-- 6. Tabela Associativa: Serviços oferecidos por cada Prestador
CREATE TABLE TB_prestadorServico (
    PK_id_TB_prestadorServico INT AUTO_INCREMENT PRIMARY KEY,
    FK_id_TB_servico INT NOT NULL,
    FK_id_TB_prestadorPerfil INT NOT NULL,
    preco_customizado_TB_prestadorServico DECIMAL(10,2),
    FOREIGN KEY (FK_id_TB_servico) REFERENCES TB_servico(PK_id_TB_servico),
    FOREIGN KEY (FK_id_TB_prestadorPerfil) REFERENCES TB_prestadorPerfil(PK_id_TB_prestadorPerfil)
);

-- 7. Solicitação / Agendamento do Serviço
CREATE TABLE TB_SolicitacaoServico (
    PK_id_TB_SolicitacaoServico INT AUTO_INCREMENT PRIMARY KEY,
    FK_id_TB_cliente INT NOT NULL,
    FK_id_TB_prestadorServico INT NOT NULL,
    data_agendamento_TB_SolicitacaoServico DATETIME NOT NULL,
    status_TB_SolicitacaoServico ENUM('pendente', 'aceito', 'em_andamento', 'concluido', 'cancelado') DEFAULT 'pendente',
    valorTotal_TB_SolicitacaoServico DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (FK_id_TB_cliente) REFERENCES TB_clientePerfil(PK_id_TB_cliente),
    FOREIGN KEY (FK_id_TB_prestadorServico) REFERENCES TB_prestadorServico(PK_id_TB_prestadorServico)
);

-- 8. Pagamento
CREATE TABLE TB_pagamento (
    PK_id_TB_pagamento INT AUTO_INCREMENT PRIMARY KEY,
    FK_id_TB_SolicitacaoServico INT NOT NULL UNIQUE,
    forma_pagamento_TB_pagamento ENUM('cartao_credito', 'cartao_debito', 'pix', 'dinheiro') NOT NULL,
    status_pagamento_TB_pagamento ENUM('pendente', 'pago', 'reembolsado', 'falhou') DEFAULT 'pendente',
    data_pagamento_TB_pagamento DATETIME,
    valor_TB_pagamento DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (FK_id_TB_SolicitacaoServico) REFERENCES TB_SolicitacaoServico(PK_id_TB_SolicitacaoServico)
);

-- 9. Avaliação do Serviço
CREATE TABLE TB_avaliacao (
    PK_id_TB_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
    FK_id_TB_SolicitacaoServico INT NOT NULL UNIQUE,
    nota_TB_avaliacao INT NOT NULL CHECK (nota_TB_avaliacao BETWEEN 1 AND 5),
    comentario_TB_avaliacao TEXT,
    data_avaliacao_TB_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (FK_id_TB_SolicitacaoServico) REFERENCES TB_SolicitacaoServico(PK_id_TB_SolicitacaoServico)
);