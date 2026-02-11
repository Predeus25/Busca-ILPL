-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/05/2025 às 18:43
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `registro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `dados_idoso`
--

CREATE TABLE `dados_idoso` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `rg` varchar(15) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `tipo_sanguineo` varchar(3) NOT NULL,
  `doenca` varchar(50) DEFAULT NULL,
  `outra_doenca` varchar(100) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `sexo` enum('Masculino','Feminino') NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `dados_idoso`
--

INSERT INTO `dados_idoso` (`id`, `nome`, `rg`, `cpf`, `tipo_sanguineo`, `doenca`, `outra_doenca`, `data_nascimento`, `sexo`, `foto_perfil`, `data_cadastro`) VALUES
(1, 'Edson José dos Santos', '01.020.000-3', '434.235.235-2', 'A+', 'Diabetes', '', '1950-06-10', 'Masculino', NULL, '2025-05-28 16:31:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `dados_responsavel`
--

CREATE TABLE `dados_responsavel` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cell` int(15) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `complemento` varchar(100) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `rg` varchar(15) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `tipo_sanguineo` varchar(3) NOT NULL,
  `parentesco` varchar(50) DEFAULT NULL,
  `outro_parentesco` varchar(100) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `dados_responsavel`
--

INSERT INTO `dados_responsavel` (`id`, `nome`, `email`, `cell`, `endereco`, `bairro`, `numero`, `complemento`, `cep`, `rg`, `cpf`, `tipo_sanguineo`, `parentesco`, `outro_parentesco`, `data_cadastro`) VALUES
(1, 'João Victor Predeus dos Santos Silva', 'jvpredeus@gmail.com', 0, 'Rua Guilhermino de Lima', 'Vila Augusta', '196', 'Apto 43', '07040-090', '02.528.596-0', '549.703.708-93', 'O+', 'neto', '', '2025-05-28 16:32:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `registros`
--

CREATE TABLE `registros` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `registros`
--

INSERT INTO `registros` (`id`, `email`, `senha`, `data_cadastro`) VALUES
(1, 'jvpredeus@gmail.com', '$2y$10$eqTEACRc8E0bhaatdpj8jOX/eXF9sGyMJP8LxJyC5boxaAnl21Q3G', '2025-05-28 16:28:52');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `dados_idoso`
--
ALTER TABLE `dados_idoso`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `dados_responsavel`
--
ALTER TABLE `dados_responsavel`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `registros`
--
ALTER TABLE `registros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `dados_idoso`
--
ALTER TABLE `dados_idoso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `dados_responsavel`
--
ALTER TABLE `dados_responsavel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `registros`
--
ALTER TABLE `registros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
