-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 21/05/2025 às 23:35
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `plataforma_imigracao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos`
--

CREATE TABLE `documentos` (
  `DocumentoId` int(11) NOT NULL,
  `ProcId` int(11) NOT NULL,
  `NomeDocumento` varchar(255) NOT NULL,
  `NomeArquivo` varchar(255) NOT NULL,
  `CaminhoArquivo` varchar(500) NOT NULL,
  `Status` enum('Em análise','Aprovado','Reprovado') DEFAULT 'Em análise',
  `DataEnvio` datetime DEFAULT current_timestamp(),
  `Observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `documentos`
--

INSERT INTO `documentos` (`DocumentoId`, `ProcId`, `NomeDocumento`, `NomeArquivo`, `CaminhoArquivo`, `Status`, `DataEnvio`, `Observacao`) VALUES
(1, 8, 'Passaporte válido', '1-SPLASH_FUN_0542.jpg', 'uploads/documentos/Eduarda_Ferreira_Fernandes_8/processo_8/doc_682e4228400e31.04918236.jpg', 'Em análise', '2025-05-20 00:09:59', ''),
(2, 8, 'Título de residência válido', '02 CATE-84.jpg', 'uploads/documentos/Eduarda_Ferreira_Fernandes_8/processo_8/doc_682e3d5deb7d91.99985769.jpg', 'Aprovado', '2025-05-21 17:53:49', NULL),
(3, 8, 'Certidão de nascimento (apostilada e traduzida)', '02 CATE-87.jpg', 'uploads/documentos/Eduarda_Ferreira_Fernandes_8/processo_8/doc_682e3d7ecb77f7.07034722.jpg', 'Reprovado', '2025-05-21 17:54:22', 'Teste'),
(4, 8, 'Atestado de antecedentes criminais do país de origem', '02 CATE-88.jpg', 'uploads/documentos/Eduarda_Ferreira_Fernandes_8/processo_8/doc_682e3e3cc257f6.17430774.jpg', 'Reprovado', '2025-05-21 17:57:32', 'Teste');

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapas_processo`
--

CREATE TABLE `etapas_processo` (
  `EtapaId` int(11) NOT NULL,
  `ProcId` int(11) NOT NULL,
  `Titulo` varchar(100) DEFAULT NULL,
  `Descricao` text DEFAULT NULL,
  `Concluida` tinyint(1) DEFAULT 0,
  `DataConclusao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `MsgId` int(11) NOT NULL,
  `ProcId` int(11) NOT NULL,
  `Remetente` enum('cliente','admin') DEFAULT NULL,
  `Mensagem` text NOT NULL,
  `DataEnvio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `processos`
--

CREATE TABLE `processos` (
  `ProcId` int(11) NOT NULL,
  `UsuId` int(11) NOT NULL,
  `Tipo` varchar(100) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `DataPedido` date DEFAULT NULL,
  `UltimaAtualizacao` datetime DEFAULT NULL,
  `Observacoes` text DEFAULT NULL,
  `Porcentagem` int(11) DEFAULT 0,
  `EtapaAtual` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `processos`
--

INSERT INTO `processos` (`ProcId`, `UsuId`, `Tipo`, `Status`, `DataPedido`, `UltimaAtualizacao`, `Observacoes`, `Porcentagem`, `EtapaAtual`) VALUES
(8, 8, 'Nacionalidade Por Tempo de Residencia', 'Reunião de documentos', '2025-05-20', '2025-05-20 05:10:28', NULL, 22, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `UsuId` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Senha` varchar(255) NOT NULL,
  `Telefone` varchar(20) DEFAULT NULL,
  `Tipo` enum('cliente','admin') DEFAULT 'cliente',
  `DataCadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `token_recuperacao` varchar(64) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  `nascimento` date NOT NULL,
  `nacionalidade` varchar(100) NOT NULL,
  `estado_civil` varchar(50) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `pais` varchar(100) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `rg` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`UsuId`, `Nome`, `Email`, `Senha`, `Telefone`, `Tipo`, `DataCadastro`, `token_recuperacao`, `token_expira`, `nascimento`, `nacionalidade`, `estado_civil`, `endereco`, `cidade`, `estado`, `pais`, `cpf`, `rg`) VALUES
(2, 'Arthur Ferreira Fernandes', 'arthurfernandesferreira@hotmail.com', '$2y$10$oS/aX64vnv0Lt3dKjLXT8OTL45BqI6EIjeR8/HY4tKNrxo0JNABZG', '11986599562', 'admin', '2025-05-15 04:04:57', NULL, NULL, '2005-06-15', 'Brasileiro', 'Casado(a)', 'Rua Tié 136', 'São Paulo', 'São Paulo', 'Brasil', '42526046807', '535201394'),
(7, 'Sarah Alves Moya Ferreira', 'sarahalmoya@gmail.com', '$2y$10$lnvYwzkehuUsrB6u8UFqBOLLSlUX1/b1IFEf/38peHMKTGAId5e9K', '11998844335', 'admin', '2025-05-19 03:34:12', NULL, NULL, '2004-09-03', 'Brasileira', 'Casado(a)', 'Rua Tié 136', 'São Paulo', 'São Paulo', 'Brasil', '37059767886', '398463633'),
(8, 'Eduarda Ferreira Fernandes', 'eduarda@123.com', '$2y$10$rgeGFbgl7W5hGkLWDN5hyO0IjkDDdejB7fSN8rNqAshfBgc9zlyNS', '99999999999', 'cliente', '2025-05-19 03:37:49', NULL, NULL, '2013-05-20', 'Brasileira', 'Solteiro(a)', 'Rua Elza dos Anjos Neves 556', 'São Paulo', 'São Paulo', 'Brasil', '12345678901', '987654321');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`DocumentoId`),
  ADD KEY `ProcId` (`ProcId`);

--
-- Índices de tabela `etapas_processo`
--
ALTER TABLE `etapas_processo`
  ADD PRIMARY KEY (`EtapaId`),
  ADD KEY `ProcId` (`ProcId`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`MsgId`),
  ADD KEY `ProcId` (`ProcId`);

--
-- Índices de tabela `processos`
--
ALTER TABLE `processos`
  ADD PRIMARY KEY (`ProcId`),
  ADD KEY `UsuId` (`UsuId`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`UsuId`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `DocumentoId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `etapas_processo`
--
ALTER TABLE `etapas_processo`
  MODIFY `EtapaId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `MsgId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `processos`
--
ALTER TABLE `processos`
  MODIFY `ProcId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `UsuId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`ProcId`) REFERENCES `processos` (`ProcId`) ON DELETE CASCADE;

--
-- Restrições para tabelas `etapas_processo`
--
ALTER TABLE `etapas_processo`
  ADD CONSTRAINT `etapas_processo_ibfk_1` FOREIGN KEY (`ProcId`) REFERENCES `processos` (`ProcId`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`ProcId`) REFERENCES `processos` (`ProcId`) ON DELETE CASCADE;

--
-- Restrições para tabelas `processos`
--
ALTER TABLE `processos`
  ADD CONSTRAINT `processos_ibfk_1` FOREIGN KEY (`UsuId`) REFERENCES `usuarios` (`UsuId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
