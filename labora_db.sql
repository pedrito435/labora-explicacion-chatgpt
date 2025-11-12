-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2025 a las 18:27:58
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
-- Base de datos: `labora_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_admin` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_admin`, `usuario`, `clave`) VALUES
(1, 'enzo', '$2y$10$JUIPX0fd4WED0np9tiqsOu/lVp3aerHRUWcnUiLT.kbjC1LLfGDMq'),
(2, 'jose', '$2y$10$G5AhOTkmX2h9QRXFpW0ukeqD9m0q20yoKkTWjMf9B2FtcAL8SpllC'),
(3, 'alan', '1234'),
(4, 'santiago', '$2y$10$7S7/MD8amg2/Txj81uIrbeyPoYUF8AgB/uNhFx038VoYB0NAh3dUO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `educacion`
--

CREATE TABLE `educacion` (
  `id_educacion` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `institucion` varchar(255) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `educacion`
--

INSERT INTO `educacion` (`id_educacion`, `id_empleado`, `titulo`, `institucion`, `fecha_inicio`, `fecha_fin`) VALUES
(6, 39, 'tecnico en informatica personal y profesional', 'Escuela Tecnica Numero 5', '0000-00-00', '2025-11-24'),
(7, 49, '', '', '0000-00-00', '0000-00-00'),
(8, 50, '', '', '0000-00-00', '0000-00-00'),
(9, 51, '', '', '0000-00-00', '0000-00-00'),
(10, 52, '', '', '0000-00-00', '0000-00-00'),
(11, 53, '', '', '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL,
  `plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plan_expira` datetime DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `profesion` varchar(100) DEFAULT NULL,
  `experiencia_años` int(11) DEFAULT NULL,
  `descripcion_servicios` text DEFAULT NULL,
  `disponibilidad` varchar(100) DEFAULT NULL,
  `precio_hora` decimal(10,2) DEFAULT NULL,
  `zona_trabajo` varchar(100) DEFAULT NULL,
  `dni` varchar(25) DEFAULT NULL,
  `fecha_nacimiento` varchar(25) DEFAULT NULL,
  `nacionalidad` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `titulo_profesional` varchar(255) DEFAULT NULL,
  `habilidades` varchar(255) DEFAULT NULL,
  `educacion` varchar(255) DEFAULT NULL,
  `experiencia` varchar(255) DEFAULT NULL,
  `portafolio` varchar(255) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `portafolio_link` varchar(255) DEFAULT NULL,
  `reset_token_hash` char(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `estado_verificacion` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `verificado_por` int(11) DEFAULT NULL,
  `fecha_verificacion` datetime DEFAULT NULL,
  `observaciones_verificacion` text DEFAULT NULL,
  `dni_frente_path` varchar(255) DEFAULT NULL,
  `dni_dorso_path` varchar(255) DEFAULT NULL,
  `matricula_path` varchar(255) DEFAULT NULL,
  `email_change_token_hash` varchar(64) DEFAULT NULL,
  `email_change_expires` datetime DEFAULT NULL,
  `email_change_new` varchar(255) DEFAULT NULL,
  `email_verificado` tinyint(1) NOT NULL DEFAULT 1,
  `email_confirm_token_hash` varchar(64) DEFAULT NULL,
  `email_confirm_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `plan_id`, `plan_expira`, `nombre`, `correo`, `clave`, `profesion`, `experiencia_años`, `descripcion_servicios`, `disponibilidad`, `precio_hora`, `zona_trabajo`, `dni`, `fecha_nacimiento`, `nacionalidad`, `telefono`, `titulo_profesional`, `habilidades`, `educacion`, `experiencia`, `portafolio`, `foto_perfil`, `portafolio_link`, `reset_token_hash`, `reset_expires`, `estado_verificacion`, `verificado_por`, `fecha_verificacion`, `observaciones_verificacion`, `dni_frente_path`, `dni_dorso_path`, `matricula_path`, `email_change_token_hash`, `email_change_expires`, `email_change_new`, `email_verificado`, `email_confirm_token_hash`, `email_confirm_expires`) VALUES
