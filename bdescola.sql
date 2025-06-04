-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/06/2025 às 02:17
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
-- Banco de dados: `bdescola`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbbibliotecario`
--

CREATE TABLE `tbbibliotecario` (
  `codigo` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text DEFAULT NULL,
  `cpf` text DEFAULT NULL,
  `codigo_escola` text DEFAULT NULL,
  `acesso` text DEFAULT NULL,
  `cadastrado_por` varchar(50) NOT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `codigo_autenticacao` varchar(8) DEFAULT NULL,
  `chave_recuperar_senha` varchar(220) DEFAULT NULL,
  `data_codigo_autenticacao` datetime DEFAULT NULL,
  `statusDev` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbbibliotecario`
--

INSERT INTO `tbbibliotecario` (`codigo`, `nome`, `email`, `password`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`, `cadastrado_por`, `data_cadastro`, `codigo_autenticacao`, `chave_recuperar_senha`, `data_codigo_autenticacao`, `statusDev`) VALUES
(1, 'Funcionário Teste', 'teste@email.com', '$2y$10$Lko2658q.wupmxP1az9o5.swd1F719SLuIxQpPtO9YtEMSo9qt7pa', '', '18991599472', '46533043862', '055', 'bibliotecario', '', NULL, NULL, '$2y$10$Z3HWtCKSybMbps2fQz2XB.OXT7rNWauOzzihl7OqIWmtME4c7Ruwq', NULL, NULL),
(2, 'ivasco', 'ivasco@gmail.com', '$2y$10$O9bevSswR5q0CffZndJRfuXISvCxrz/FuJoq1mayfONZ67j.xEU0e', '18996511409', '18996511409', '62949020020', '015', 'bibliotecario', 'Jonathan Henrique Granado Perroni', '2025-02-16 14:08:49', NULL, NULL, NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tbbibliotecario`
--
ALTER TABLE `tbbibliotecario`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tbbibliotecario`
--
ALTER TABLE `tbbibliotecario`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
