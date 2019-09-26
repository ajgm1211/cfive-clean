

CREATE TABLE `rates_apis` (
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
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `rates_apis`
--
ALTER TABLE `rates_apis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rates_apis_origin_port_foreign` (`origin_port`),
  ADD KEY `rates_apis_destiny_port_foreign` (`destiny_port`),
  ADD KEY `rates_apis_carrier_id_foreign` (`carrier_id`),
  ADD KEY `rates_apis_contract_id_foreign` (`contract_id`),
  ADD KEY `rates_apis_currency_id_foreign` (`currency_id`),
  ADD KEY `rates_apis_schedule_type_id_foreign` (`schedule_type_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `rates_apis`
--
ALTER TABLE `rates_apis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rates_apis`
--
ALTER TABLE `rates_apis`
  ADD CONSTRAINT `rates_apis_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`),
  ADD CONSTRAINT `rates_apis_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts_apis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rates_apis_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
  ADD CONSTRAINT `rates_apis_destiny_port_foreign` FOREIGN KEY (`destiny_port`) REFERENCES `harbors` (`id`),
  ADD CONSTRAINT `rates_apis_origin_port_foreign` FOREIGN KEY (`origin_port`) REFERENCES `harbors` (`id`),
  ADD CONSTRAINT `rates_apis_schedule_type_id_foreign` FOREIGN KEY (`schedule_type_id`) REFERENCES `schedule_type` (`id`) ON DELETE SET NULL;
