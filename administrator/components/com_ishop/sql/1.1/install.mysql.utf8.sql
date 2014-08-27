ALTER TABLE  `jss_ishop_products` ADD  `new_flag` TINYINT NOT NULL COMMENT  'Новинка' AFTER  `recommended_flag` ,
ADD INDEX (  `new_flag` )