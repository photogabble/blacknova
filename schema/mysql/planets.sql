CREATE TABLE IF NOT EXISTS bnt_planets (
  planet_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  sector_id int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  organics int(20) NOT NULL DEFAULT '0',
  ore int(20) NOT NULL DEFAULT '0',
  goods int(20) NOT NULL DEFAULT '0',
  energy int(20) NOT NULL DEFAULT '0',
  colonists int(20) NOT NULL DEFAULT '0',
  credits int(20) NOT NULL DEFAULT '0',
  fighters int(20) NOT NULL DEFAULT '0',
  torps int(20) NOT NULL DEFAULT '0',
  `owner` int(10) unsigned NOT NULL DEFAULT '0',
  corp int(10) unsigned NOT NULL DEFAULT '0',
  base varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  sells varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  prod_organics int(11) NOT NULL DEFAULT '20',
  prod_ore int(11) NOT NULL DEFAULT '0',
  prod_goods int(11) NOT NULL DEFAULT '0',
  prod_energy int(11) NOT NULL DEFAULT '0',
  prod_fighters int(11) NOT NULL DEFAULT '0',
  prod_torp int(11) NOT NULL DEFAULT '0',
  defeated varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (planet_id),
  KEY bnt_owner (`owner`),
  KEY bnt_corp (corp)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
