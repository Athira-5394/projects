DROP TABLE IF EXISTS `m_user`;

CREATE TABLE `m_user` (
  `userid` int NOT NULL AUTO_INCREMENT COMMENT 'ユーザーID',
  `mail` varchar(256) NOT NULL COMMENT 'メールアドレス',
  `username` varchar(256) NOT NULL COMMENT '氏名',
  `password` varchar(256) NOT NULL COMMENT 'パスワード',
  `permissiontype` tinyint NOT NULL COMMENT '権限タイプ 0:作業者、1:管理者',
  `enableflg` tinyint NOT NULL COMMENT '有効フラグ 0:無効、1:有効',
  `apday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `upday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '変更日',
  `apemp` varchar(256) NOT NULL COMMENT '登録者',
  `upemp` varchar(256) NOT NULL COMMENT '変更者',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `m_user_mail` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ユーザー情報';

DROP TABLE IF EXISTS `t_user_api`;

CREATE TABLE `t_user_api` (
  `userid` int NOT NULL COMMENT 'ユーザーID',
  `token` varchar(256) NOT NULL COMMENT 'APIトークン',
  `expiration` datetime NOT NULL COMMENT 'API有効期限',
  `apday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `upday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '変更日',
  `apemp` varchar(256) NOT NULL COMMENT '登録者',
  `upemp` varchar(256) NOT NULL COMMENT '変更者',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `t_user_api_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ユーザーAPI情報';

DROP TABLE IF EXISTS `t_content`;

CREATE TABLE `t_content` (
  `contentid` int NOT NULL AUTO_INCREMENT COMMENT 'コンテンツID',
  `domainid` int NOT NULL COMMENT 'ドメインID',
  `filename` varchar(256) NOT NULL COMMENT 'ファイル名',
  `path` varchar(256) NOT NULL COMMENT 'パス',
  `title` varchar(256) NOT NULL COMMENT 'タイトル',
  `remarks` text DEFAULT NULL COMMENT '備考',
  `contentstatus` tinyint NOT NULL COMMENT 'コンテンツ更新ステータス 0:更新無し、1:更新有り、2:新規、3:削除',
  `contentcreatedate` datetime NOT NULL COMMENT 'コンテンツ作成日時',
  `contentupdatedate` datetime NOT NULL COMMENT 'コンテンツ更新日時',
  `userid` int NOT NULL COMMENT '棚卸担当者ユーザーID',
  `inventorystatus` tinyint NOT NULL COMMENT '棚卸ステータス 0:未完了、1:完了',
  `inventory` tinyint NOT NULL COMMENT '棚卸対象ステータス 0:棚卸対象、1:棚卸対象外',
  `inventoryduedate` date DEFAULT NULL COMMENT '棚卸期限',
  `inventorydate` date DEFAULT NULL COMMENT '棚卸実施日',
  `disableflg` tinyint NOT NULL COMMENT '有効フラグ 0:有効、1:無効（基本的に0とし、画面に表示させたくない場合、1とする）',
  `apday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `upday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '変更日',
  `apemp` varchar(256) NOT NULL COMMENT '登録者',
  `upemp` varchar(256) NOT NULL COMMENT '変更者',
  PRIMARY KEY (`contentid`),
  UNIQUE KEY `m_t_content_path` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='コンテンツ情報';

DROP TABLE IF EXISTS `t_content_temp`;

CREATE TABLE `t_content_temp` (
  `tempcontentid` int NOT NULL AUTO_INCREMENT COMMENT '一時コンテンツID',
  `domainid` int NOT NULL COMMENT 'ドメインID',
  `filename` varchar(256) NOT NULL COMMENT 'ファイル名',
  `path` varchar(256) NOT NULL COMMENT 'パス',
  `title` varchar(256) NOT NULL COMMENT 'タイトル',
  `contentupdatedate` datetime NOT NULL COMMENT 'コンテンツ更新日時',
  `apday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `apemp` varchar(256) NOT NULL COMMENT '登録者',
  PRIMARY KEY (`tempcontentid`),
  UNIQUE KEY `m_t_content_temp_path` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='一時コンテンツ情報';

DROP TABLE IF EXISTS `t_content_log`;

CREATE TABLE `t_content_log` (
  `logid` int NOT NULL AUTO_INCREMENT COMMENT 'ログID',
  `contentid` int NOT NULL COMMENT 'コンテンツID',
  `userid` int NOT NULL COMMENT '更新者ユーザーID',
  `username` varchar(256) NOT NULL COMMENT '更新者名',
  `detail` text NOT NULL COMMENT '更新内容',
  `apday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `upday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '変更日',
  `apemp` varchar(256) NOT NULL COMMENT '登録者',
  `upemp` varchar(256) NOT NULL COMMENT '変更者',
  PRIMARY KEY (`logid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='コンテンツ棚卸履歴';

DROP TABLE IF EXISTS `m_domain`;

CREATE TABLE `m_domain` (
  `domainid` int NOT NULL AUTO_INCREMENT COMMENT 'ドメインID',
  `domainname` varchar(256) NOT NULL COMMENT 'ドメイン名',
  `documentroot` varchar(256) NOT NULL COMMENT 'ドキュメント・ルート',
  `apday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `upday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '変更日',
  `apemp` varchar(256) NOT NULL COMMENT '登録者',
  `upemp` varchar(256) NOT NULL COMMENT '変更者',
  PRIMARY KEY (`domainid`),
  UNIQUE KEY `m_domain_domainname` (`domainname`),
  UNIQUE KEY `m_domain_documentroot` (`documentroot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ドメイン情報';

DROP TABLE IF EXISTS `m_directory`;

CREATE TABLE `m_directory` (
  `directoryid` int NOT NULL AUTO_INCREMENT COMMENT 'ディレクトリID',
  `domainid` int NOT NULL COMMENT 'ドメインID',
  `path` varchar(256) NOT NULL COMMENT 'パス',
  `enableflg` tinyint NOT NULL COMMENT '有効フラグ 0:無効、1:有効',
  `apday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `upday` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '変更日',
  `apemp` varchar(256) NOT NULL COMMENT '登録者',
  `upemp` varchar(256) NOT NULL COMMENT '変更者',
  PRIMARY KEY (`directoryid`),
  UNIQUE KEY `m_directory_domainid_path` (`domainid`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='情報';
