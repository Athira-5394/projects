SHOW CREATE TABLE t_content\G

ALTER TABLE t_content
MODIFY COLUMN `userid` varchar(256) NOT NULL COMMENT '棚卸担当者ユーザーID';

UPDATE t_content
SET userid = ''
WHERE userid = '0';

SHOW CREATE TABLE t_content\G
