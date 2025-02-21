-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/02/2025 às 00:41
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
-- Estrutura para tabela `dados_etec`
--

CREATE TABLE `dados_etec` (
  `codigo` int(11) NOT NULL,
  `codigo_escola` varchar(50) NOT NULL,
  `unidadeEscola` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `dados_etec`
--

INSERT INTO `dados_etec` (`codigo`, `codigo_escola`, `unidadeEscola`) VALUES
(1, '006', 'Etec Polivalente de Americana'),
(2, '007', 'Etec Conselheiro Antônio Prado'),
(3, '008', 'Etec Vasco Antônio Venchiarutti'),
(4, '009', 'Etec João Baptista de Lima Figueiredo'),
(5, '010', 'Etec Lauro Gomes'),
(6, '011', 'Etec Jorge Street'),
(7, '012', 'Etec Prof. Camargo Aranha (Mooca)'),
(8, '013', 'Etec Getúlio Vargas (Ipiranga)'),
(9, '014', 'Etec Júlio de Mesquita'),
(10, '015', 'Etec Presidente Vargas'),
(11, '016', 'Etec Fernando Prestes'),
(12, '017', 'Etec Rubens de Faria e Souza'),
(13, '018', 'Etec São Paulo (Bom Retiro)'),
(14, '019', 'Etec Dr. Adail Nunes da Silva'),
(15, '023', 'Etec Albert Einstein (Casa Verde)'),
(16, '024', 'Etec Prefeito Alberto Feres'),
(17, '025', 'Etec Prof. Alcídio de Souza Prado'),
(18, '026', 'Etec Prof. Alfredo de Barros Santos'),
(19, '027', 'Etec Amim Jundi'),
(20, '028', 'Etec Sebastiana Augusta de Moraes'),
(21, '029', 'Etec Profª Anna de Oliveira Ferraz'),
(22, '030', 'Etec Antônio de Pádua Cardoso'),
(23, '031', 'Etec Antônio Devisate'),
(24, '032', 'Etec Prof. Dr. Antônio E. de Toledo'),
(25, '033', 'Etec Antonio Junqueira da Veiga'),
(26, '034', 'Etec Prof. Aprígio Gonzaga (Penha)'),
(27, '035', 'Etec Aristóteles Ferreira'),
(28, '036', 'Etec Prof. Armando Bayeux da Silva'),
(29, '037', 'Etec Frei Arnaldo Maria de Itaporanga'),
(30, '038', 'Etec Astor de Mattos Carvalho'),
(31, '039', 'Etec Augusto Tortolero Araújo'),
(32, '040', 'Etec Comendador João Rays'),
(33, '041', 'Etec Prof. Basílides de Godoy (Vila Leopoldina)'),
(34, '042', 'Etec Benedito Storani'),
(35, '043', 'Etec Bento Quirino'),
(36, '044', 'Etec Prof. Marcos Uchôas dos Santos Penchel'),
(37, '045', 'Etec Carlos de Campos (Brás)'),
(38, '046', 'Etec Prof. Carmelino Correa Junior'),
(39, '047', 'Etec Dr. Carolino da Motta e Silva'),
(40, '048', 'Etec Cônego José Bento'),
(41, '049', 'Etec Dr. Dario Pacheco Pedroso'),
(42, '050', 'Etec Dr. Demétrio Azevedo Jr.'),
(43, '051', 'Etec Dr. Domingos Minicucci Filho'),
(44, '052', 'Etec Profª Carmelina Barbosa'),
(45, '053', 'Etec Prof. Edson Galvão'),
(46, '054', 'Etec Elias Nechar'),
(47, '061', 'Etec Guaracy Silveira (Pinheiros)'),
(48, '062', 'Etec Profª Helcy Moreira Martins Aguiar'),
(49, '055', 'Etec Prof. Eudécio Luiz Vicente'),
(50, '056', 'Etec Cel. Fernando Febeliano da Costa'),
(51, '057', 'Etec Prof. Francisco dos Santos'),
(52, '058', 'Etec Dep. Francisco Franco'),
(53, '059', 'Etec Dr. Francisco Nogueira de Lima'),
(54, '060', 'Etec Francisco Garcia'),
(55, '054', 'Etec Elias Nechar'),
(56, '065', 'Etec de Ilha Solteira'),
(57, '066', 'Etec Jacinto Ferreira de Sá'),
(58, '067', 'Etec João Belarmino'),
(59, '068', 'Etec João Gomes de Araújo'),
(60, '069', 'Etec João Jorge Geraissate');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_usuarios`
--

CREATE TABLE `historico_usuarios` (
  `id` int(11) NOT NULL,
  `historico_responsavel` text NOT NULL,
  `historico_acao` text NOT NULL,
  `historico_usuario` text NOT NULL,
  `historico_acesso` text NOT NULL,
  `historico_data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `historico_usuarios`
--

INSERT INTO `historico_usuarios` (`id`, `historico_responsavel`, `historico_acao`, `historico_usuario`, `historico_acesso`, `historico_data_hora`) VALUES
(1, 'Jonathan Perroni', 'cadastrar', 'Magnata', 'administrador', '2024-11-01 04:53:55'),
(2, 'Jonathan Perroni', 'cadastrar', 'Larrissa Noda', 'desenvolvedor', '2024-11-03 18:05:53'),
(3, 'Jonathan Perroni', 'cadastrar', 'Etec La Noda', 'tecnica', '2024-11-03 18:30:58'),
(4, 'Jonathan Perroni', 'cadastrar', 'Pedro ETEC', 'Escola', '2024-11-03 18:35:52'),
(5, 'Jonathan Perroni', 'editar', 'Jonathan Granado Perroni', 'Desenvolvedor', '2024-11-03 18:41:32'),
(6, 'Jonathan Perroni', 'editar', 'Julio cessar romano', 'administrador', '2024-11-03 18:46:07'),
(7, 'Jonathan Perroni', 'editar', 'Etec Prof. Basílides de Godoy (Vila Leopoldina)', 'Escola', '2024-11-03 18:50:21'),
(8, 'Jonathan Perroni', 'excluir', '11', 'Desenvolvedor', '2024-11-03 19:08:05'),
(9, 'Jonathan Perroni', 'excluir', '15', 'Desenvolvedor', '2024-11-03 19:11:59'),
(10, 'Jonathan Perroni', 'excluir', '12', 'Desenvolvedor', '2024-11-03 19:22:09'),
(11, 'Jonathan Perroni', 'excluir', 'Larissa noda', 'Desenvolvedor', '2024-11-03 19:24:14'),
(12, 'Jonathan Perroni', 'excluir', 'jonathan Henrique Granaddo Perroni', 'administrador', '2024-11-03 19:26:17'),
(13, 'Jonathan Perroni', 'excluir', 'Larrissa Noda', 'desenvolvedor', '2024-11-03 19:29:14'),
(14, 'Jonathan Perroni', 'excluir', '44', 'Escola', '2024-11-03 19:31:36'),
(15, 'Jonathan Perroni', 'excluir', 'Etec La Noda', 'Escola', '2024-11-03 19:35:49'),
(16, 'Jonathan Perroni', 'excluir', 'zitos', 'Escola', '2024-11-03 19:35:56'),
(17, 'Jonathan Granado Perroni', 'cadastrar', 'Jonathan Henrique Granado Perroni', 'administrador', '2024-11-04 01:23:37'),
(18, 'Funcionário Teste', 'editar', 'Funcionário Teste', 'bibliotecario', '2025-02-16 06:12:56'),
(19, 'Jonathan Granado Perroni', 'cadastrar', 'Dev user test final', 'desenvolvedor', '2025-02-16 15:21:37'),
(20, 'Jonathan Granado Perroni', 'cadastrar', 'adminteste@gmail.com', 'administrador', '2025-02-16 15:23:50'),
(21, 'Jonathan Granado Perroni', 'cadastrar', 'Etec zitos', 'Escola', '2025-02-16 15:25:53'),
(22, 'Funcionário Teste', 'editar', 'Dev user test finall', 'desenvolvedor', '2025-02-16 15:52:38'),
(23, 'Funcionário Teste', 'excluir', 'Dev user test finall', 'desenvolvedor', '2025-02-16 15:53:15'),
(24, 'Funcionário Teste', 'editar', 'admintestee@gmail.com', 'administrador', '2025-02-16 15:54:24'),
(25, 'Funcionário Teste', 'excluir', 'admintestee@gmail.com', 'administrador', '2025-02-16 15:54:44'),
(26, 'Funcionário Teste', 'editar', 'Etec zitosss', 'Escola', '2025-02-16 15:55:27'),
(27, 'Funcionário Teste', 'excluir', 'Etec zitosss', 'Escola', '2025-02-16 15:55:39'),
(28, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'Felipe ', 'administrador', '2025-02-16 15:59:22'),
(29, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'ivasco', 'administrador', '2025-02-16 16:01:40'),
(30, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'ivasco', 'bibliotecario', '2025-02-16 17:08:49'),
(31, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'Desenvolvimento web', 'Curso', '2025-02-16 17:20:05'),
(32, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'Figma UX', 'Curso', '2025-02-16 17:27:34'),
(33, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'UX & XD', 'Curso', '2025-02-16 17:48:41'),
(34, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'Curso X', 'Curso', '2025-02-16 17:49:36'),
(35, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'Teste Curso', 'Curso', '2025-02-16 17:54:03'),
(36, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'aa', 'Curso', '2025-02-16 17:58:58'),
(37, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'a', 'Curso', '2025-02-16 18:00:33'),
(38, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'a', 'Curso', '2025-02-16 18:00:49'),
(39, 'Jonathan Henrique Granado Perroni', 'cadastrar', 'adasdasdasdasd', 'Curso', '2025-02-16 18:05:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbadmin`
--

CREATE TABLE `tbadmin` (
  `codigo` int(11) NOT NULL,
  `nome` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text NOT NULL,
  `cpf` char(11) NOT NULL,
  `codigo_escola` int(3) DEFAULT NULL,
  `acesso` text DEFAULT NULL,
  `cadastrado_por` varchar(50) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `codigo_autenticacao` varchar(8) DEFAULT NULL,
  `chave_recuperar_senha` varchar(220) DEFAULT NULL,
  `data_codigo_autenticacao` datetime DEFAULT NULL,
  `statusDev` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbadmin`
--

INSERT INTO `tbadmin` (`codigo`, `nome`, `email`, `password`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`, `cadastrado_por`, `data_cadastro`, `codigo_autenticacao`, `chave_recuperar_senha`, `data_codigo_autenticacao`, `statusDev`) VALUES
(16, 'Jonathan Henrique Granado Perroni', 'jhow.zitos@gmail.com', '$2y$10$TlJa4hIWCJDLHno8ULWRQOEGMtULv973czMVMbQ1JJv6q5PhqjcYm', '18996511409', '18996511409', '36844808860', 8, 'administrador', 'Jonathan Granado Perroni', '2024-11-03 22:23:37', NULL, '$2y$10$PpWY6Ku2kFA4KMd88qZYvemCIKrJkCm0xo2fZIiPPGrVDSActu9G.', NULL, 1),
(18, 'Felipe ', 'felipe@gmail.com', '$2y$10$wY2pNlQuR9F42e4Amiq0V./HS6qhQfIkyOktqJnhfMWZ5Eh2CJfmy', '18996511409', '18996511409', '25810827047', 11, 'administrador', 'Jonathan Henrique Granado Perroni', '2025-02-16 12:59:22', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbalunos`
--

CREATE TABLE `tbalunos` (
  `codigo` int(11) NOT NULL,
  `nome` text NOT NULL,
  `data_nascimento` date NOT NULL,
  `endereco` text NOT NULL,
  `cidade` text NOT NULL,
  `estado` char(2) NOT NULL,
  `cpf` char(11) NOT NULL,
  `celular` text NOT NULL,
  `periodo` text NOT NULL,
  `situacao` text NOT NULL,
  `responsavel` text NOT NULL,
  `nome_escola` text NOT NULL,
  `nome_curso` text NOT NULL,
  `email` text DEFAULT NULL,
  `tipo_ensino` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `acesso` text NOT NULL,
  `cadastrado_por` varchar(50) DEFAULT NULL,
  `data_cdadastro` datetime DEFAULT NULL,
  `codigo_autenticacao` varchar(8) DEFAULT NULL,
  `chave_recuperar_senha` varchar(220) DEFAULT NULL,
  `data_codigo_autenticacao` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbalunos`
--

INSERT INTO `tbalunos` (`codigo`, `nome`, `data_nascimento`, `endereco`, `cidade`, `estado`, `cpf`, `celular`, `periodo`, `situacao`, `responsavel`, `nome_escola`, `nome_curso`, `email`, `tipo_ensino`, `password`, `acesso`, `cadastrado_por`, `data_cdadastro`, `codigo_autenticacao`, `chave_recuperar_senha`, `data_codigo_autenticacao`, `status`) VALUES
(1, 'Aluno Teste da Silva', '1994-11-22', 'Rua Teste, 552', 'Adamantina', 'SP', '413.735.168', '18991599472', 'noite', 'a cursar', 'Maria Teste', 'Prof Eudécio Luiz Vicente', 'Desenvolvimento de Sistemas', 'aluno@teste.com', 'tecnico', '$2y$10$JcSCQzzMKtXK17nGTogu2epkBt4/Ic9kNlvXTeiDBqg0zHmtbXyhC', 'aluno', NULL, NULL, 'IXGzNg', '$2y$10$uUi0xSOFyGZ62SDzmCe.m.H7K6qXpYqjiQ2TSvnSOMjAMxarWcnIu', '2025-02-16 02:27:58', 1);

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
(1, 'Funcionário Teste', 'teste@email.com', '$2y$10$BTtaKIcjmqiK4NyxdHHClud/kF8buhEREEcugDDYWxuzHqLzs5aPu', '', '18991599472', '46533043862', '055', 'bibliotecario', '', NULL, NULL, '$2y$10$KkYBuaTj.WDuTgqrFMAXz.S6wjg1Q/ZTCTDshKEarkdshCG2i9leq', NULL, NULL),
(2, 'ivasco', 'ivasco@gmail.com', '$2y$10$O9bevSswR5q0CffZndJRfuXISvCxrz/FuJoq1mayfONZ67j.xEU0e', '18996511409', '18996511409', '62949020020', '015', 'bibliotecario', 'Jonathan Henrique Granado Perroni', '2025-02-16 14:08:49', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbclasse`
--

CREATE TABLE `tbclasse` (
  `codigo` int(11) NOT NULL,
  `classe` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbclasse`
--

INSERT INTO `tbclasse` (`codigo`, `classe`) VALUES
(1, 'Livro'),
(2, 'Revista'),
(3, 'Jornal'),
(4, 'Tcc'),
(5, 'Midia'),
(6, 'Jogo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbcursos`
--

CREATE TABLE `tbcursos` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text NOT NULL,
  `nome_curso` text NOT NULL,
  `tempo_curso` text NOT NULL,
  `cadastrado_por` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbcursos`
--

INSERT INTO `tbcursos` (`codigo`, `nome_escola`, `nome_curso`, `tempo_curso`, `cadastrado_por`, `data_cadastro`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Desenvolvimento de Sistemas', '3 semestres', '', '2025-02-16 17:20:00'),
(2, 'Prof Eudécio Luiz Vicente', 'Açúcar e Álcool', '3 semestres', '', '2025-02-16 17:20:00'),
(3, 'Prof Eudécio Luiz Vicente', 'Administração', '3 semestres', '', '2025-02-16 17:20:00'),
(4, 'Prof Eudécio Luiz Vicente', 'Contabilidade', '3 semestres', '', '2025-02-16 17:20:00'),
(5, 'Prof Eudécio Luiz Vicente', 'Enfermagem', '4 semestres', '', '2025-02-16 17:20:00'),
(6, 'Prof Eudécio Luiz Vicente', 'Ensino Médio', '6 semestres', '', '2025-02-16 17:20:00'),
(7, 'Prof Eudécio Luiz Vicente', 'Ensino Médio Integrado', '6 semestres', '', '2025-02-16 17:20:00'),
(8, 'Prof Eudécio Luiz Vicente', 'Informática para Internet', '3 semestres', '', '2025-02-16 17:20:00'),
(9, 'Prof Eudécio Luiz Vicente', 'Logística', '3 semestres', '', '2025-02-16 17:20:00'),
(10, 'Prof Eudécio Luiz Vicente', 'Mecânica', '3 semestres', '', '2025-02-16 17:20:00'),
(11, 'Prof Eudécio Luiz Vicente', 'Secretariado - EAD', '2 semestres', '', '2025-02-16 17:20:00'),
(12, 'Prof Eudécio Luiz Vicente', 'Segurança do Trabalho', '3 semestres', '', '2025-02-16 17:20:00'),
(13, '', 'Desenvolvimento web', '2', 'Jonathan Henrique Granado Perroni', '2025-02-16 17:20:05'),
(14, '', 'Figma UX', '2 semestres ', 'Jonathan Henrique Granado Perroni', '2025-02-16 17:27:34'),
(15, '', 'UX & XD', '2 semestre', 'Jonathan Henrique Granado Perroni', '2025-02-16 17:48:41'),
(16, '', 'Curso X', '1 semestre', 'Jonathan Henrique Granado Perroni', '2025-02-16 17:49:36'),
(17, '', 'Teste Curso', '1 semestre', 'Jonathan Henrique Granado Perroni', '2025-02-16 17:54:03'),
(18, '', 'aa', '1 ', 'Jonathan Henrique Granado Perroni', '2025-02-16 17:58:58'),
(19, '', 'a', '1', 'Jonathan Henrique Granado Perroni', '2025-02-16 18:00:33'),
(20, '', 'a', 'a', 'Jonathan Henrique Granado Perroni', '2025-02-16 18:00:49'),
(21, 'Etec Lauro Gomes', 'adasdasdasdasd', '2', 'Jonathan Henrique Granado Perroni', '2025-02-16 18:05:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbdev`
--

CREATE TABLE `tbdev` (
  `codigo` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `email` varchar(220) NOT NULL,
  `password` varchar(220) NOT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `acesso` varchar(15) DEFAULT NULL,
  `codigo_autenticacao` varchar(8) DEFAULT NULL,
  `chave_recuperar_senha` varchar(220) DEFAULT NULL,
  `data_codigo_autenticacao` datetime DEFAULT NULL,
  `statusDev` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbdev`
--

INSERT INTO `tbdev` (`codigo`, `nome`, `cpf`, `email`, `password`, `telefone`, `celular`, `acesso`, `codigo_autenticacao`, `chave_recuperar_senha`, `data_codigo_autenticacao`, `statusDev`) VALUES
(8, 'Jonathan Granado Perroni', '36844808860', 'jhow.zitos@gmail.com', '$2y$10$lrTMlf2V3I8DFDNJ.vkwieXTr.rh2dK/p/weBZsSgLkX0wCZzOsdS', '18996511409', '18996511409', 'Desenvolvedor', NULL, NULL, NULL, 1),
(9, 'Fernanda  Stanislaw', '00959139044', 'fer.stanislaw.fs@gmail.com', '$2y$10$uit8efwwTtyLbO.Gz8jx6O4MpWGVBfXi5lLGnHbOZGJt/tD2guBbS', '(18) 99612-2396', '(18) 99612-2396', 'Desenvolvedor', NULL, NULL, NULL, 1),
(11, 'supremeDev', '93608124047', 'supremeDev@gmail.com', '$2y$10$mP5t4HdOlM/ooQqfr2zHNOIIgL/Tf3BoCIBK3iQk..eiyrjbPdzDS', '18996511409', '18996511409', 'Desenvolvedor', NULL, NULL, NULL, 0),
(18, 'teste94', '16702118007', 'teste@teste.com', '$2y$10$ih8U1g9GHHZSwDt.O24/z.iGNPfoyVEoTrN1805dMF2kWe1op63YS', '18996511409', '18999999999', 'Desenvolvedor', NULL, NULL, NULL, 1),
(24, 'Felipe Ivasco', '41373516860', 'felipeivasco@gmail.com', '$2y$10$TJ2sPBt6uyW7crwVtI/3aOu1v9Q1ux5c1gGQLDajoSEmUitBK9p0a', '18996511409', '18996511409', 'desenvolvedor', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbemprestimos`
--

CREATE TABLE `tbemprestimos` (
  `id` int(11) NOT NULL,
  `cpf_aluno` varchar(14) NOT NULL,
  `isbn_livro` varchar(20) NOT NULL,
  `data_emprestimo` datetime DEFAULT current_timestamp(),
  `status` enum('emprestado','devolvido') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbemprestimos`
--

INSERT INTO `tbemprestimos` (`id`, `cpf_aluno`, `isbn_livro`, `data_emprestimo`, `status`) VALUES
(4, '413.735.168', '123456', '2025-02-16 02:56:34', 'emprestado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbescola`
--

CREATE TABLE `tbescola` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text NOT NULL,
  `tipoEscola` varchar(220) NOT NULL,
  `codigo_escola` varchar(50) NOT NULL,
  `endereco` text NOT NULL,
  `bairro` text NOT NULL,
  `cidade` text NOT NULL,
  `estado` char(100) NOT NULL,
  `cnpj` char(14) NOT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text NOT NULL,
  `email` varchar(220) NOT NULL,
  `cep` varchar(20) NOT NULL,
  `numero` varchar(100) NOT NULL,
  `cadastrado_por` varchar(220) NOT NULL,
  `data_cadastro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbescola`
--

INSERT INTO `tbescola` (`codigo`, `nome_escola`, `tipoEscola`, `codigo_escola`, `endereco`, `bairro`, `cidade`, `estado`, `cnpj`, `telefone`, `celular`, `email`, `cep`, `numero`, `cadastrado_por`, `data_cadastro`) VALUES
(37, 'Etec Prof. Basílides de Godoy (Vila Leopoldina)', 'ensinoMedio', '041 - Etec Prof. Basílides de Godoy (Vila Leopoldi', 'Av. ademar de barro, 489 ap 3', 'centro', 'Adamantina', 'SP', '04364091000103', '18996511409', '998086573', 'zitos.eteca@gmail.com', '17800000', '1444', 'Jonathan Perroni', '2024-09-10 01:47:46'),
(38, 'Etec João Jorge Geraissate', 'tecnico', '069 - Etec João Jorge Geraissate', 'Av. ademar de barro, 489 ap 3', 'centro', 'Adamantina', 'PA', '89207954000183', '(18) 99808-6573', '998086573', 'zitoas.etec@gmail.com', '17800000', '123', 'Jonathan Perroni', '2024-09-10 02:13:08'),
(39, 'Etec Elias Nechar', 'ensinoMedio', '054 - Etec Elias Nechar', 'av rio', 'cent', 'ada', 'PA', '39183908000113', '18996511409', '18996511409', 'z@gmail.com', '17800', '17', 'Jonathan Perroni', '2024-09-10 12:11:32'),
(40, 'Etec Lauro Gomes', 'tecnico', '010 - Etec Lauro Gomes', 'avenida geraldo', 'centro', 'Adamantina', 'MG', '08734162000165', '18996511409', '18996511409', 'jhowtest@gmail.com', '17800', '69', 'Jonathan Perroni', '2024-09-10 19:03:28'),
(41, 'Etec Prof. Camargo Aranha (Mooca)', 'tecnico', '012 - Etec Prof. Camargo Aranha (Mooca)', 'rua gera', 'todos', 'jurere', 'MS', '86919756000154', '18996511409', '18996511409', 'teste123@teste.com', '17888999', '10', 'Jonathan Perroni', '2024-09-16 20:13:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbfuncionarios`
--

CREATE TABLE `tbfuncionarios` (
  `codigo` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text DEFAULT NULL,
  `cpf` text DEFAULT NULL,
  `codigo_escola` text DEFAULT NULL,
  `acesso` text DEFAULT NULL,
  `cadastrado_por` varchar(50) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `codigo_autenticacao` varchar(8) DEFAULT NULL,
  `chave_recuperar_senha` varchar(220) DEFAULT NULL,
  `data_codigo_autenticacao` datetime DEFAULT NULL,
  `statusDev` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbfuncionarios`
--

INSERT INTO `tbfuncionarios` (`codigo`, `nome`, `email`, `password`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`, `cadastrado_por`, `data_cadastro`, `codigo_autenticacao`, `chave_recuperar_senha`, `data_codigo_autenticacao`, `statusDev`) VALUES
(1, 'Funcionário Teste', 'funcionario@email.com', '$2y$10$uhYMPZ./bkkDWNYhqHduteEpwAcWiBj3wniIf4V8VNYViGdCY9QdK', '', '18997005573', '06968841358', '055', 'funcionario', NULL, NULL, 'IXGzNg', NULL, '2025-02-16 02:27:58', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbjogoseducativos`
--

CREATE TABLE `tbjogoseducativos` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text NOT NULL,
  `classe` text NOT NULL,
  `titulo` text NOT NULL,
  `categoria` text DEFAULT NULL,
  `idade_minima` int(11) DEFAULT NULL,
  `num_jogadores` int(11) DEFAULT NULL,
  `fabricante` text DEFAULT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `estante` text DEFAULT NULL,
  `prateleira` text DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbjogoseducativos`
--

INSERT INTO `tbjogoseducativos` (`codigo`, `nome_escola`, `classe`, `titulo`, `categoria`, `idade_minima`, `num_jogadores`, `fabricante`, `data_adicao`, `estante`, `prateleira`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Jogo', 'Jogo Teste', 'Estratégia', 10, 2, 'Toys', '2024-07-04 00:42:51', '1', '1', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbjornal_revista`
--

CREATE TABLE `tbjornal_revista` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text NOT NULL,
  `classe` text NOT NULL,
  `titulo` text NOT NULL,
  `data_publicacao` date DEFAULT NULL,
  `editora` text DEFAULT NULL,
  `categoria` text DEFAULT NULL,
  `issn` text DEFAULT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `estante` text DEFAULT NULL,
  `prateleira` text DEFAULT NULL,
  `edicao` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbjornal_revista`
--

INSERT INTO `tbjornal_revista` (`codigo`, `nome_escola`, `classe`, `titulo`, `data_publicacao`, `editora`, `categoria`, `issn`, `data_adicao`, `estante`, `prateleira`, `edicao`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Jornal', 'Jornal Teste', '2021-03-01', 'Editora Teste', 'Categoria Teste', '123456', '2024-07-04 00:33:17', '1', '2', 2, 5),
(2, 'Prof Eudécio Luiz Vicente', 'Revista', 'Revista Teste', '2023-04-25', 'Editora Teste', 'Categoria Teste', '654321', '2024-07-04 00:33:43', '6', '1', 1, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tblivros`
--

CREATE TABLE `tblivros` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text DEFAULT NULL,
  `classe` text DEFAULT NULL,
  `titulo` text NOT NULL,
  `autor` text NOT NULL,
  `editora` text DEFAULT NULL,
  `ano_publicacao` year(4) DEFAULT NULL,
  `isbn` text DEFAULT NULL,
  `genero` text DEFAULT NULL,
  `num_paginas` int(11) DEFAULT NULL,
  `idioma` text DEFAULT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `estante` text DEFAULT NULL,
  `prateleira` text DEFAULT NULL,
  `edicao` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tblivros`
--

INSERT INTO `tblivros` (`codigo`, `nome_escola`, `classe`, `titulo`, `autor`, `editora`, `ano_publicacao`, `isbn`, `genero`, `num_paginas`, `idioma`, `data_adicao`, `estante`, `prateleira`, `edicao`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Livro', 'Livro Teste', 'Autor Teste', 'Editora Teste', '2002', '1234567890123', 'Educacional', 102, 'Português', '2024-07-04 00:43:52', '1', '1', 5, 2),
(2, 'Escola Exemplo', '1º Ano', 'A História do Mundo', 'Autor Exemplo', 'Editora Exemplo', '2024', '978-3-16-148410-0', 'História', 200, 'Português', '2025-02-16 06:43:34', 'A1', 'Prateleira 3', 0, 10),
(3, 'Escola Exemplo', '2º Ano', 'O Mundo da Ciência', 'Outro Autor', 'Outra Editora', '2023', '978-1-23-456789-0', 'Ciência', 250, 'Inglês', '2025-02-16 06:43:34', 'B2', 'Prateleira 5', 0, 15),
(4, 'Escola Exemplo', '3º Ano', 'Matemática para Todos', 'Mais um Autor', 'Mais uma Editora', '2022', '978-0-12-345678-9', 'Matemática', 180, 'Español', '2025-02-16 06:43:34', 'C3', 'Prateleira 7', 0, 20),
(5, 'Escola Exemplo', '4º Ano', 'Geografia do Mundo', 'Autor Geográfico', 'Editora Mundo', '2021', '978-9-87-654321-0', 'Geografia', 300, 'Português', '2025-02-16 06:43:34', 'D4', 'Prateleira 1', 0, 5),
(6, 'Escola Exemplo', '5º Ano', 'Física Experimental', 'Físico Cientista', 'Editora Ciência', '2020', '978-1-23-987654-3', 'Física', 320, 'Inglês', '2025-02-16 06:43:34', 'E5', 'Prateleira 6', 0, 12),
(7, 'Escola Exemplo', '6º Ano', 'Química Fácil', 'Químico Exemplo', 'Editora Química', '2019', '978-0-98-765432-1', 'Química', 250, 'Português', '2025-02-16 06:43:34', 'F6', 'Prateleira 2', 0, 7),
(8, 'Escola Exemplo', '7º Ano', 'Literatura Brasileira', 'Autor Literário', 'Editora Brasil', '2018', '978-0-12-345678-5', 'Literatura', 180, 'Português', '2025-02-16 06:43:34', 'G7', 'Prateleira 4', 0, 10),
(9, 'Escola Exemplo', '8º Ano', 'Psicologia e Educação', 'Psicólogo Educacional', 'Editora Psico', '2017', '978-0-56-987654-3', 'Psicologia', 220, 'Português', '2025-02-16 06:43:34', 'H8', 'Prateleira 8', 0, 8),
(10, 'Escola Exemplo', '9º Ano', 'Cultura e Sociedade', 'Autor Sociológico', 'Editora Cultura', '2016', '978-1-24-567890-1', 'Sociologia', 210, 'Português', '2025-02-16 06:43:34', 'I9', 'Prateleira 9', 0, 14),
(11, 'Escola Exemplo', '1º Ano', 'Introdução à Filosofia', 'Filósofo Moderno', 'Editora Filosofia', '2020', '978-2-13-456789-2', 'Filosofia', 230, 'Inglês', '2025-02-16 06:43:34', 'J1', 'Prateleira 3', 0, 20),
(12, 'Escola Exemplo', '2º Ano', 'Arte e Criatividade', 'Artista Exemplar', 'Editora Arte', '2015', '978-1-99-876543-2', 'Arte', 150, 'Inglês', '2025-02-16 06:43:34', 'K2', 'Prateleira 5', 0, 6),
(13, 'Escola Exemplo', '3º Ano', 'História da Arte', 'Historiador Artístico', 'Editora História', '2018', '978-0-87-654321-5', 'História', 280, 'Português', '2025-02-16 06:43:34', 'L3', 'Prateleira 7', 0, 12),
(14, 'Escola Exemplo', '4º Ano', 'Astronomia e o Universo', 'Astrônomo Grande', 'Editora Espaço', '2017', '978-3-14-567890-2', 'Astronomia', 200, 'Português', '2025-02-16 06:43:34', 'M4', 'Prateleira 2', 0, 9),
(15, 'Escola Exemplo', '5º Ano', 'Filosofia Contemporânea', 'Filosofador Atual', 'Editora Pensamento', '2022', '978-9-87-654987-3', 'Filosofia', 220, 'Português', '2025-02-16 06:43:34', 'N5', 'Prateleira 4', 0, 15),
(16, 'Escola Exemplo', '6º Ano', 'Biologia Marinha', 'Biólogo Marinho', 'Editora Oceano', '2023', '978-1-25-678901-2', 'Biologia', 250, 'Português', '2025-02-16 06:43:34', 'O6', 'Prateleira 6', 0, 18),
(17, 'Escola Exemplo', '7º Ano', 'Economia Básica', 'Economista Iniciante', 'Editora Economia', '2024', '978-2-56-789012-3', 'Economia', 180, 'Português', '2025-02-16 06:43:34', 'P7', 'Prateleira 1', 0, 10),
(18, 'Escola Exemplo', '8º Ano', 'Tecnologia e Inovação', 'Tecnólogo Futurista', 'Editora Tech', '2020', '978-3-21-654987-0', 'Tecnologia', 290, 'Inglês', '2025-02-16 06:43:34', 'Q8', 'Prateleira 9', 0, 8),
(19, 'Escola Exemplo', '9º Ano', 'Geopolítica Global', 'Autor Geopolítico', 'Editora Global', '2021', '978-1-21-543210-9', 'Geopolítica', 250, 'Português', '2025-02-16 06:43:34', 'R9', 'Prateleira 2', 0, 10),
(20, 'Escola Exemplo', '1º Ano', 'Geografia do Brasil', 'Geógrafo Brasileiro', 'Editora Brasil', '2019', '978-2-13-987654-0', 'Geografia', 200, 'Português', '2025-02-16 06:43:34', 'S1', 'Prateleira 3', 0, 7),
(21, 'Escola Exemplo', '2º Ano', 'Literatura Inglesa', 'Autor Britânico', 'Editora Londrina', '2018', '978-0-32-456789-4', 'Literatura', 300, 'Inglês', '2025-02-16 06:43:34', 'T2', 'Prateleira 4', 0, 11);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbmidias`
--

CREATE TABLE `tbmidias` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text NOT NULL,
  `classe` text NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `data_lancamento` date DEFAULT NULL,
  `genero` varchar(100) DEFAULT NULL,
  `diretor_artista` varchar(255) DEFAULT NULL,
  `duracao` text DEFAULT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `estante` text DEFAULT NULL,
  `prateleira` text DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbmidias`
--

INSERT INTO `tbmidias` (`codigo`, `nome_escola`, `classe`, `titulo`, `data_lancamento`, `genero`, `diretor_artista`, `duracao`, `data_adicao`, `estante`, `prateleira`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Midia', 'Midia Teste', '2006-05-02', 'Educacional', 'Artista Teste', '6:01', '2024-07-04 00:45:09', '1', '1', 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbprofessores`
--

CREATE TABLE `tbprofessores` (
  `codigo` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text DEFAULT NULL,
  `cpf` text DEFAULT NULL,
  `codigo_escola` text DEFAULT NULL,
  `acesso` text DEFAULT NULL,
  `cadastrado_por` varchar(50) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL,
  `codigo_autenticacao` varchar(8) DEFAULT NULL,
  `chave_recuperar_senha` varchar(220) DEFAULT NULL,
  `data_codigo_autenticacao` datetime DEFAULT NULL,
  `statusDev` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbprofessores`
--

INSERT INTO `tbprofessores` (`codigo`, `nome`, `email`, `password`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`, `cadastrado_por`, `data_cadastro`, `codigo_autenticacao`, `chave_recuperar_senha`, `data_codigo_autenticacao`, `statusDev`) VALUES
(1, 'Professor Teste', 'professor@teste.com', '$2y$10$vgDrWrxGXFLMhKdJlwoJcu.DBDEXxSgaYDwMSviPBkfCv0uaWfUiG', '', '18991599472', '09739704805', '055', 'professor', NULL, NULL, 'IXGzNg', NULL, '2025-02-16 02:27:58', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbtcc`
--

CREATE TABLE `tbtcc` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text NOT NULL,
  `classe` text NOT NULL,
  `titulo` text NOT NULL,
  `autor` text NOT NULL,
  `orientador` text NOT NULL,
  `curso` text NOT NULL,
  `ano` year(4) NOT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp(),
  `estante` text DEFAULT NULL,
  `prateleira` text DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tbtcc`
--

INSERT INTO `tbtcc` (`codigo`, `nome_escola`, `classe`, `titulo`, `autor`, `orientador`, `curso`, `ano`, `data_adicao`, `estante`, `prateleira`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Tcc', 'Tcc Teste', 'Felipe Ivasco', 'Professor André Luis', 'Desenvolvimento de Sistemas', '2024', '2024-07-04 00:45:48', '1', '2', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `dados_etec`
--
ALTER TABLE `dados_etec`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `historico_usuarios`
--
ALTER TABLE `historico_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tbadmin`
--
ALTER TABLE `tbadmin`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbalunos`
--
ALTER TABLE `tbalunos`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbbibliotecario`
--
ALTER TABLE `tbbibliotecario`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbclasse`
--
ALTER TABLE `tbclasse`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbcursos`
--
ALTER TABLE `tbcursos`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbdev`
--
ALTER TABLE `tbdev`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbemprestimos`
--
ALTER TABLE `tbemprestimos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tbescola`
--
ALTER TABLE `tbescola`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbfuncionarios`
--
ALTER TABLE `tbfuncionarios`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbjogoseducativos`
--
ALTER TABLE `tbjogoseducativos`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbjornal_revista`
--
ALTER TABLE `tbjornal_revista`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tblivros`
--
ALTER TABLE `tblivros`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbmidias`
--
ALTER TABLE `tbmidias`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbprofessores`
--
ALTER TABLE `tbprofessores`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `tbtcc`
--
ALTER TABLE `tbtcc`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `dados_etec`
--
ALTER TABLE `dados_etec`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de tabela `historico_usuarios`
--
ALTER TABLE `historico_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `tbadmin`
--
ALTER TABLE `tbadmin`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `tbalunos`
--
ALTER TABLE `tbalunos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tbbibliotecario`
--
ALTER TABLE `tbbibliotecario`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tbclasse`
--
ALTER TABLE `tbclasse`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tbcursos`
--
ALTER TABLE `tbcursos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `tbdev`
--
ALTER TABLE `tbdev`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `tbemprestimos`
--
ALTER TABLE `tbemprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tbescola`
--
ALTER TABLE `tbescola`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `tbfuncionarios`
--
ALTER TABLE `tbfuncionarios`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tbjogoseducativos`
--
ALTER TABLE `tbjogoseducativos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tbjornal_revista`
--
ALTER TABLE `tbjornal_revista`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tblivros`
--
ALTER TABLE `tblivros`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `tbmidias`
--
ALTER TABLE `tbmidias`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tbprofessores`
--
ALTER TABLE `tbprofessores`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tbtcc`
--
ALTER TABLE `tbtcc`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
