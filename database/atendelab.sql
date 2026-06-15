-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Banco de dados: `atendelab`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- ADICIONADO: Limpa as tabelas antes de recriar para evitar erro de duplicação
DROP TABLE IF EXISTS `atendimentos`, `tipos_atendimentos`, `pessoas`, `usuarios`;
-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','aluno','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- O INSERT manual com a senha 'root' foi removido propositalmente.
-- Crie o usuário admin@atendelab.com pelo Thunder Client.

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--
CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo` enum('aluno','professor','servidor') DEFAULT 'aluno',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_atendimentos`
--
CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos`
--
CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `tipo_atendimento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('pendente','em_andamento','concluido','cancelado') DEFAULT 'pendente',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`);

ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atend_pessoa` (`pessoa_id`),
  ADD KEY `fk_atend_tipo` (`tipo_atendimento_id`),
  ADD KEY `fk_atend_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_atend_pessoa` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`),
  ADD CONSTRAINT `fk_atend_tipo` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`),
  ADD CONSTRAINT `fk_atend_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
