
CREATE TABLE `contract_apis` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validity` date NOT NULL,
  `expire` date NOT NULL,
  `status` enum('publish','draft','incomplete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `remarks` varchar(5000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_user_id` int(10) UNSIGNED DEFAULT NULL,
  `account_id` int(10) UNSIGNED DEFAULT NULL,
  `direction_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contract_apis`
--
ALTER TABLE `contract_apis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_apis_company_user_id_foreign` (`company_user_id`),
  ADD KEY `contract_apis_account_id_foreign` (`account_id`),
  ADD KEY `contract_apis_direction_id_foreign` (`direction_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contract_apis`
--
ALTER TABLE `contract_apis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contract_apis`
--
ALTER TABLE `contract_apis`
  ADD CONSTRAINT `contract_apis_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts_import_cfcl` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_apis_company_user_id_foreign` FOREIGN KEY (`company_user_id`) REFERENCES `company_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_apis_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`);

  
  
  
  
  -- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 26-09-2019 a las 18:06:34
-- Versión del servidor: 5.7.27
-- Versión de PHP: 7.1.31-1+0~20190807.22+debian9~1.gbpf402ed

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `cargofive`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rate_apis`
--

CREATE TABLE `rate_apis` (
  `id` int(10) UNSIGNED NOT NULL,
  `origin_port` int(10) UNSIGNED NOT NULL,
  `destiny_port` int(10) UNSIGNED NOT NULL,
  `carrier_id` int(10) UNSIGNED NOT NULL,
  `contract_id` int(10) UNSIGNED NOT NULL,
  `twuenty` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forty` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fortyhc` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fortynor` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fortyfive` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_id` int(10) UNSIGNED NOT NULL,
  `schedule_type_id` int(10) UNSIGNED DEFAULT NULL,
  `transit_time` int(10) UNSIGNED DEFAULT NULL,
  `via` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rate_apis`
--

INSERT INTO `rate_apis` (`id`, `origin_port`, `destiny_port`, `carrier_id`, `contract_id`, `twuenty`, `forty`, `fortyhc`, `fortynor`, `fortyfive`, `currency_id`, `schedule_type_id`, `transit_time`, `via`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 742, 743, 1, 1, '100', '200', '300', '400', '500', 1, 1, 10, '10', NULL, '2019-09-26 04:00:00', '2019-09-26 04:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `rate_apis`
--
ALTER TABLE `rate_apis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rate_apis_origin_port_foreign` (`origin_port`),
  ADD KEY `rate_apis_destiny_port_foreign` (`destiny_port`),
  ADD KEY `rate_apis_carrier_id_foreign` (`carrier_id`),
  ADD KEY `rate_apis_contract_id_foreign` (`contract_id`),
  ADD KEY `rate_apis_currency_id_foreign` (`currency_id`),
  ADD KEY `rate_apis_schedule_type_id_foreign` (`schedule_type_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `rate_apis`
--
ALTER TABLE `rate_apis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rate_apis`
--
ALTER TABLE `rate_apis`
  ADD CONSTRAINT `rate_apis_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`),
  ADD CONSTRAINT `rate_apis_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contract_apis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rate_apis_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
  ADD CONSTRAINT `rate_apis_destiny_port_foreign` FOREIGN KEY (`destiny_port`) REFERENCES `harbors` (`id`),
  ADD CONSTRAINT `rate_apis_origin_port_foreign` FOREIGN KEY (`origin_port`) REFERENCES `harbors` (`id`),
  ADD CONSTRAINT `rate_apis_schedule_type_id_foreign` FOREIGN KEY (`schedule_type_id`) REFERENCES `schedule_type` (`id`) ON DELETE SET NULL;

  
  
  
  
  
  
  
  
  
  
  
