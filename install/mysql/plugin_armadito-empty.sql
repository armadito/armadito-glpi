DROP TABLE IF EXISTS `glpi_plugin_armadito_agents`;

CREATE TABLE `glpi_plugin_armadito_agents` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `entities_id` int(11) NOT NULL DEFAULT '0',
   `computers_id` int(11) NOT NULL,
   `agent_version` varchar(255) DEFAULT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `plugin_armadito_schedulers_id` int(11) NOT NULL,
   `last_contact` datetime DEFAULT NULL,
   `last_alert` datetime DEFAULT NULL,
   `uuid` varchar(255) NOT NULL,
   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `is_deleted` (`is_deleted`),
   UNIQUE KEY `uuid` (`uuid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `glpi_plugin_armadito_configs`;

CREATE TABLE `glpi_plugin_armadito_configs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) DEFAULT NULL,
   `value` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `unicity` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_avconfigs`;

CREATE TABLE `glpi_plugin_armadito_avconfigs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `realtime_status` varchar(255) DEFAULT NULL,
   `last_avconfig` datetime DEFAULT NULL,
   `plugin_armadito_avconfigdetails_id` int(11) NOT NULL,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_avconfigdetails`;

CREATE TABLE `glpi_plugin_armadito_avconfigdetails` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) DEFAULT NULL,
   `value` text,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_schedulers`;

CREATE TABLE `glpi_plugin_armadito_schedulers` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `is_used` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_antiviruses`;

CREATE TABLE `glpi_plugin_armadito_antiviruses` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(255) DEFAULT NULL,
   `osname` varchar(255) DEFAULT NULL,
   `fullname` varchar(255) DEFAULT NULL,
   `version` varchar(255) DEFAULT NULL,
   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `is_deleted` (`is_deleted`),
   UNIQUE KEY `fullname` (`fullname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_states`;

CREATE TABLE `glpi_plugin_armadito_states` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `update_status` varchar(255) DEFAULT NULL,
   `last_update` datetime DEFAULT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `service_status` varchar(255) DEFAULT NULL,
   `plugin_armadito_stateupdatedetails_id` int(11) NOT NULL,
   `plugin_armadito_stateavdetails_id` int(11) NOT NULL,
   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `is_deleted` (`is_deleted`),
   UNIQUE KEY `plugin_armadito_agents_id` (`plugin_armadito_agents_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_stateupdatedetails`;

CREATE TABLE `glpi_plugin_armadito_stateupdatedetails` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `module_name` varchar(255) NOT NULL,
   `module_version` varchar(255) NOT NULL,
   `module_update_status` varchar(255) NOT NULL,
   `module_last_update` datetime NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `couple module_agent_id` (`module_name`,`plugin_armadito_agents_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_stateavdetails`;

CREATE TABLE `glpi_plugin_armadito_stateavdetails` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) DEFAULT NULL,
   `value` varchar(255) DEFAULT NULL,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_jobs`;

CREATE TABLE `glpi_plugin_armadito_jobs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `job_type` varchar(255) DEFAULT NULL,
   `job_priority` varchar(255) DEFAULT NULL,
   `job_status` varchar(255) DEFAULT NULL,
   `job_error_code` int(11) DEFAULT NULL,
   `job_error_msg` varchar(255) DEFAULT NULL,
   `start_time` datetime NOT NULL,
   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `is_deleted` (`is_deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_alerts`;

CREATE TABLE `glpi_plugin_armadito_alerts` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `plugin_armadito_jobs_id` int(11) NOT NULL,
   `plugin_armadito_scans_id` int(11) NOT NULL,
   `threat_name` varchar(255) DEFAULT NULL,
   `filepath` varchar(255) DEFAULT NULL,
   `module_name` varchar(255) DEFAULT NULL,
   `impact_severity` varchar(255) DEFAULT NULL,
   `info` text DEFAULT NULL,
   `action` varchar(255) DEFAULT NULL,
   `detection_time` datetime DEFAULT NULL,
   `checksum` varchar(255) DEFAULT NULL,
   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `is_deleted` (`is_deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_scans`;

CREATE TABLE `glpi_plugin_armadito_scans` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_jobs_id` int(11) NOT NULL,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_scanconfigs_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `malware_count` int(11) DEFAULT NULL,
   `suspicious_count` int(11) DEFAULT NULL,
   `scanned_count` int(11) DEFAULT NULL,
   `start_time` datetime DEFAULT NULL,
   `end_time` datetime DEFAULT NULL,
   `duration` varchar(255) DEFAULT NULL,
   `progress` int(11) DEFAULT NULL,
   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `is_deleted` (`is_deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_scanconfigs`;

CREATE TABLE `glpi_plugin_armadito_scanconfigs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `scan_name` varchar(255) DEFAULT NULL,
   `scan_path` varchar(255) DEFAULT NULL,
   `scan_options` text DEFAULT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `is_deleted` (`is_deleted`),
   UNIQUE KEY `scan_name` (`scan_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_lastcontactstats`;

CREATE TABLE `glpi_plugin_armadito_lastcontactstats` (
 `id` smallint(3) NOT NULL AUTO_INCREMENT,
 `day` smallint(3) NOT NULL DEFAULT '0',
 `hour` tinyint(2) NOT NULL DEFAULT '0',
 `counter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_lastalertstats`;

CREATE TABLE `glpi_plugin_armadito_lastalertstats` (
 `id` smallint(3) NOT NULL AUTO_INCREMENT,
 `day` smallint(3) NOT NULL DEFAULT '0',
 `hour` tinyint(2) NOT NULL DEFAULT '0',
 `counter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
