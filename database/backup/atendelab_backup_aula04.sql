-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 24/06/2026 às 02:06
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
-- Banco de dados: `atendelab`
--

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
  `status` enum('aberto','em_andamento','concluido') DEFAULT 'aberto',
  `data_atendimento` date NOT NULL DEFAULT '2024-01-01',
  `horario_atendimento` time NOT NULL DEFAULT '08:00:00',
  `observacao_final` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `pessoa_id`, `tipo_atendimento_id`, `usuario_id`, `descricao`, `status`, `data_atendimento`, `horario_atendimento`, `observacao_final`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 1, 3, 'atendimento vip', '', '2024-01-01', '08:00:00', NULL, '2026-06-10 00:20:47', '2026-06-24 00:01:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `documento` varchar(30) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `curso` varchar(120) DEFAULT NULL,
  `periodo` varchar(20) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `documento`, `telefone`, `email`, `curso`, `periodo`, `observacoes`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'joaozinho atualizado', '', '40028922', 'joaozinho123@gmail.com', NULL, NULL, NULL, 'ativo', '2026-06-10 00:04:25', '2026-06-23 23:56:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_atendimentos`
--

CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_atendimentos`
--

INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, '', 'consultoria academica atualizada', 'ativo', '2026-06-10 00:14:25', '2026-06-23 23:58:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador', 'admin@atendelab.com', '$2y$10$J9P2kU2BAMZ3TZcuxTsW4e1D/lka8EocYHzvyoOZmCNcWDQz3RuVC', 'admin', 'ativo', '2026-06-08 22:20:08', '2026-06-23 23:53:12'),
(3, 'gustavo atualizado', 'gustavoteste@gmail.com', '$2y$10$ZiR0Aw/TRMBG66J6D/NaCunPA4JRumiDrdxAdpJAjd6NOGsDefTQu', 'admin', 'ativo', '2026-06-08 23:07:22', '2026-06-23 23:53:12');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atendimentos_usuarios` (`usuario_id`),
  ADD KEY `fk_atendimentos_pessoas` (`pessoa_id`),
  ADD KEY `fk_atendimentos_tipos` (`tipo_atendimento_id`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- Índices de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_atendimentos_pessoas` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`),
  ADD CONSTRAINT `fk_atendimentos_tipos` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`),
  ADD CONSTRAINT `fk_atendimentos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
