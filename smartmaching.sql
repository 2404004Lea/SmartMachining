-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-03-2026 a las 03:17:01
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
-- Base de datos: `smartmachings`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `nombre`, `created_at`) VALUES
(2, 'admin1', 'admin123', 'Naylea', '2026-03-03 17:24:03'),
(3, 'admin2', 'admin2', 'Pedro', '2026-03-13 20:16:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientas`
--

CREATE TABLE `herramientas` (
  `id` varchar(30) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `categoria` varchar(80) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `model_path` varchar(300) NOT NULL,
  `color` varchar(10) DEFAULT '#e67e22',
  `destacado` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `herramientas`
--

INSERT INTO `herramientas` (`id`, `nombre`, `categoria`, `descripcion`, `model_path`, `color`, `destacado`, `created_at`, `updated_at`) VALUES
('t1772584889795', 'Fresa de Carburo de Punta Plana', 'Corte', 'Herramienta de corte fabricada en carburo de tungsteno sólido, diseñada para operaciones de fresado frontal y lateral. Cuenta con filos rectos de alta precisión que permiten cortes limpios en materiales como acero, aluminio y aleaciones no ferrosas. Alta resistencia al desgaste y excelente estabilidad térmica.', 'models/pieza1.glb', '#e67e22', 0, '2026-03-03 18:41:54', '2026-03-03 18:41:54'),
('t1772586110216', 'Broca Helicoidal HSS', 'Corte', 'Broca helicoidal fabricada en acero rápido (HSS), diseñada para perforación en metales, plásticos y materiales compuestos. Su geometría helicoidal facilita la evacuación eficiente de viruta y reduce el sobrecalentamiento durante el mecanizado.', 'models/pieza2.glb', '#e67e22', 0, '2026-03-03 19:02:28', '2026-03-03 19:02:28'),
('t1772586187553', 'Engranaje Recto de Acero', 'Neumática', 'Elemento mecánico – Transmisión de potencia\nComponente mecánico fabricado en acero templado, con dientes rectos mecanizados de alta precisión. Diseñado para transmitir movimiento rotatorio entre ejes paralelos, garantizando eficiencia y durabilidad en sistemas industriales.', 'models/pieza3.glb', '#e67e22', 0, '2026-03-03 19:04:17', '2026-03-03 20:27:24'),
('t1772586282957', 'Portaherramientas CNC', 'Sujeción', 'Dispositivo de sujeción para máquinas CNC, diseñado para asegurar herramientas de corte con alta concentricidad. Fabricado en acero aleado tratado térmicamente para mayor resistencia mecánica y precisión en procesos de alta velocidad.', 'models/pieza4.glb', '#e67e22', 0, '2026-03-03 19:05:23', '2026-03-03 19:05:23'),
('t1772586341513', 'Inserto de Corte Indexable', 'Corte', 'Inserto intercambiable fabricado en carburo recubierto (TiN/TiAlN), utilizado en operaciones de torneado. Diseñado para ofrecer alta resistencia al desgaste, estabilidad térmica y facilidad de reemplazo sin desmontar el portaherramientas.', 'models/pieza5.glb', '#e67e22', 0, '2026-03-03 19:06:05', '2026-03-03 19:06:05'),
('t1772589687734', 'Soporte estructural base', 'Sujeción', 'Componente mecánico / Estructural.\nModelo 3D en formato .glb que representa un soporte estructural diseñado para brindar estabilidad y fijación a un sistema mecánico. Presenta geometría sólida con superficies planas y perforaciones para ensamblaje mediante tornillería. Diseñado para manufactura en material metálico o polímero de alta resistencia.', 'models/pieza6.glb', '#e67e22', 0, '2026-03-03 20:02:31', '2026-03-03 20:17:16'),
('t1772589761661', 'Conector de unión lateral', 'Sujeción', 'Componente de ensamble.\nModelo tridimensional en formato .glb correspondiente a un conector de unión empleado para acoplar dos o más elementos estructurales. Incluye cavidades y/o salientes diseñadas para ajuste preciso. Compatible con sistemas de ensamblaje mecánico.', 'models/pieza7.glb', '#e67e22', 0, '2026-03-03 20:04:06', '2026-03-03 20:27:59'),
('t1772589859466', 'Eje de transmisión', 'Neumática', 'Componente mecánico / Movimiento.\nModelo 3D en formato .glb que representa un eje diseñado para transmitir movimiento rotacional entre componentes. Presenta forma cilíndrica con posibles ranuras o acoples para engranaje o sujeción. Fabricable en acero templado u otro material resistente al torque.', 'models/pieza8.glb', '#e67e22', 0, '2026-03-03 20:05:15', '2026-03-03 20:18:01'),
('t1772590158993', 'Cubierta protectora', 'Diagnóstico', 'Componente de protección.\nModelo tridimensional en formato .glb correspondiente a una cubierta diseñada para proteger mecanismos internos contra agentes externos como polvo o impactos leves. Incluye bordes de ajuste y puntos de fijación.', 'models/pieza9.glb', '#e67e22', 0, '2026-03-03 20:10:21', '2026-03-03 20:25:18'),
('t1772590235190', 'Base de soporte principal', 'Sujeción', 'Componente estructural.\nModelo 3D en formato .glb que representa la base principal de un sistema, diseñada para distribuir cargas y proporcionar estabilidad. Geometría robusta con superficie amplia de contacto y perforaciones para anclaje.', 'models/pieza10.glb', '#e67e22', 0, '2026-03-03 20:13:29', '2026-03-03 20:13:29'),
('t1773449391053', 'Engranaje cónico dentado', 'Otra', 'Elemento mecánico utilizado para transmitir movimiento y potencia entre ejes que se intersectan. Sus dientes permiten transferir torque dentro de sistemas de maquinaria o cajas de engranajes.', 'models/pieza14.glb', '#e67e22', 0, '2026-03-13 18:52:10', '2026-03-13 18:52:10'),
('t1773449618535', 'Eje de transmisión escalonado', 'Sujeción', 'Componente cilíndrico mecanizado diseñado para transmitir movimiento rotatorio entre diferentes elementos mecánicos. Presenta secciones escalonadas que permiten el montaje de rodamientos, engranajes o poleas en sistemas de maquinaria.', 'models/pieza15.glb', '#e67e22', 0, '2026-03-13 18:54:51', '2026-03-13 18:54:51'),
('t1773450722798', 'Soporte mecánico de montaje', 'Sujeción', 'Elemento estructural diseñado para fijar o sostener componentes mecánicos dentro de un sistema. Posee superficies planas y puntos de anclaje que permiten su instalación mediante tornillos o pernos en estructuras o bastidores.', 'models/pieza16.glb', '#e67e22', 0, '2026-03-13 19:14:05', '2026-03-13 19:14:05'),
('t1773450918587', 'Tornillo sin fin', 'Otra', 'Tipo de herramienta: Transmisión mecánica.\nComponente helicoidal utilizado en mecanismos de transmisión para convertir movimiento rotatorio en movimiento angular controlado. Funciona junto con una rueda dentada (corona) y es común en reductores de velocidad y sistemas de precisión.', 'models/pieza17.glb', '#e67e22', 0, '2026-03-13 19:16:17', '2026-03-13 19:16:17'),
('t1773451007306', 'Acoplamiento mecánico cilíndrico', 'Otra', 'Tipo de herramienta: Conexión / transmisión\nDescripción técnica:\nDispositivo utilizado para unir dos ejes y transmitir movimiento rotatorio entre ellos. Permite mantener alineados los componentes del sistema y absorber pequeñas desalineaciones durante el funcionamiento.', 'models/pieza18.glb', '#e67e22', 0, '2026-03-13 19:18:40', '2026-03-13 19:18:40'),
('t1773452047396', 'horquilla mecánic', 'Otra', 'Elemento de unión que permite conectar dos componentes mediante un pasador, facilitando movimiento articulado o transmisión de carga.', 'models/pieza11.glb', '#e67e22', 0, '2026-03-13 19:51:05', '2026-03-13 19:51:05'),
('t1773453195816', 'Horquilla doble o clevis simétrico.', 'Otra', 'Elementos de unión mecánica.', 'models/pieza12.glb', '#e67e22', 0, '2026-03-13 19:55:20', '2026-03-13 19:55:20'),
('t1773453953028', 'Tornillo hexagonal', 'Otra', 'Cabeza: hexagonal, diseñada para llaves o dados.\n\nCuerpo: vástago cilíndrico roscado.\n\nFunción: unir piezas mediante roscado interno (tuerca o rosca en el material).\n\nMaterial típico: acero al carbono, acero inoxidable u otras aleaciones.\n\nNormas comunes: DIN 933, ISO 4017, ANSI/ASME B18.2.1.', 'models/pieza13.glb', '#e67e22', 0, '2026-03-13 20:11:04', '2026-03-13 20:11:04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `herramientas`
--
ALTER TABLE `herramientas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