(39, 1, NULL, 'enzo santino mamani cuba', 'labora1357@gmail.com', '$2y$10$olnPTJwRLHYiQLVuNIh.AOJTUOK2rB.6TX/w0hJgCiozbeYeB09pi', 'Carpinteria', 5, 'Soy excelente en mi profesión de carpintero.', 'Full time', 2500.00, 'Merlo', '47161648', '2006-01-26', 'Argentina', '1164718626', NULL, 'Resolución de problemas, Licencia de conducir', NULL, NULL, '', 'pf_68c9ae637f5350.62357190.jpg', '', NULL, NULL, 'aprobado', 1, '2025-09-15 15:40:02', '', 'uploads/verificaciones/empleado_39/doc_68c85385c05480.74623229.jpg', 'uploads/verificaciones/empleado_39/doc_68c85385c0a027.98148134.jpg', 'uploads/verificaciones/empleado_39/doc_68c85385c0d2d5.10723924.jpg', '5a6c2ec702f7f2186caedf506fe9cb84ec16ba0bd3fe36f554bc8cd3fc635460', '2025-09-30 21:44:21', 'santinomam@gmail.com', 1, NULL, NULL),
(41, 1, NULL, 'Santiago Silvera', 'santiagosilvera760@gmail.com', '$2y$10$6l1wfTiQY9tV7F5JMb1Ow..fXJnZYJVynpVtfYFz9nS/aKxRxYtc.', 'carpinteria', 5, NULL, NULL, NULL, 'Merlo', '47557438', '2006-10-19', 'Argentina', '1154094994', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'rechazado', 4, '2025-09-20 13:59:12', 'sos un pajero', 'uploads/verificaciones/empleado_41/doc_68cdb09cdfdd87.29275463.png', 'uploads/verificaciones/empleado_41/doc_68cdb09ce05221.14315446.png', 'uploads/verificaciones/empleado_41/doc_68cdb09ce09fd0.12989278.png', NULL, NULL, NULL, 1, NULL, NULL),
(42, 1, NULL, 'Matias Alejandro Silvera', 'msilvera383@gmail.com', '$2y$10$2YIc7to2ZC0vKQrhTnYb8eah/sxmQSNAGYeO6Gi.Ny8KifcCJfj3m', 'fletero', 10, NULL, NULL, NULL, 'Merlo', '32161888', '1986-04-03', 'Argentina', '1133191608', 'Fletero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aprobado', 4, '2025-09-20 13:57:57', '', 'uploads/verificaciones/empleado_42/doc_68cedbd0bec8b4.74148963.jpg', 'uploads/verificaciones/empleado_42/doc_68cedbd0c85ca2.90448488.jpg', 'uploads/verificaciones/empleado_42/doc_68cedbd0c890c3.82432218.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(43, 1, NULL, 'Anabela Soledad Zizza', 'anizizza20@gmail.com', '$2y$10$iW0YmL6pFgOZq6sBKrK7Ie/QHqQjhvRPZfKo4C3a84XYVNLqGi4Ia', 'jardineria', 10, NULL, NULL, NULL, 'Merlo', '35607932', '1991-02-13', 'Argentina', '1133181824', 'Jardinera', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aprobado', 4, '2025-09-20 14:05:12', '', 'uploads/verificaciones/empleado_43/doc_68cede1f190fb0.47627196.jpg', 'uploads/verificaciones/empleado_43/doc_68cede1f198272.11623832.jpg', 'uploads/verificaciones/empleado_43/doc_68cede1f19b1c8.57348262.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(44, 1, NULL, 'Maria Julieta Venegas', 'blackxeno2000@gmail.com', '$2y$10$18aWsUr8kk636DmC5qAoeOl9XwaguIV.fP2mOpyB8aB56oM.HKZNe', 'educacion', 15, NULL, NULL, NULL, 'Merlo', '34568536', '1987-01-14', 'Argentina', '1154547634', 'Docente', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aprobado', 4, '2025-09-20 16:36:36', '', 'uploads/verificaciones/empleado_44/doc_68cf01c7f25096.99094887.jpg', 'uploads/verificaciones/empleado_44/doc_68cf01c805bda1.00421942.jpg', 'uploads/verificaciones/empleado_44/doc_68cf01c8062636.17020718.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(45, 1, NULL, 'Juan Domingo Sanchez', 'shitindirectas@gmail.com', '$2y$10$eE/VhQCgOl4gZxRc8Df64OWoQcxE3quH2rsf1Yyjw6sYOjeNr68Xm', 'electricidad', 20, NULL, NULL, NULL, 'Merlo', '23685684', '1969-10-14', 'Argentina', '1153095123', 'Electricista', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aprobado', 4, '2025-09-20 16:42:55', '', 'uploads/verificaciones/empleado_45/doc_68cf035be9abc1.93872829.png', 'uploads/verificaciones/empleado_45/doc_68cf035bea0908.04381989.png', 'uploads/verificaciones/empleado_45/doc_68cf035bea4d95.12718176.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(49, 1, NULL, 'Ana Gómez', 'melsfantasystore@gmail.com', '$2y$10$4CIPyBavaE.gZ6EpGuAoq.8lxWP.F8BGZKRa6E0PSYwfqleVers.W', 'carpinteria', 5, 'Soy bastante competente', NULL, NULL, 'Padua', '29876543', '1995-06-15', 'Argentina', '1122334455', NULL, '', NULL, NULL, '', 'pf_68d4103c25f413.64421303.jpg', '', NULL, NULL, 'pendiente', NULL, NULL, NULL, 'uploads/verificaciones/empleado_49/doc_68d40f97399709.97362636.png', 'uploads/verificaciones/empleado_49/doc_68d40f973a2cc5.22585196.png', 'uploads/verificaciones/empleado_49/doc_68d40f973acef6.14113194.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(50, 1, NULL, 'Santiago Sanchez', 'exaltacioncuba18@gmail.com', '$2y$10$ZlwLjUEt557RwW316WQXxO0CeVa7oNMMsjXyMEWxlYUwAMU0BWnaK', 'plomeria', 5, '', NULL, NULL, 'Padua', '32123456', '1995-06-15', 'Argentina', '1122334455', 'Tecnico', '', NULL, NULL, '', 'pf_68d410ce223987.13014510.jpg', '', NULL, NULL, 'aprobado', 1, '2025-09-24 12:39:18', '', 'uploads/verificaciones/empleado_50/doc_68d41079defdb2.75522720.jpg', 'uploads/verificaciones/empleado_50/doc_68d41079df6f90.37822570.jpg', 'uploads/verificaciones/empleado_50/doc_68d41079dfc214.72510057.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(51, 1, NULL, 'Juan Pérez', 'compraura@gmail.com', '$2y$10$DGfyOmwon7XnIUri1P7OD.z4X4/r9tBQAjgBOjDGtRd6juGjMtdUG', 'plomeria', 5, '', NULL, NULL, 'Padua', '32123456', '1995-06-15', 'Argentina', '1122334455', NULL, '', NULL, NULL, '', 'pf_68d4119c515ac7.26028312.jpg', '', NULL, NULL, 'aprobado', 1, '2025-09-24 12:42:19', '', 'uploads/verificaciones/empleado_51/doc_68d4113ddb9de5.36674795.jpg', 'uploads/verificaciones/empleado_51/doc_68d4113ddc5e61.41601060.jpg', 'uploads/verificaciones/empleado_51/doc_68d4113ddd0272.86395612.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(52, 1, NULL, 'Melina Florencia Córdoba', 'santinomamanicuba@gmail.com', '$2y$10$1M.voGF2tzZE9fFNEUEVluwxcJBtgN1Lp5e/F8hWWcNCZC/mubpSW', 'carpinteria', 5, '', NULL, NULL, 'Padua', '29876543', '1995-06-15', 'Argentina', '1122334455', 'Tecnico', '', NULL, NULL, '', 'pf_68d41254d87fa7.21975614.jpg', '', NULL, NULL, 'aprobado', 1, '2025-09-24 12:45:42', '', 'uploads/verificaciones/empleado_52/doc_68d411f8dcde40.27264057.jpg', 'uploads/verificaciones/empleado_52/doc_68d411f8ddad95.79231017.jpg', 'uploads/verificaciones/empleado_52/doc_68d411f8de0fa5.54180970.jpg', '9b09fa81a2a6ebb6704e3eebdde853b25cb29c7f3fa8ab92c2a1c8cbde83ee4d', '2025-10-07 23:48:21', NULL, 1, NULL, NULL),
(53, 1, NULL, 'Enzo Santino Mamani Cuba', 'santinomam@gmail.com', '$2y$10$Ym/Bl/Oic.2Dk48jyH0YWef9KAEP/dDHu9rcbjXoJyNhgBqusVNOa', 'fletero', 5, '', 'Por proyecto', NULL, 'Merlo', '47161648', '2006-01-26', 'Argentina', '1164718626', 'Licencia de conducir (B1)', '', NULL, NULL, '', NULL, '', '58071666b5f702ad846cf6abb8ba479c305ca5860c1be7576d06f4e2ede32459', '2025-10-14 04:34:59', 'aprobado', 1, '2025-10-07 17:51:01', '', 'uploads/verificaciones/empleado_53/doc_68e57cda45c597.89338524.png', 'uploads/verificaciones/empleado_53/doc_68e57cda4621c0.60097627.png', 'uploads/verificaciones/empleado_53/doc_68e57cda470f17.02557170.jpg', NULL, NULL, NULL, 1, NULL, NULL),
(54, 2, '2025-11-30 01:30:38', 'Luciano Pinturas', 'luciano@example.com', '', 'Pintura', NULL, 'Pintor profesional. Interior/exterior, enduido y látex. Presupuesto sin cargo.', NULL, 4000.00, 'Merlo', NULL, NULL, NULL, '11 5555-1111', NULL, 'Puntualidad, Atención al detalle', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aprobado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(55, 3, '2026-10-30 01:30:38', 'Marcela Electricista', 'marcela@example.com', '', 'Electricidad', NULL, 'Electricista matriculada. Instalaciones, tableros, urgencias 24/7.', NULL, 6500.00, 'Ituzaingo', NULL, NULL, NULL, '11 5555-2222', NULL, 'Herramientas propias, Guardias / urgencias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aprobado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `experiencia_laboral`
--

CREATE TABLE `experiencia_laboral` (
  `id_experiencia` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `contacto_referencia` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `experiencia_laboral`
--

INSERT INTO `experiencia_laboral` (`id_experiencia`, `id_empleado`, `puesto`, `empresa`, `contacto_referencia`, `fecha_inicio`, `fecha_fin`, `descripcion`) VALUES
(6, 39, 'encargado', 'coca cola', '1131232012', '2022-01-22', '2023-02-22', 'estaba encargado de todo tipo de servicios.'),
(7, 49, '', '', '', '0000-00-00', '0000-00-00', ''),
(8, 50, '', '', '', '0000-00-00', '0000-00-00', ''),
(9, 51, '', '', '', '0000-00-00', '0000-00-00', ''),
(10, 52, '', '', '', '0000-00-00', '0000-00-00', ''),
(11, 53, '', '', '', '0000-00-00', '0000-00-00', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id_mensaje` int(11) NOT NULL,
  `emisor_tipo` enum('usuario','empleado') NOT NULL,
  `emisor_id` int(11) NOT NULL,
  `receptor_tipo` enum('usuario','empleado') NOT NULL,
  `receptor_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  `leido` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id_mensaje`, `emisor_tipo`, `emisor_id`, `receptor_tipo`, `receptor_id`, `mensaje`, `fecha_envio`, `leido`) VALUES
(1, 'usuario', 14, 'empleado', 52, 'Hola, ¿estás disponible?', '2025-10-16 10:40:16', 0),
(2, 'empleado', 53, 'usuario', 14, 'Hola, si', '2025-10-16 10:41:26', 1),
(3, 'empleado', 53, 'usuario', 14, 'hola, estoy disponible todo el día', '2025-10-16 10:43:55', 1),
(4, 'usuario', 14, 'empleado', 52, 'perfecto', '2025-10-16 10:44:07', 0),
(5, 'usuario', 14, 'empleado', 52, 'hola', '2025-10-16 10:48:31', 0),
(6, 'usuario', 14, 'empleado', 53, 'hola que tal', '2025-10-16 11:15:11', 1),
(7, 'empleado', 53, 'usuario', 14, 'Me parece perfecto, arreglamos la hora?', '2025-10-16 11:15:23', 1),
(8, 'usuario', 14, 'empleado', 53, 'a las 5 de la tarde', '2025-10-16 11:33:49', 1),
(9, 'usuario', 14, 'empleado', 53, 'hola', '2025-10-16 12:59:32', 1),
(10, 'usuario', 15, 'empleado', 53, 'Hola', '2025-10-16 13:20:46', 1),
(11, 'usuario', 15, 'empleado', 53, 'Hola que tal', '2025-10-16 13:20:53', 1),
(12, 'usuario', 15, 'empleado', 49, 'Permiso', '2025-10-16 13:22:08', 0),
(13, 'usuario', 15, 'empleado', 53, 'saddas', '2025-10-16 14:04:22', 1),
(14, 'empleado', 53, 'usuario', 15, 'hola', '2025-10-16 14:17:42', 1),
(15, 'usuario', 15, 'empleado', 53, 'Hola, soy el usuario', '2025-10-16 14:17:54', 1),
(16, 'empleado', 53, 'usuario', 15, 'hola, yo soy el trabajador', '2025-10-16 14:17:59', 1),
(17, 'usuario', 14, 'empleado', 53, 'holaa', '2025-10-17 16:56:16', 1),
(18, 'usuario', 14, 'empleado', 53, 'hola', '2025-10-21 07:46:13', 1),
(19, 'usuario', 14, 'empleado', 53, 'dasd', '2025-10-21 07:46:19', 1),
(20, 'usuario', 14, 'empleado', 39, 'hola', '2025-10-21 08:49:04', 0),
(21, 'empleado', 53, 'usuario', 15, 'perfecto', '2025-10-23 10:03:39', 1),
(22, 'empleado', 53, 'usuario', 15, 'hola', '2025-10-23 10:03:53', 1),
(23, 'empleado', 53, 'usuario', 15, 'hola que tal', '2025-10-23 10:04:01', 1),
(24, 'empleado', 53, 'usuario', 15, 'Hola', '2025-10-23 10:04:13', 1),
(25, 'usuario', 14, 'empleado', 53, 'das', '2025-10-23 10:32:06', 1),
(26, 'usuario', 14, 'empleado', 53, 'dasd', '2025-10-23 10:32:07', 1),
(27, 'usuario', 14, 'empleado', 53, 'dasdas', '2025-10-23 10:32:08', 1),
(28, 'usuario', 14, 'empleado', 53, 'dasdasd', '2025-10-23 10:32:09', 1),
(29, 'usuario', 14, 'empleado', 53, 'asdadsad', '2025-10-23 10:32:09', 1),
(30, 'usuario', 14, 'empleado', 53, 'dasdsasadda', '2025-10-23 10:32:10', 1),
(31, 'usuario', 14, 'empleado', 53, 'dsadasd', '2025-10-23 10:32:11', 1),
(32, 'usuario', 14, 'empleado', 53, 'asdasdsad', '2025-10-23 10:32:12', 1),
(33, 'usuario', 14, 'empleado', 53, 'dasdsad', '2025-10-23 10:32:12', 1),
(34, 'usuario', 14, 'empleado', 53, 'dasdasdasd', '2025-10-23 10:32:13', 1),
(35, 'usuario', 14, 'empleado', 53, 'dasdasd', '2025-10-23 10:32:13', 1),
(36, 'usuario', 14, 'empleado', 53, 'dasdasd', '2025-10-23 10:32:14', 1),
(37, 'usuario', 14, 'empleado', 53, 'hpla', '2025-10-24 14:45:26', 1),
(38, 'usuario', 14, 'empleado', 52, 'Hol', '2025-10-24 14:49:06', 0),
(39, 'usuario', 14, 'empleado', 43, 'hola', '2025-10-28 13:24:50', 0),
(40, 'usuario', 14, 'empleado', 43, 'Hola, soy Usuario LABORA. Te comparto mi ficha: http://localhost/labora_db/vistas/comunes/usuario_publico.php?uid=14', '2025-10-28 13:25:24', 0),
(41, 'usuario', 14, 'empleado', 52, 'hola', '2025-10-28 13:26:16', 0),
(42, 'usuario', 14, 'empleado', 52, 'dsadad', '2025-10-28 13:26:17', 0),
(43, 'usuario', 14, 'empleado', 52, 'dasdsad', '2025-10-28 13:26:18', 0),
(44, 'usuario', 14, 'empleado', 52, 'asdadsad', '2025-10-28 13:26:18', 0),
(45, 'usuario', 14, 'empleado', 52, 'dasdas', '2025-10-28 13:26:23', 0),
(46, 'usuario', 14, 'empleado', 52, 'dasdsa', '2025-10-28 13:26:23', 0),
(47, 'usuario', 14, 'empleado', 52, 'dsadas', '2025-10-28 13:26:24', 0),
(48, 'usuario', 14, 'empleado', 52, 'dasdsad', '2025-10-28 13:26:24', 0),
(49, 'usuario', 14, 'empleado', 52, 'dasdsa', '2025-10-28 13:26:30', 0),
(50, 'usuario', 14, 'empleado', 52, 'dasdad', '2025-10-28 13:26:31', 0),
(51, 'usuario', 14, 'empleado', 52, 'dasdsad', '2025-10-28 13:26:32', 0),
(52, 'usuario', 14, 'empleado', 52, 'dasdad', '2025-10-28 13:26:42', 0),
(53, 'usuario', 14, 'empleado', 39, 'Hola, soy Enzo Santino Mamani Cuba. Te comparto mi ficha: http://localhost/labora_db/vistas/comunes/usuario_publico.php?uid=14', '2025-10-28 15:46:02', 0),
(54, 'empleado', 53, 'usuario', 15, 'Hola', '2025-10-29 20:00:52', 1),
(55, 'usuario', 15, 'empleado', 53, ':V', '2025-10-29 20:01:03', 1),
(56, 'empleado', 53, 'usuario', 15, '🏃🏻‍♂️', '2025-10-29 20:01:11', 1),
(57, 'empleado', 53, 'usuario', 15, '🧅', '2025-10-29 20:01:40', 1),
(58, 'empleado', 53, 'usuario', 15, 'jaja', '2025-10-30 00:58:36', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `billing_interval` enum('free','monthly','yearly') NOT NULL,
  `price_minor` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `currency` char(3) NOT NULL DEFAULT 'ARS',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `portfolio_media_limit` smallint(5) UNSIGNED NOT NULL DEFAULT 3,
  `search_priority` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `badge` varchar(50) DEFAULT NULL,
  `frame_style` varchar(50) DEFAULT NULL,
  `has_stats` tinyint(1) NOT NULL DEFAULT 0,
  `has_priority_support` tinyint(1) NOT NULL DEFAULT 0,
  `has_campaign_boost` tinyint(1) NOT NULL DEFAULT 0,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plans`
--

INSERT INTO `plans` (`id`, `code`, `name`, `billing_interval`, `price_minor`, `currency`, `is_active`, `portfolio_media_limit`, `search_priority`, `badge`, `frame_style`, `has_stats`, `has_priority_support`, `has_campaign_boost`, `meta`, `created_at`, `updated_at`) VALUES
(1, 'FREE', 'Gratis', 'free', 0, 'ARS', 1, 3, 0, NULL, NULL, 0, 0, 0, '{\"notes\": \"Plan base visible\"}', '2025-10-30 04:12:36', '2025-10-30 04:12:36'),
(2, 'PREMIUM_M', 'Premium', 'monthly', 499900, 'ARS', 1, 10, 1, 'Premium', 'dorado', 1, 0, 0, '{\"tax_note\": \"+impuestos\"}', '2025-10-30 04:12:36', '2025-10-30 04:12:36'),
(3, 'PREMIUM_Y', 'Premium Anual', 'yearly', 4999900, 'ARS', 1, 10, 2, 'Miembro Anual', 'platino', 1, 1, 1, '{\"savings_months\": 2}', '2025-10-30 04:12:36', '2025-10-30 04:47:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_pendiente_empleados`
--

CREATE TABLE `registro_pendiente_empleados` (
  `id_empleado` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `profesion` text DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `nacionalidad` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `zona_trabajo` varchar(100) DEFAULT NULL,
  `experiencia_años` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `dni_frente_tmp` varchar(255) DEFAULT NULL,
  `dni_dorso_tmp` varchar(255) DEFAULT NULL,
  `matricula_tmp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_pendiente_empleados`
--

INSERT INTO `registro_pendiente_empleados` (`id_empleado`, `nombre`, `correo`, `clave`, `profesion`, `dni`, `fecha_nacimiento`, `nacionalidad`, `telefono`, `zona_trabajo`, `experiencia_años`, `token`, `creado_en`, `dni_frente_tmp`, `dni_dorso_tmp`, `matricula_tmp`) VALUES
(32, 'Maria Julieta Venegas', 'cuentaparalegends1919@gmail.com', '$2y$10$s0HWchGjHFj0b2MV5vfMO.4QobVKQJhFCPERD8vLzRWwyTyVCISVG', 'educacion', '34568536', '1987-04-12', 'Argentina', '1154547634', 'Merlo', 15, 'db6ac87a2a2298ed7a07a90728c59f021c89372f403404ba9931aed8b06624f2', '2025-09-20 16:30:08', 'uploads/verificaciones/pre_empleado_32/doc_68cf00c007b496.43951511.jpg', 'uploads/verificaciones/pre_empleado_32/doc_68cf00c19ec229.25295212.jpg', 'uploads/verificaciones/pre_empleado_32/doc_68cf00c19f1482.76797047.jpg'),
(42, 'Juan Pérez', 'labora1357@gmail.com', '$2y$10$uvx0GSr.UOfkFw6leBFq9OsC4T6mo/kNtscaFQgLEm1fJX/MZckze', 'plomeria', '32123456', '1995-06-15', 'Argentina', '1122334455', 'Padua', 5, '99d1f05f82fe2a731df1e766e429077e7001982ede7ca556476c7764055966a6', '2025-09-24 13:18:36', 'uploads/verificaciones/pre_empleado_42/doc_68d419dca4a915.24191053.png', 'uploads/verificaciones/pre_empleado_42/doc_68d419dca555b2.06391734.png', 'uploads/verificaciones/pre_empleado_42/doc_68d419dca60ba9.11752060.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_pendiente_usuarios`
--

CREATE TABLE `registro_pendiente_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `dni` varchar(50) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `localidad` varchar(250) DEFAULT NULL,
  `dni_frente_tmp` varchar(255) DEFAULT NULL,
  `dni_dorso_tmp` varchar(255) DEFAULT NULL,
  `matricula_tmp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_pendiente_usuarios`
--

INSERT INTO `registro_pendiente_usuarios` (`id_usuario`, `nombre`, `correo`, `clave`, `telefono`, `token`, `fecha_nacimiento`, `fecha_registro`, `direccion`, `dni`, `creado_en`, `localidad`, `dni_frente_tmp`, `dni_dorso_tmp`, `matricula_tmp`) VALUES
(34, 'Santiago Silvera', 'joseacostatecnica5@gmail.com', '$2y$10$I8.ZXDUtyjzHawPkiCM1nO5Yxg8chq0bde94Ape04h.Vn5UQ7qQRm', '1164718626', '9ce418eb9f9a23e90ebd9c1d68bdad9dadee3ed22c776908642e8e2c9ba06a1e', '2000-02-20', NULL, 'Magoya 342', '47892021', '2025-10-30 14:21:11', 'Merlo', 'uploads/verificaciones/pre_usuario_34/doc_69039e87657b32.50530939.png', 'uploads/verificaciones/pre_usuario_34/doc_69039e8765c823.35896544.png', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `correo` varchar(200) DEFAULT NULL,
  `clave` varchar(250) DEFAULT NULL,
  `nombre` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `tipo_usuario` enum('','','','') NOT NULL,
  `dni` varchar(25) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` varchar(30) DEFAULT NULL,
  `localidad` varchar(250) DEFAULT NULL,
  `reset_token_hash` char(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `estado_verificacion` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `verificado_por` int(11) DEFAULT NULL,
  `fecha_verificacion` datetime DEFAULT NULL,
  `observaciones_verificacion` text DEFAULT NULL,
  `dni_frente_path` varchar(255) DEFAULT NULL,
  `dni_dorso_path` varchar(255) DEFAULT NULL,
  `matricula_path` varchar(255) DEFAULT NULL,
  `zona_busqueda` varchar(120) DEFAULT NULL,
  `rubros_interes` varchar(255) DEFAULT NULL,
  `presupuesto_max` decimal(10,2) DEFAULT NULL,
  `medio_contacto` enum('telefono','whatsapp','email') DEFAULT 'whatsapp',
  `horario_contacto` varchar(120) DEFAULT NULL,
  `descripcion_usuario` text DEFAULT NULL,
  `foto_perfil_usuario` varchar(255) DEFAULT NULL,
  `visibilidad` enum('publico','oculto') DEFAULT 'publico',
  `email_change_token_hash` varchar(64) DEFAULT NULL,
  `email_change_expires` datetime DEFAULT NULL,
  `email_change_new` varchar(255) DEFAULT NULL,
  `email_confirm_token_hash` varchar(64) DEFAULT NULL,
  `email_confirm_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `correo`, `clave`, `nombre`, `telefono`, `fecha_registro`, `tipo_usuario`, `dni`, `direccion`, `fecha_nacimiento`, `localidad`, `reset_token_hash`, `reset_expires`, `estado_verificacion`, `verificado_por`, `fecha_verificacion`, `observaciones_verificacion`, `dni_frente_path`, `dni_dorso_path`, `matricula_path`, `zona_busqueda`, `rubros_interes`, `presupuesto_max`, `medio_contacto`, `horario_contacto`, `descripcion_usuario`, `foto_perfil_usuario`, `visibilidad`, `email_change_token_hash`, `email_change_expires`, `email_change_new`, `email_confirm_token_hash`, `email_confirm_expires`) VALUES
(14, 'santinomam@gmail.com', '$2y$10$/MQPWnB7hubswirg/zpsEOaewvAruO2gfnWxnz.xdQvJntD2G2MgK', 'Enzo Santino Mamani Cuba', '1164718626', '2025-10-13 22:12:56', '', '47161648', 'constitucion 858', '2006-01-26', 'Merlo', 'a159dd12353ff5b798576008fa246c6d7690b25181c8b07ac81fc5e46d8631f9', '2025-10-14 04:31:23', 'aprobado', 1, '2025-10-13 22:24:14', '', 'uploads/verificaciones/usuario_14/doc_68eda372891789.66654569.png', 'uploads/verificaciones/usuario_14/doc_68eda37289d0b1.69383146.png', 'uploads/verificaciones/usuario_14/doc_68eda3728ad324.19514807.jpg', NULL, '', NULL, 'whatsapp', '', '', NULL, 'publico', NULL, NULL, NULL, NULL, NULL),
(15, 'enzosantinomamanicuba@gmail.com', '$2y$10$GsplYv2fZf5hqPvLsZSyc.Z42j3T1fHxg3blAYyRgeF/3bXWmBnmS', 'Alan Fernandez', '1164718626', '2025-10-16 13:17:03', '', '47890231', 'constitucion 858', '2006-01-26', 'Merlo', NULL, NULL, 'aprobado', 1, '2025-10-16 13:18:34', '', 'uploads/verificaciones/usuario_15/doc_68f11a6bf240c9.05264915.png', 'uploads/verificaciones/usuario_15/doc_68f11a6bf2d8e7.54307529.png', 'uploads/verificaciones/usuario_15/doc_68f11a6bf3dc79.09981317.jpg', NULL, NULL, NULL, 'whatsapp', NULL, NULL, NULL, 'publico', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoraciones`
--

CREATE TABLE `valoraciones` (
  `id_valoracion` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `puntuacion` tinyint(4) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `valoraciones`
--

INSERT INTO `valoraciones` (`id_valoracion`, `id_empleado`, `id_usuario`, `puntuacion`, `comentario`, `fecha`) VALUES
(9, 39, 15, 5, 'Buenisimo.', '2025-10-30 03:03:16');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `educacion`
--
ALTER TABLE `educacion`
  ADD PRIMARY KEY (`id_educacion`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `reset_token_hash` (`reset_token_hash`),
  ADD KEY `idx_empleado_estado` (`estado_verificacion`),
  ADD KEY `idx_empleado_plan` (`plan_id`);

--
-- Indices de la tabla `experiencia_laboral`
--
ALTER TABLE `experiencia_laboral`
  ADD PRIMARY KEY (`id_experiencia`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `idx_receptor` (`receptor_tipo`,`receptor_id`,`leido`,`fecha_envio`),
  ADD KEY `idx_par` (`emisor_tipo`,`emisor_id`,`receptor_tipo`,`receptor_id`,`fecha_envio`);

--
-- Indices de la tabla `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `registro_pendiente_empleados`
--
ALTER TABLE `registro_pendiente_empleados`
  ADD PRIMARY KEY (`id_empleado`);

--
-- Indices de la tabla `registro_pendiente_usuarios`
--
ALTER TABLE `registro_pendiente_usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`correo`),
  ADD KEY `reset_token_hash` (`reset_token_hash`),
  ADD KEY `idx_usuarios_estado` (`estado_verificacion`),
  ADD KEY `fk_usuarios_verificador` (`verificado_por`);

--
-- Indices de la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  ADD PRIMARY KEY (`id_valoracion`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `educacion`
--
ALTER TABLE `educacion`
  MODIFY `id_educacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `experiencia_laboral`
--
ALTER TABLE `experiencia_laboral`
  MODIFY `id_experiencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `registro_pendiente_empleados`
--
ALTER TABLE `registro_pendiente_empleados`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `registro_pendiente_usuarios`
--
ALTER TABLE `registro_pendiente_usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  MODIFY `id_valoracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `educacion`
--
ALTER TABLE `educacion`
  ADD CONSTRAINT `educacion_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `fk_empleado_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `experiencia_laboral`
--
ALTER TABLE `experiencia_laboral`
  ADD CONSTRAINT `experiencia_laboral_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_verificador` FOREIGN KEY (`verificado_por`) REFERENCES `administradores` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `valoraciones`
--
ALTER TABLE `valoraciones`
  ADD CONSTRAINT `valoraciones_fk_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `valoraciones_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `purge_msgs_7d` ON SCHEDULE EVERY 1 DAY STARTS '2025-10-21 14:10:51' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM mensajes
  WHERE fecha_envio < NOW() - INTERVAL 7 DAY$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
