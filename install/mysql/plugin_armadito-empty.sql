
DROP TABLE IF EXISTS `glpi_plugin_armadito_armaditos`;
DROP TABLE IF EXISTS `glpi_plugin_armadito_agents`;

CREATE TABLE `glpi_plugin_armadito_agents` (
   `id` int(11) NOT NULL auto_increment,
   `entities_id` int(11) NOT NULL default 0,
   `computers_id` int(11) NOT NULL,
   `plugin_fusioninventory_agents_id` int(11) NOT NULL,
   `device_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Fusion deviceid',
   `agent_version` varchar(255) collate utf8_unicode_ci default NULL,
   `antivirus_name` varchar(255) collate utf8_unicode_ci NOT NULL,
   `antivirus_version` varchar(255) collate utf8_unicode_ci default NULL,
   `antivirus_state` varchar(255) collate utf8_unicode_ci default NULL,
   `last_contact` datetime default NULL,
   `last_alert` datetime default NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `glpi_plugin_armadito_configs`;

CREATE TABLE `glpi_plugin_armadito_configs` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `unicity` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `glpi_plugin_armadito_states`;

CREATE TABLE `glpi_plugin_armadito_states` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `agent_id` int(11) NOT NULL,
   `update_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `last_update` datetime default NULL,
   `antivirus_name` varchar(255) collate utf8_unicode_ci NOT NULL,
   `antivirus_version` varchar(255) collate utf8_unicode_ci default NULL,
   `antivirus_realtime` varchar(255) collate utf8_unicode_ci default NULL,
   `antivirus_service` varchar(255) collate utf8_unicode_ci default NULL,
   `plugin_armadito_statedetails_id` int(11) NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `agent_id` (`agent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_statedetails`;

CREATE TABLE `glpi_plugin_armadito_statedetails` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `agent_id` int(11) NOT NULL,
   `module_name` varchar(255) collate utf8_unicode_ci NOT NULL,
   `module_version` varchar(255) collate utf8_unicode_ci NOT NULL,
   `module_update_status` varchar(255) collate utf8_unicode_ci NOT NULL,
   `module_last_update` datetime NOT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `couple module_agent_id` (`module_name`,`agent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_alerts`;

CREATE TABLE `glpi_plugin_armadito_alerts` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `glpi_plugin_armadito_lastupdatestats`;

CREATE TABLE `glpi_plugin_armadito_lastupdatestats` (
 `id` smallint(3) NOT NULL AUTO_INCREMENT,
 `day` smallint(3) NOT NULL DEFAULT '0',
 `hour` tinyint(2) NOT NULL DEFAULT '0',
 `counter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
