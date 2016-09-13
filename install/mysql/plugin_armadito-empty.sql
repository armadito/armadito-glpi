DROP TABLE IF EXISTS `glpi_plugin_armadito_agents`;

CREATE TABLE `glpi_plugin_armadito_agents` (
   `id` int(11) NOT NULL auto_increment,
   `entities_id` int(11) NOT NULL default 0,
   `computers_id` int(11) NOT NULL,
   `plugin_fusioninventory_agents_id` int(11) NOT NULL,
   `device_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Fusion deviceid',
   `agent_version` varchar(255) collate utf8_unicode_ci default NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `last_contact` datetime default NULL,
   `last_alert` datetime default NULL,
   `fingerprint` varchar(255) collate utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `fingerprint` (`fingerprint`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `glpi_plugin_armadito_configs`;

CREATE TABLE `glpi_plugin_armadito_configs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `unicity` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_antiviruses`;

CREATE TABLE `glpi_plugin_armadito_antiviruses` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `fullname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `fullname` (`fullname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_states`;

CREATE TABLE `glpi_plugin_armadito_states` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `update_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `last_update` datetime default NULL,
   `antivirus_name` varchar(255) collate utf8_unicode_ci NOT NULL,
   `antivirus_version` varchar(255) collate utf8_unicode_ci default NULL,
   `antivirus_realtime` varchar(255) collate utf8_unicode_ci default NULL,
   `antivirus_service` varchar(255) collate utf8_unicode_ci default NULL,
   `plugin_armadito_statedetails_id` int(11) NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `plugin_armadito_agents_id` (`plugin_armadito_agents_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_statedetails`;

CREATE TABLE `glpi_plugin_armadito_statedetails` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `module_name` varchar(255) collate utf8_unicode_ci NOT NULL,
   `module_version` varchar(255) collate utf8_unicode_ci NOT NULL,
   `module_update_status` varchar(255) collate utf8_unicode_ci NOT NULL,
   `module_last_update` datetime NOT NULL,
   `itemlink` varchar(255) collate utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `couple module_agent_id` (`module_name`,`plugin_armadito_agents_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_jobs`;

CREATE TABLE `glpi_plugin_armadito_jobs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `job_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `job_priority` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `job_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `job_error_code` int(11) NOT NULL,
   `job_error_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_jobs_agents`;

CREATE TABLE `glpi_plugin_armadito_jobs_agents` (
   `job_id` int(11) NOT NULL,
   `agent_id` int(11) NOT NULL,
   PRIMARY KEY (`job_id`,`agent_id`),
   FOREIGN KEY (`job_id`)
      REFERENCES glpi_plugin_armadito_jobs(id)
      ON UPDATE CASCADE ON DELETE CASCADE,
   FOREIGN KEY (`agent_id`)
      REFERENCES glpi_plugin_armadito_agents(id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_alerts`;

CREATE TABLE `glpi_plugin_armadito_alerts` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_scans`;

CREATE TABLE `glpi_plugin_armadito_scans` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `plugin_armadito_jobs_id` int(11) NOT NULL,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_scanconfigs_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   `malware_count` int(11) NOT NULL,
   `suspicious_count` int(11) NOT NULL,
   `scanned_count` int(11) NOT NULL,
   `start_time` datetime default NULL,
   `duration` int(11) NOT NULL,
   `progress` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_scanconfigs`;

CREATE TABLE `glpi_plugin_armadito_scanconfigs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `scan_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `scan_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `scan_options` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   PRIMARY KEY (`id`),
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
