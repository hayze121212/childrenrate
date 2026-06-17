CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `expires` timestamp NULL DEFAULT NULL,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `expires` timestamp NULL DEFAULT NULL,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;