-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17-Set-2024 às 01:38
-- Versão do servidor: 10.4.21-MariaDB
-- versão do PHP: 8.0.10

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
-- Estrutura da tabela `dados_etec`
--

CREATE TABLE `dados_etec` (
  `codigo` int(11) NOT NULL,
  `codigo_escola` varchar(50) NOT NULL,
  `unidadeEscola` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `dados_etec`
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
-- Estrutura da tabela `tbadmin`
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
  `data_cadastro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbadmin`
--

INSERT INTO `tbadmin` (`codigo`, `nome`, `email`, `password`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`, `cadastrado_por`, `data_cadastro`) VALUES
(5, 'Jonathan ', 'todosTeste@gmail.com', '$2y$10$3krq./upHuosp7NfcBCuROUKuEtlIS.xsqb9Qd1e2585IO8LGfeNe', '18996511409', '18996511409', '93260770046', 35, 'admin', 'Jonathan Perroni', '2024-09-10 19:34:28'),
(6, 'Jonathan Perroni', 'jhow.zitos@gmail.com', '$2y$10$6l8mdwZsx9gCRNmEpnSu1ea1GNiOLwATJlSKHqSGvNdlcMcnkOtmy', '18996511409', '18996511409', '36844808860', 55, 'admin', 'Jonathan Perroni', '2024-09-10 19:39:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbalunos`
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
  `senha` varchar(255) NOT NULL,
  `acesso` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbalunos`
--

INSERT INTO `tbalunos` (`codigo`, `nome`, `data_nascimento`, `endereco`, `cidade`, `estado`, `cpf`, `celular`, `periodo`, `situacao`, `responsavel`, `nome_escola`, `nome_curso`, `email`, `tipo_ensino`, `senha`, `acesso`) VALUES
(1, 'Aluno Teste da Silva', '1994-11-22', 'Rua Teste, 552', 'Adamantina', 'SP', '413.735.168', '18991599472', 'noite', 'a cursar', 'Maria Teste', 'Prof Eudécio Luiz Vicente', 'Desenvolvimento de Sistemas', 'aluno@teste.com', 'tecnico', '$2y$10$WzjtDMaL0J/2vHdMx1mfiePOpTZhqGKkY6YpRTbW3CtiwKk8sw/LG', 'aluno');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbbibliotecario`
--

CREATE TABLE `tbbibliotecario` (
  `codigo` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `senha` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text DEFAULT NULL,
  `cpf` text DEFAULT NULL,
  `codigo_escola` text DEFAULT NULL,
  `acesso` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbbibliotecario`
--

INSERT INTO `tbbibliotecario` (`codigo`, `nome`, `email`, `senha`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`) VALUES
(1, 'Funcionário Teste', 'teste@email.com', '$2y$10$2v8Rw0H.iv.2m0fIjJPGle2jfEBHjv9.WzwJaAzUp27viBSnvNm9W', '', '18991599472', '46533043862', '055', 'bibliotecario');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbclasse`
--

CREATE TABLE `tbclasse` (
  `codigo` int(11) NOT NULL,
  `classe` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbclasse`
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
-- Estrutura da tabela `tbcursos`
--

CREATE TABLE `tbcursos` (
  `codigo` int(11) NOT NULL,
  `nome_escola` text DEFAULT NULL,
  `nome_curso` text NOT NULL,
  `tempo_curso` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbcursos`
--

INSERT INTO `tbcursos` (`codigo`, `nome_escola`, `nome_curso`, `tempo_curso`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Desenvolvimento de Sistemas', '3 semestres'),
(2, 'Prof Eudécio Luiz Vicente', 'Açúcar e Álcool', '3 semestres'),
(3, 'Prof Eudécio Luiz Vicente', 'Administração', '3 semestres'),
(4, 'Prof Eudécio Luiz Vicente', 'Contabilidade', '3 semestres'),
(5, 'Prof Eudécio Luiz Vicente', 'Enfermagem', '4 semestres'),
(6, 'Prof Eudécio Luiz Vicente', 'Ensino Médio', '6 semestres'),
(7, 'Prof Eudécio Luiz Vicente', 'Ensino Médio Integrado', '6 semestres'),
(8, 'Prof Eudécio Luiz Vicente', 'Informática para Internet', '3 semestres'),
(9, 'Prof Eudécio Luiz Vicente', 'Logística', '3 semestres'),
(10, 'Prof Eudécio Luiz Vicente', 'Mecânica', '3 semestres'),
(11, 'Prof Eudécio Luiz Vicente', 'Secretariado - EAD', '2 semestres'),
(12, 'Prof Eudécio Luiz Vicente', 'Segurança do Trabalho', '3 semestres');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbdev`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbdev`
--

INSERT INTO `tbdev` (`codigo`, `nome`, `cpf`, `email`, `password`, `telefone`, `celular`, `acesso`, `codigo_autenticacao`, `chave_recuperar_senha`, `data_codigo_autenticacao`, `statusDev`) VALUES
(3, 'Felipe Ivasco de Almeida', '41373516860', 'ivascofelipe@gmail.com', '$2y$10$8E7Xo3Ab1WgHyJFdWv8SeuMJJ9gcCVOtIxbFRf5AD9QM3/UbaI1me', '18991599472', '18991599472', 'Desenvolvedor', NULL, NULL, NULL, 0),
(8, 'Jonathan Perroni', '36844808860', 'jhow.zitos@gmail.com', '$2y$10$acOGKPYw00.2TXdxYnR26unHL3p55h2i5n8nojDLVM18SjfchouD.', '18996511409', '18996511409', 'Desenvolvedor', NULL, NULL, NULL, 1),
(9, 'Fernanda  Stanislaw', '00959139044', 'fer.stanislaw.fs@gmail.com', '$2y$10$uit8efwwTtyLbO.Gz8jx6O4MpWGVBfXi5lLGnHbOZGJt/tD2guBbS', '(18) 99612-2396', '(18) 99612-2396', 'Desenvolvedor', NULL, NULL, NULL, 1),
(10, 'Jose Eduardo ', '10466400039', 'joseeduardo@gmail.com', '$2y$10$Tvxv78PFHdM9XYslGSL7zOEaKV0BDg210L9MqYTgG1fcHWFi8NIUm', '18998086573', '18991599472', 'Desenvolvedor', NULL, '$2y$10$lXRvYIfvb2hm9V8knIQKQetIhZ742icgTzZemCZjdYvM8CHxO9FgO', NULL, 1),
(11, 'supremeDev', '93608124047', 'supremeDev@gmail.com', '$2y$10$mP5t4HdOlM/ooQqfr2zHNOIIgL/Tf3BoCIBK3iQk..eiyrjbPdzDS', '18996511409', '18996511409', 'Desenvolvedor', NULL, NULL, NULL, 0),
(18, 'teste94', '16702118007', 'teste@teste.com', '$2y$10$sj0eIO6ke3atqLOLXtIPvOqYAtepND7wGzolwOWO5QTOEB5LeJogS', '18996511409', '18999999999', 'Desenvolvedor', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbescola`
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
  `cep` int(10) NOT NULL,
  `numero` int(10) NOT NULL,
  `cadastrado_por` varchar(220) NOT NULL,
  `data_cadastro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbescola`
--

INSERT INTO `tbescola` (`codigo`, `nome_escola`, `tipoEscola`, `codigo_escola`, `endereco`, `bairro`, `cidade`, `estado`, `cnpj`, `telefone`, `celular`, `email`, `cep`, `numero`, `cadastrado_por`, `data_cadastro`) VALUES
(35, 'Etec Júlio de Mesquita', 'ensinoMedio', '014 - Etec Júlio de Mesquita', 'Av. ademar de barro, 489 ap 3', 'centro', 'Adamantina', 'SP', '95068841000147', '(18) 99808-6573', '(18) 99612-2396', 'zitos.etec@gmail.com', 17800000, 123, 'Jonathan Perroni', '2024-09-10 01:45:47'),
(36, 'Etec Jorge Street', 'ensinoMedio', '011 - Etec Jorge Street', 'Av. ademar de barro, 489 ap 3', 'centro', 'Adamantina', 'SP', '78430640000129', '(18) 99808-6573', '(18) 99612-2396', 'zitos.etec@gmgail.com', 17800000, 123, 'Jonathan Perroni', '2024-09-10 01:46:36'),
(37, 'Etec Prof. Basílides de Godoy (Vila Leopoldina)', 'ensinoMedio', '041 - Etec Prof. Basílides de Godoy (Vila Leopoldi', 'Av. ademar de barro, 489 ap 3', 'centro', 'Adamantina', 'SP', '04364091000103', '18996511409', '998086573', 'zitos.eteca@gmail.com', 17800000, 144, 'Jonathan Perroni', '2024-09-10 01:47:46'),
(38, 'Etec João Jorge Geraissate', 'tecnico', '069 - Etec João Jorge Geraissate', 'Av. ademar de barro, 489 ap 3', 'centro', 'Adamantina', 'PA', '89207954000183', '(18) 99808-6573', '998086573', 'zitoas.etec@gmail.com', 17800000, 123, 'Jonathan Perroni', '2024-09-10 02:13:08'),
(39, 'Etec Elias Nechar', 'ensinoMedio', '054 - Etec Elias Nechar', 'av rio', 'cent', 'ada', 'PA', '39183908000113', '18996511409', '18996511409', 'z@gmail.com', 17800, 17, 'Jonathan Perroni', '2024-09-10 12:11:32'),
(40, 'Etec Lauro Gomes', 'tecnico', '010 - Etec Lauro Gomes', 'avenida geraldo', 'centro', 'Adamantina', 'MG', '08734162000165', '18996511409', '18996511409', 'jhowtest@gmail.com', 17800, 69, 'Jonathan Perroni', '2024-09-10 19:03:28'),
(41, 'Etec Prof. Camargo Aranha (Mooca)', 'tecnico', '012 - Etec Prof. Camargo Aranha (Mooca)', 'rua gera', 'todos', 'jurere', 'MS', '86919756000154', '18996511409', '18996511409', 'teste123@teste.com', 17888999, 10, 'Jonathan Perroni', '2024-09-16 20:13:36');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbfuncionarios`
--

CREATE TABLE `tbfuncionarios` (
  `codigo` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `senha` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text DEFAULT NULL,
  `cpf` text DEFAULT NULL,
  `codigo_escola` text DEFAULT NULL,
  `acesso` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbfuncionarios`
--

INSERT INTO `tbfuncionarios` (`codigo`, `nome`, `email`, `senha`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`) VALUES
(1, 'Funcionário Teste', 'funcionario@email.com', '$2y$10$uhYMPZ./bkkDWNYhqHduteEpwAcWiBj3wniIf4V8VNYViGdCY9QdK', '', '18997005573', '06968841358', '055', 'funcionario');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbjogoseducativos`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbjogoseducativos`
--

INSERT INTO `tbjogoseducativos` (`codigo`, `nome_escola`, `classe`, `titulo`, `categoria`, `idade_minima`, `num_jogadores`, `fabricante`, `data_adicao`, `estante`, `prateleira`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Jogo', 'Jogo Teste', 'Estratégia', 10, 2, 'Toys', '2024-07-04 00:42:51', '1', '1', 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbjornal_revista`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbjornal_revista`
--

INSERT INTO `tbjornal_revista` (`codigo`, `nome_escola`, `classe`, `titulo`, `data_publicacao`, `editora`, `categoria`, `issn`, `data_adicao`, `estante`, `prateleira`, `edicao`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Jornal', 'Jornal Teste', '2021-03-01', 'Editora Teste', 'Categoria Teste', '123456', '2024-07-04 00:33:17', '1', '2', 2, 5),
(2, 'Prof Eudécio Luiz Vicente', 'Revista', 'Revista Teste', '2023-04-25', 'Editora Teste', 'Categoria Teste', '654321', '2024-07-04 00:33:43', '6', '1', 1, 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tblivros`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tblivros`
--

INSERT INTO `tblivros` (`codigo`, `nome_escola`, `classe`, `titulo`, `autor`, `editora`, `ano_publicacao`, `isbn`, `genero`, `num_paginas`, `idioma`, `data_adicao`, `estante`, `prateleira`, `edicao`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Livro', 'Livro Teste', 'Autor Teste', 'Editora Teste', 2002, '123456', 'Educacional', 102, 'Português', '2024-07-04 00:43:52', '1', '1', 5, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbmidias`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbmidias`
--

INSERT INTO `tbmidias` (`codigo`, `nome_escola`, `classe`, `titulo`, `data_lancamento`, `genero`, `diretor_artista`, `duracao`, `data_adicao`, `estante`, `prateleira`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Midia', 'Midia Teste', '2006-05-02', 'Educacional', 'Artista Teste', '6:01', '2024-07-04 00:45:09', '1', '1', 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbprofessores`
--

CREATE TABLE `tbprofessores` (
  `codigo` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `senha` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `celular` text DEFAULT NULL,
  `cpf` text DEFAULT NULL,
  `codigo_escola` text DEFAULT NULL,
  `acesso` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbprofessores`
--

INSERT INTO `tbprofessores` (`codigo`, `nome`, `email`, `senha`, `telefone`, `celular`, `cpf`, `codigo_escola`, `acesso`) VALUES
(1, 'Professor Teste', 'professor@teste.com', '$2y$10$vgDrWrxGXFLMhKdJlwoJcu.DBDEXxSgaYDwMSviPBkfCv0uaWfUiG', '', '18991599472', '09739704805', '055', 'professor');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbtcc`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tbtcc`
--

INSERT INTO `tbtcc` (`codigo`, `nome_escola`, `classe`, `titulo`, `autor`, `orientador`, `curso`, `ano`, `data_adicao`, `estante`, `prateleira`, `quantidade`) VALUES
(1, 'Prof Eudécio Luiz Vicente', 'Tcc', 'Tcc Teste', 'Felipe Ivasco', 'Professor André Luis', 'Desenvolvimento de Sistemas', 2024, '2024-07-04 00:45:48', '1', '2', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `dados_etec`
--
ALTER TABLE `dados_etec`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbadmin`
--
ALTER TABLE `tbadmin`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbalunos`
--
ALTER TABLE `tbalunos`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbbibliotecario`
--
ALTER TABLE `tbbibliotecario`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbclasse`
--
ALTER TABLE `tbclasse`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbcursos`
--
ALTER TABLE `tbcursos`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbdev`
--
ALTER TABLE `tbdev`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbescola`
--
ALTER TABLE `tbescola`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbfuncionarios`
--
ALTER TABLE `tbfuncionarios`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbjogoseducativos`
--
ALTER TABLE `tbjogoseducativos`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbjornal_revista`
--
ALTER TABLE `tbjornal_revista`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tblivros`
--
ALTER TABLE `tblivros`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbmidias`
--
ALTER TABLE `tbmidias`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbprofessores`
--
ALTER TABLE `tbprofessores`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `tbtcc`
--
ALTER TABLE `tbtcc`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `dados_etec`
--
ALTER TABLE `dados_etec`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de tabela `tbadmin`
--
ALTER TABLE `tbadmin`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tbalunos`
--
ALTER TABLE `tbalunos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tbbibliotecario`
--
ALTER TABLE `tbbibliotecario`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tbclasse`
--
ALTER TABLE `tbclasse`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tbcursos`
--
ALTER TABLE `tbcursos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `tbdev`
--
ALTER TABLE `tbdev`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `tbescola`
--
ALTER TABLE `tbescola`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
