-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-08-2025 a las 05:29:25
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `plataforma_colegio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guias`
--

CREATE TABLE `guias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `curso` varchar(10) NOT NULL,
  `materia_id` int(11) DEFAULT NULL,
  `fecha_subida` date NOT NULL,
  `profesor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `guias`
--

INSERT INTO `guias` (`id`, `titulo`, `archivo`, `curso`, `materia_id`, `fecha_subida`, `profesor_id`) VALUES
(3, 'bla', '1753652597_Doc1 (1).pdf', '5°2°', NULL, '2025-07-29', 15),
(7, 'bla', 'guia_688b0be1e1faa_presentacion_fiel_simple.pdf', '6°2°', NULL, '2025-07-31', 15),
(8, 'dddd', 'guia_688b0c0882ef5_presentacion_final_pasantia (1).pdf', '7°2°', NULL, '2025-07-31', 16),
(11, 'hjguyf', 'guia_689d4f6d38a95_Argumentacion_Proyecto_Empresa_Alba.pdf', '5°2°', 2, '2025-08-13', 15),
(12, 'hjguyf', 'guia_689d570026d20_Argumentacion_Proyecto_Empresa_Alba.pdf', '7°2°', 4, '2025-08-14', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `activo`) VALUES
(1, 'sistema', 1),
(2, 'matematica', 1),
(3, 'lengua', 1),
(4, 'practica profesionalizante', 1),
(5, 'mantenimiento de hardware', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `materia_id` int(11) DEFAULT NULL,
  `nota` decimal(5,2) NOT NULL,
  `trimestre` tinyint(4) DEFAULT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `alumno_id`, `profesor_id`, `materia_id`, `nota`, `trimestre`, `fecha`) VALUES
(4, 16, 15, NULL, 10.00, 2, '2025-07-29'),
(9, 21, 15, NULL, 7.00, 2, '2025-07-31'),
(10, 18, 16, NULL, 9.00, 2, '2025-07-31'),
(14, 18, 15, 4, 9.00, 1, '2025-08-14'),
(15, 22, 15, 4, 7.00, 1, '2025-08-14'),
(16, 18, 15, 4, 8.00, 1, '2025-08-14'),
(17, 22, 15, 4, 6.00, 1, '2025-08-14'),
(18, 22, 15, 4, 10.00, 1, '2025-08-14'),
(19, 18, 15, 4, 6.00, 1, '2025-08-14'),
(20, 18, 15, 4, 10.00, 1, '2025-08-14'),
(21, 18, 15, 4, 10.00, 2, '2025-08-14'),
(22, 18, 15, 4, 8.00, 3, '2025-08-14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_curso`
--

CREATE TABLE `profesor_curso` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `curso` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor_curso`
--

INSERT INTO `profesor_curso` (`id`, `profesor_id`, `curso`) VALUES
(2, 15, '7°2°'),
(3, 15, '6°2°'),
(4, 15, '5°2°'),
(5, 15, '4°2°'),
(6, 16, '6°2°'),
(7, 16, '5°2°'),
(8, 16, '7°2°');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_materia`
--

CREATE TABLE `profesor_materia` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `curso` varchar(10) NOT NULL,
  `materia_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor_materia`
--

INSERT INTO `profesor_materia` (`id`, `profesor_id`, `curso`, `materia_id`) VALUES
(6, 15, '5°2°', 2),
(13, 15, '7°2°', 1),
(11, 15, '7°2°', 3),
(12, 15, '7°2°', 4),
(3, 16, '1°1°', 2),
(2, 16, '1°1°', 3),
(16, 16, '7°2°', 2),
(15, 16, '7°2°', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `curso` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('admin','profesor','alumno') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `nombre`, `apellido`, `dni`, `curso`, `email`, `password`, `rol`) VALUES
(15, 'david', 'david', 'segura', '12345679', '7°2°', 'monardezalejo@gmail.com', '$2y$10$csnl.Njd.m5dJ6puIn1swO6V3MlBDCdddSlDbvCNo7JjqmQA.fxWm', 'profesor'),
(16, 'caritadebebe', 'ariel', 'gonzales', '11111111', NULL, 'carabebe@gmail.com', '$2y$10$KT8fMGj6LmMlfdSW7QCg0uAReo8TlN9.jt4nta2VDo4Au0lN5NTfm', 'profesor'),
(18, 'alejo', 'Alejo', 'Monardez', '47815371', '7°2°', 'monardezalejo@gmail.com', '$2y$10$azSJXgNF6qD2ftw1qj9sXOymZndfgojFMoV5e0YGMwKfOD4lvH5/q', 'alumno'),
(20, 'admin', 'Administrador', 'Principal', '12345678', '', 'admin@colegio.com', '$2y$10$4jOdNmbw8UmrsRzy77LpOufKvnMsZetFLfgSLbKKje4PYkzNJW71C', 'admin'),
(21, 'yoandrea', 'andrea', 'vega', '33333333', '6°2°', 'konanakatsuki@gmail.com', '$2y$10$bzpYdI9p8OlZaW4PtzOnYu/0m.i06HJfvpnmBKGYOe0dsXdvpCRna', 'alumno'),
(22, 'fabrii', 'fabricio', 'aciar', '22222222', '7°2°', 'fabri@gmail.com', '$2y$10$jzMxo9F7ne5lxxAGfd/EK.euPjix2sauW30bAgGgDdeuK4WGbzRYW', 'alumno'),
(23, 'scorpion', 'german', 'castro', '12345676', '7°2°', NULL, '$2y$10$DYtyO2OHjlR1V2zWTmiitOj8o6ivDFFfkPyONxy/ICuJQSMSEfdi.', 'alumno');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `guias`
--
ALTER TABLE `guias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_guias_profesor` (`profesor_id`),
  ADD KEY `idx_guias_curso` (`curso`),
  ADD KEY `idx_guias_materia` (`materia_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `idx_notas_profesor` (`profesor_id`),
  ADD KEY `idx_notas_alumno` (`alumno_id`),
  ADD KEY `idx_notas_materia` (`materia_id`),
  ADD KEY `idx_notas_trimestre` (`trimestre`);

--
-- Indices de la tabla `profesor_curso`
--
ALTER TABLE `profesor_curso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `profesor_materia`
--
ALTER TABLE `profesor_materia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_prof_curso_mat` (`profesor_id`,`curso`,`materia_id`),
  ADD KEY `fk_pm_materia` (`materia_id`),
  ADD KEY `idx_pm_profesor_curso` (`profesor_id`,`curso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `guias`
--
ALTER TABLE `guias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `profesor_curso`
--
ALTER TABLE `profesor_curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `profesor_materia`
--
ALTER TABLE `profesor_materia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `guias`
--
ALTER TABLE `guias`
  ADD CONSTRAINT `fk_guias_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `fk_notas_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `profesor_curso`
--
ALTER TABLE `profesor_curso`
  ADD CONSTRAINT `profesor_curso_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesor_materia`
--
ALTER TABLE `profesor_materia`
  ADD CONSTRAINT `fk_pm_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pm_profesor` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
