CREATE TABLE `civicrm_property_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hov_id` int(11) DEFAULT NULL,
  `hov_vge_id` int(11) DEFAULT NULL,
  `hov_corr_name` varchar(128) DEFAULT NULL,
  `hov_start_date` date DEFAULT NULL,
  `hov_end_date` date DEFAULT NULL,
  `hov_hoofd_huurder_id` int(11) DEFAULT NULL,
  `hov_mede_huurder_id` int(11) DEFAULT NULL,
  `hov_mutatie_id` int(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `hov_id` (`hov_id`),
  KEY `vge_id` (`hov_vge_id`),
  KEY `hoofd_huurder` (`hov_hoofd_huurder_id`),
  KEY `mede_huurder` (`hov_mede_huurder_id`),
  KEY `mutatie` (`hov_mutatie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci;