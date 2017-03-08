/* Migration to 0.11.0 */

CREATE TABLE `glpi_plugin_armadito_schedulerdetails` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `type` varchar(255) DEFAULT NULL,
   `value` text,
   `plugin_armadito_agents_id` int(11) NOT NULL,
   `plugin_armadito_antiviruses_id` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

ALTER TABLE `glpi_plugin_armadito_schedulers` ADD `plugin_armadito_antiviruses_id` int(11) NOT NULL;
ALTER TABLE `glpi_plugin_armadito_schedulers` ADD `plugin_armadito_schedulerdetails_id` int(11) NOT NULL;
