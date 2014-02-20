CREATE TABLE `civicrm_property_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci;

INSERT INTO `civicrm_property_type` SET `label` = "Eengezinswoning";
INSERT INTO `civicrm_property_type` SET `label` = "Maisonnette";
INSERT INTO `civicrm_property_type` SET `label` = "Benedenduplex";
INSERT INTO `civicrm_property_type` SET `label` = "Bovenduplex";
INSERT INTO `civicrm_property_type` SET `label` = "Appartement Met Lift";
INSERT INTO `civicrm_property_type` SET `label` = "Appartement Zonder Lift";
INSERT INTO `civicrm_property_type` SET `label` = "Laagbouwwoning";
INSERT INTO `civicrm_property_type` SET `label` = "Onzelfstandige kamer";
INSERT INTO `civicrm_property_type` SET `label` = "Standplaats";
INSERT INTO `civicrm_property_type` SET `label` = "Woonzorgunit";