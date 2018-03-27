<?php

require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();
$dotenv->required([
    "DB"
]);

try {
    $db = getDB();
    $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS `clicks` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `rule_id` int(11) unsigned NOT NULL,
        `clicked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `from` varchar(256) NOT NULL DEFAULT '',
        `to` varchar(256) NOT NULL DEFAULT '',
        `ip` varchar(16) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `rule_id` (`rule_id`),
        CONSTRAINT `clicks_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt2 = $db->prepare("CREATE TABLE IF NOT EXISTS `rules` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` varchar(256) DEFAULT NULL,
        `from` varchar(256) NOT NULL,
        `to` varchar(256) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `enabled` tinyint(1) NOT NULL DEFAULT '1',
        `modified_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `from` (`from`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    $stmt2->execute([]);
    $stmt->execute([]);
    die("Migrated successfully");
} catch (\Exception $e) {
    die($e->getMessage());
}