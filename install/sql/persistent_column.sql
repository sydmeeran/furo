/*
# Disable unique keys after delete with NULL in key, stored or persistent column
ALTER TABLE `user` ADD `del` CHAR(0) GENERATED ALWAYS AS (if(status in ('ACTIVE', 'ONHOLD', 'BANNED'),'', NULL)) PERSISTENT;
ALTER TABLE `product` ADD `del` CHAR(0) GENERATED ALWAYS AS (if(status in ('ACTIVE'),'', NULL)) PERSISTENT;
ALTER TABLE `product_addon` ADD `del` CHAR(0) GENERATED ALWAYS AS (if(status in ('ACTIVE'),'', NULL)) PERSISTENT;
ALTER TABLE `user` ADD `del` CHAR(0) GENERATED ALWAYS AS (if(status in ('ACTIVE', 'ONHOLD', 'BANNED'),'', NULL)) PERSISTENT AFTER `code`;

# Virtual column
ALTER TABLE `user` ADD code varchar(32) GENERATED ALWAYS AS (left(MD5(username),16)) VIRTUAL;
ALTER TABLE `user` ADD code varchar(32) GENERATED ALWAYS AS (left(MD5(username),16)) VIRTUAL AFTER `ststus`;

# Disable unique keys after delete with NULL in key, stored or persistent column
CREATE TABLE client (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    username varchar(250),
    email varchar(250),
    status enum('ACTIVE', 'ONHOLD', 'BANNED', 'DELETED'),
    del char(0) GENERATED ALWAYS as (if(status in ('ACTIVE', 'ONHOLD', 'BANNED'),'', NULL)) PERSISTENT,
	code varchar(32) GENERATED ALWAYS AS (left(MD5(username),16)) VIRTUAL,
    PRIMARY KEY (id),
    unique(email, del),
    unique(username, del)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/