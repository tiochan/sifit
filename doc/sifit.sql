-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: localhost    Database: sifit
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `app_parameters`
--

DROP TABLE IF EXISTS `app_parameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_parameters` (
  `parameter` varchar(60) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `description` text,
  UNIQUE KEY `parameter` (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_parameters`
--

LOCK TABLES `app_parameters` WRITE;
/*!40000 ALTER TABLE `app_parameters` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_parameters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bug`
--

DROP TABLE IF EXISTS `bug`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bug` (
  `id_bug` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `username` varchar(60) DEFAULT NULL,
  `bug_description` text,
  `bug_content` text,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closing_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bug_description2` text,
  PRIMARY KEY (`id_bug`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bug`
--

LOCK TABLES `bug` WRITE;
/*!40000 ALTER TABLE `bug` DISABLE KEYS */;
/*!40000 ALTER TABLE `bug` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboards`
--

DROP TABLE IF EXISTS `dashboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dashboards` (
  `id_dashboard` mediumint(9) NOT NULL AUTO_INCREMENT,
  `dashboard_name` varchar(60) NOT NULL,
  `id_group` mediumint(9) DEFAULT NULL,
  `content` text,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_dashboard`),
  UNIQUE KEY `dashboard` (`dashboard_name`,`id_group`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboards`
--

LOCK TABLES `dashboards` WRITE;
/*!40000 ALTER TABLE `dashboards` DISABLE KEYS */;
INSERT INTO `dashboards` VALUES (1,'CRE_DASHBOARD_1',NULL,'<p>\r\n	<style type=\"text/css\">\r\nbody {\r\nbackground-color: #330066;\r\ncolor: #ddd;\r\n}	</style>\r\n</p>\r\n<h1>\r\n	CRE Dashboard #1</h1>\r\n<p>\r\n	<em>View data as user <strong>{USER_NAME}</strong>, level<strong> {USER_LEVEL_NAME}</strong>.</em></p>\r\n<p>\r\n	&nbsp;</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" height=\"186\" style=\"border: 1px solid rgb(221, 221, 221);\" width=\"562\">\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				<p>\r\n					<strong><span style=\"color:#fff0f5;\">Current Users:</span></strong></p>\r\n			</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				<p>\r\n					<strong><span style=\"color:#fff0f5;\">Current Downloads:</span></strong></p>\r\n			</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				<span style=\"color:#fff0f5;\"><strong>Bandwidth usage:</strong></span></td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=1;refresh_time=10}<em> <span style=\"color:#fff0f5;\"><em>10s refresh</em></span></em></span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=2;refresh_time=5}<span style=\"color:#fff0f5;\"><em> 5s refresh</em></span></span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=3;refresh_time=10} <span style=\"color:#fff0f5;\"><em>10s refresh</em></span></span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=4;refresh_time=10} <span style=\"color:#fff0f5;\"><em>5s refresh</em></span></span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=10;refresh_time=10} <span style=\"color:#fff0f5;\"><em>10s refresh</em></span></span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=11;refresh_time=5} <span style=\"color:#fff0f5;\"><em>5s refresh</em></span></span></td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{Z_TEST_GENERIC_GRAPH|ID=5;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}4</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{Z_TEST_GENERIC_GRAPH|ID=6;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{Z_TEST_GENERIC_GRAPH|ID=12;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KP 5</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KPI 6</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KPI 7</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KP 8</span></td>\r\n			<td>\r\n				&nbsp;</td>\r\n			<td>\r\n				&nbsp;</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	Here, some kind of graphs</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" style=\"border: solid 1px #ddd;width: 500px\">\r\n	<tbody>\r\n		<tr>\r\n			<td rowspan=\"4\" style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">This is a query, but instead using a dedicated tag, it uses a generic tag where the query is a parameter... awesome? </span>{GENERIC_QUERY|ID=20;QUERY=select month(mark_date), sum(minutes) from time_marks group by month(mark_date) LIMIT 3}</p>\r\n			</td>\r\n			<td>\r\n				<p>\r\n					<strong><span style=\"color:#fff0f5;\">Example for a Radar graph:</span></strong></p>\r\n				<p>\r\n					{EXAMPLE_GRAPH_RADAR|width=350;height=200;show_legend=false;top=30;bottom=30;left=30;right=30;}</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<p>\r\n					<strong><span style=\"color:#fff0f5;\">This is just a graph with auto-refresh:</span></strong></p>\r\n				<p>\r\n					{Z_TEST_GENERIC_GRAPH|ID=25;width=350;height=200;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<p>\r\n					<strong><span style=\"color:#fff0f5;\">Same graph, but with auto-refresh</span></strong></p>\r\n				<p>\r\n					{Z_TEST_PIE|ID=11;width=350;height=200;show_legend=false;top=30;bottom=30;left=30;right=30}</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<p>\r\n					<strong><span style=\"color:#fff0f5;\">Same graph, but with auto-refresh:</span></strong></p>\r\n				<p>\r\n					{Z_TEST_BAR_90|ID=11;width=350;height=200;show_legend=false;top=30;bottom=30;left=30;right=30}</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	Nice?</p>\r\n',''),(2,'CRE_DASHBOARD_1_COPY',NULL,'<p>\r\n	<style type=\"text/css\">\r\nbody {\r\nbackground-color: #330066;\r\ncolor: #ddd;\r\n}	</style>\r\n</p>\r\n<h1>\r\n	CRE Dashboard #1</h1>\r\n<p>\r\n	&nbsp;</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" style=\"border: solid 1px #ddd;width: 500px\">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				KPI 1</td>\r\n			<td>\r\n				KPI 2</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				KPI 3</td>\r\n			<td>\r\n				KPI 4</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				KP 5</td>\r\n			<td>\r\n				KPI 6</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	Here, some kind of graphs</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" style=\"border: solid 1px #ddd;width: 500px\">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				This is just a graph:<br />\r\n				{Z_TEST_GENERIC_GRAPH|width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30}</td>\r\n			<td>\r\n				This is a query, but instead using a dedicated tag, it uses a generic tag where the query is a parameter... awesome? {GENERIC_QUERY_LOCAL|QUERY=select month(mark_date), sum(minutes) from time_marks group by month(mark_date)}</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				Same graph, but with auto-refresh:<br />\r\n				{Z_TEST_GENERIC_GRAPH|width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=5000} KPI 3</td>\r\n			<td>\r\n				KPI 4</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				KP 5</td>\r\n			<td>\r\n				KPI 6</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	Nice?</p>\r\n',''),(3,'CRE_DASHBOARD_SERVICES',NULL,'<p>\r\n	<style type=\"text/css\">\r\nbody {\r\n/*background-color: #330066;*/\r\nbackground-color: #444;\r\ncolor: #ddd;\r\n}	</style>\r\n</p>\r\n<h1>\r\n	CRE Dashboard #2:<em> Overall status</em></h1>\r\n<p>\r\n	<em>(View data as user <strong>{USER_NAME}</strong>, level<strong> {USER_LEVEL_NAME}</strong>.)</em></p>\r\n<p>\r\n	&nbsp;</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" height=\"186\" style=\"border: 1px solid rgb(221, 221, 221);\" width=\"562\">\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"2\" style=\"background-color: rgb(102, 102, 102);\">\r\n				<h1 style=\"text-align: center;\">\r\n					<span style=\"color:#fff0f5;\">SCHIP</span></h1>\r\n			</td>\r\n			<td colspan=\"2\" style=\"background-color: rgb(102, 102, 102);\">\r\n				<h1 style=\"text-align: center;\">\r\n					<span style=\"color:#fff0f5;\">ZEUS</span></h1>\r\n			</td>\r\n			<td colspan=\"2\" style=\"background-color: rgb(102, 102, 102);\">\r\n				<h1 style=\"text-align: center;\">\r\n					<span style=\"color:#fff0f5;\">TARDIS</span></h1>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Blocked pods:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=1;refresh_time=10}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Hosts down:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=2;refresh_time=10}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>EMR Status:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_3|ID=3}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Memory usage:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=4;refresh_time=10}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Queue length:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_2|ID=10;refresh_time=10}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Depend. status:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{Z_INDICATOR_TEST_3|ID=11;KPI_VALUE=Down} </span></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{Z_TEST_GENERIC_GRAPH_2|ID=5;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}4</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{Z_TEST_GENERIC_GRAPH|ID=6;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{Z_TEST_GENERIC_GRAPH|ID=12;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KP 5</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KPI 6</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KPI 7</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KP 8</span></td>\r\n			<td>\r\n				&nbsp;</td>\r\n			<td>\r\n				&nbsp;</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	&nbsp;</p>\r\n','');
/*!40000 ALTER TABLE `dashboards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id_group` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`id_group`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'SIFIT Adm','SIFIT Administrators group');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `log_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `username` varchar(30) DEFAULT NULL,
  `user_level` int(11) NOT NULL DEFAULT '0',
  `host` varchar(100) DEFAULT NULL,
  `module` varchar(25) DEFAULT NULL,
  `action` text,
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_subscription`
--

DROP TABLE IF EXISTS `report_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_subscription` (
  `id_rs` mediumint(9) NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(9) NOT NULL,
  `id_report` mediumint(9) NOT NULL,
  PRIMARY KEY (`id_rs`),
  UNIQUE KEY `rs_unique` (`id_user`,`id_report`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_subscription`
--

LOCK TABLES `report_subscription` WRITE;
/*!40000 ALTER TABLE `report_subscription` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_subscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_tags`
--

DROP TABLE IF EXISTS `report_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_tags` (
  `id_tag` mediumint(9) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(60) NOT NULL,
  `calc_method` varchar(60) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `value` text,
  `extrainfo` text,
  `connection` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `id_user` mediumint(9) DEFAULT NULL,
  `id_group` mediumint(9) DEFAULT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_tags`
--

LOCK TABLES `report_tags` WRITE;
/*!40000 ALTER TABLE `report_tags` DISABLE KEYS */;
INSERT INTO `report_tags` VALUES (3,'OP_DATE_TODAY','operation','Current date in format dd-mm-yyyy','date(\\\"d-m-Y\\\")',NULL,NULL,1,NULL,NULL,1),(4,'OP_DATE_LAST_WEEK','operation','Date one week back in format dd-mm-yyyy','date(\\\"d-m-Y\\\",strtotime(\\\"-1 week\\\"))',NULL,NULL,1,NULL,NULL,1),(5,'USER_GROUP','system_var','','USER_GROUP',NULL,NULL,1,NULL,NULL,1),(6,'USER_ID','system_var','','USER_ID',NULL,NULL,1,NULL,NULL,1),(10,'USER_LEVEL','system_var','','USER_LEVEL',NULL,NULL,1,NULL,NULL,1),(11,'USER_GROUP_NAME','system_var','','USER_GROUP_NAME',NULL,NULL,1,NULL,NULL,1),(12,'USER_NAME','system_var','','USER_NAME',NULL,NULL,1,NULL,NULL,1),(13,'USER_LEVEL_NAME','system_var','','USER_LEVEL_NAME',NULL,NULL,1,NULL,NULL,1),(15,'IMG_LOGO_UPCNET','image','Mostra el logo d\\\'UPCnet','/include/images/logo_upcnet.png',NULL,NULL,1,NULL,NULL,0),(30,'OP_DATE_TODAY_MONTH','operation','Current date in format mm','date(\\\"m\\\")',NULL,NULL,1,NULL,NULL,1),(31,'OP_DATE_TODAY_YEAR','operation','Current date in format yyyy','date(\\\"Y\\\")',NULL,NULL,1,NULL,NULL,1),(131,'OP_DAY_OF_WEEK','operation','Returns the day of week (0 - 6)','date(\"w\")','',NULL,1,NULL,NULL,1),(132,'OP_LAST_SATURDAY','operation','','\"current_date - INTERVAL \" . ( date(\"w\") +1 ) . \" day\"','',NULL,1,NULL,NULL,1),(133,'OP_LAST_SUNDAY','operation','','\"current_date - INTERVAL \" . date(\"w\")  . \" day\"','',NULL,1,NULL,NULL,1),(134,'OP_LAST_MONDAY','operation','','\"current_date - INTERVAL \" . ( date(\"w\") +7 ) . \" day\"','',NULL,1,NULL,NULL,1),(135,'CONS_DATE_FORMAT','constant','The format (for PHP functions) of the given dates.','%d/%m/%Y','','',1,NULL,NULL,1),(137,'CONS_DATE_FORMAT_PHP','constant','The format (for PHP functions) of the given dates.','d/m/Y','','',1,NULL,NULL,1),(172,'CONS_DATE_FORMAT_SQL','constant','The format (for SQL queries) of the given dates.','Y/m/d','','',1,NULL,NULL,1),(173,'CONS_DATE_TIME_FORMAT','constant','The format (for PHP functions) of the given dates.','%d/%m/%Y %H:%M:%S','','',1,NULL,NULL,1),(174,'CONS_DATE_TIME_FORMAT_PHP','constant','The format (for PHP functions) of the given dates.','d/m/Y H:i:s','','',1,NULL,NULL,1),(175,'CONS_DATE_TIME_FORMAT_SQL','constant','The format (for SQL queries) of the given dates.','Y/m/d H:i:s','','',1,NULL,NULL,1),(176,'EXAMPLE_MARK_MONTHLY_REPORT','php_code','','include_once INC_DIR . \"/forms/form_elements.inc.php\";\r\ninclude_once MY_INC_DIR . \"/classes/fb_date_selector.class.php\";\r\ninclude_once MY_INC_DIR . \"/classes/dw_marks.php\";\r\ninclude_once MY_INC_DIR . \"/classes/marks_calendar.class.php\";\r\n\r\nglobal $MESSAGES;\r\n\r\n$date_selector= new plain_date_selector(\"0\");\r\n$calendar= new mark_calendar(\"mark_calendar_1\", $dw_marks);\r\n$dw_marks= new dw_marks($date_selector);\r\n\r\n\r\necho \"<div style=\'align:left;\'>\";\r\necho \"<b>Periode des de: \" . $date_selector->start_date . \" fins al \" . $date_selector->end_date . \"<br><br><br>\";\r\n\r\n$calendar->show();\r\necho \"<hr>\";\r\n$dw_marks->show();\r\n\r\n\r\necho \"</div><br><br><br><br>\";\r\n','TO_NAME={USER_NAME}','APP_GENERIC_CONN',1,NULL,NULL,0),(177,'EXAMPLE_GRAPH_DAY_POLL_CURRENT_MONTH','generic_graph_linear','','{GRAPH_DAY_POLL_CURRENT_MONTH_QUERY}','ROTATE_LABELS=true;\r\nCOL_NAMES=Date,poll;','',1,NULL,NULL,0),(178,'ProvaHTTP','http','','http://www.upc.edu','','',1,NULL,NULL,0),(184,'Z_TEST_GENERIC_GRAPH','generic_graph','','<GRAPH_SERIE>1,800\r\n2,800\r\n3,800\r\n4,800\r\n5,800\r\n6,800\r\n7,800\r\n<GRAPH_SERIE>{Z_TEST_SCRIPT}\r\n<GRAPH_SERIE>{Z_TEST_SHELL_DATA}\r\n<GRAPH_SERIE>{Z_TEST_HTTP_DATA}\r\n<GRAPH_SERIE>{Z_TEST_SHELL_DATA_GENERIC|DOCUMENT=resultats4.txt}','COL_NAMES=Data from SHELL,Data from script,HTTP Data,Generic data;\r\nWIDTH=800;\r\nTHEME=Universal;\r\n--GRAPH_TYPE=BarPlot;\r\nGRAPH_TYPE=LinePlot;\r\nTITLE=Generic Graph;\r\nSUBTITLE=This is a generic graph with series;\r\nSHOW_VALUES=false;\r\nSHOW_LEGEND=true;\r\nACCUMULATED=true;\r\nSERIE1_TYPE=LinePlot;\r\nSERIE1_LINE_STYLE=dashed;\r\nSERIE1_COLOR=red;\r\nSERIE1_SHOW_VALUES=false;\r\n--SERIE2_FILL_COLOR=khaki;\r\nSERIE2_TYPE=BarPlot;\r\nSERIE2_SHOW_VALUES=true;','',1,1,1,0),(185,'Z_TEST_SHELL_DATA','system_command','','cat /var/www/html/reports/test/resultats.txt','','',1,1,1,0),(186,'Z_TEST_HTTP_DATA','http','','http://localhost/reports/test/resultats2.txt','','',1,1,1,0),(187,'Z_TEST_PHP_DATA','php_code','','echo file_get_contents(SYSHOME . \"/test/resultats.txt\");','','',1,1,1,0),(188,'Z_TEST_SCRIPT','script','','echo_values','','',1,1,1,0),(191,'Z_TEST_SHELL_DATA_GENERIC','system_command','','cat /var/www/html/reports/test/{$DOCUMENT}','DOCUMENT=resultats4.txt','',1,1,1,0),(193,'Z_TEST_QUERY_DATA','query','--WHERE vote_date >= \'{OP_DATE_TODAY_YEAR}/{OP_DATE_TODAY_MONTH}/01\'\r\n-- SELECT vote_date, vote FROM day_poll','select month(mark_date), sum(minutes) from time_marks group by month(mark_date);\r\n','CSV=false;\r\nSHOW_FIELD_NAMES=true;','APP_GENERIC_CONN',1,1,1,0),(204,'GENERIC_QUERY','query','Generic query abstractor TAG.\r\nSet QUERY parameter and check other parameters too to customize output.','{$QUERY}','CSV=false;\r\nSHOW_NO_DATA=false;\r\nSHOW_FIELD_NAMES=false;','APP_GENERIC_CONN',1,1,1,1),(205,'GRAPH_DAY_POLL_CURRENT_MONTH_QUERY','query','','SELECT vote_date, vote\r\nFROM day_poll\r\nWHERE id_user=\'{USER_ID}\' AND\r\nvote_date >= \'{OP_DATE_TODAY_YEAR}/{OP_DATE_TODAY_MONTH}/01\'','CSV=true;\r\nSHOW_FIELD_NAMES=false;','APP_GENERIC_CONN',1,1,1,0),(206,'EXAMPLE_GENERIC_QUERY','constant','','{GENERIC_QUERY|QUERY=select month(mark_date), sum(minutes) from time_marks group by month(mark_date);CSV={$CSV}}','CSV=false','',1,1,1,0),(208,'Z_TEST_SEARCH','search','','{ProvaHTTP}','REGEXP=temperatures nuvolsalts(.*)</p>','',1,1,1,0),(209,'Z_TEST_HTML_UPC','http','','http://www.upc.edu','','',1,1,1,0),(210,'Z_TEST_DIV_EXTRACT','html_extract','','--','OBJECT_TYPE=div;\r\n--OBJECT_CLASS=meteoBox;\r\nOBJECT_CLASS=columna_3;\r\n--OBJECT_ID=baners;\r\n--OBJECT_ID=beautytab dostab tabNews;\r\n--OBJECT_CLASS=wrapper;\r\nURL=www.upc.edu;\r\n--OBJECT_ID=noticias_ibex;\r\n--URL=www.expansion.com/mercados/cotizaciones/indices/ibex35_I.IB.html;','',1,1,1,0),(213,'EXAMPLE_HTML_EXTRACT','html_extract','','--','URL=www.324.cat/;\r\nOBJECT_CLASS=FonsModulDiners;\r\nINCLUDE_SCRIPTS=0;\r\n--INCLUDE_STYLES=0;','',1,1,1,0),(214,'X_INFOJOBS_SANTSADURNI','html_extract','','--','OBJECT_TYPE=table;\r\nOBJECT_ID=table_results;\r\nURL=http://www.infojobs.net/ofertas-trabajo/barcelona/sant-sadurnÃ­-d\'anoia;','',1,1,1,0),(224,'X_INFOJOBS_SANT_PERE_RIUDEBITLLES','html_extract','','--','OBJECT_TYPE=table;\r\nOBJECT_ID=table_results;\r\nURL=http://www.infojobs.net/ofertas-trabajo/sant-pere-de-riudebitlles;','',1,NULL,NULL,0),(229,'Z_INDICATOR_TEST_1','html','','<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" height=\"78\" width=\"100\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"text-align: center; background-color: rgb(0, 204, 0);\">\r\n				<span style=\"color:#fff0f5;\"><span style=\"font-size:36px;\"><strong>{Z_TEST_QUERY}</strong> </span> </span></td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n','','',1,1,1,0),(230,'Z_TEST_QUERY','query','-- select round(avg(minutes),1) from time_marks;','SELECT FLOOR(7 + (RAND() * 5));\r\n','','APP_GENERIC_CONN',1,1,1,0),(231,'Z_TEST_PIE','generic_graph_pie_perc','','{Z_TEST_PHP_DATA}','','',1,1,1,0),(232,'Z_TEST_BAR_90','generic_graph_bar_90','','{Z_TEST_PHP_DATA}','','',1,1,1,0),(233,'KPI_Integer','html','','<div id=\"{$RANDOM_ID}\" style=\"display: block; width: 100px; height:80px; font-size:24px; text-align: center;vertical-align: middle;line-height: 90px;\">\r\n	{$KPI_VALUE}</div>\r\n<p>\r\n	{KPI_Colorize_Integer|DIV_ID={$RANDOM_ID};VALUE={$KPI_VALUE}}</p>\r\n','-- KPI_VALUE=THIS VALUE MUST BE SET;\r\n-- WARN_LIMIT=THIS VALUE MUST BE SET;\r\n-- CRIT_LIMIT=THIS VALUE MUST BE SET;\r\n-- lower is better?;\r\n--KPI_VALUE=This value must be set;\r\nKPI_VALUE=1;\r\nLOWER_IS_BETTER=1;\r\nRANDOM_ID={RANDOM};\r\n','',1,NULL,NULL,0),(234,'RANDOM','php_code','','echo rand({$MIN},{$MAX});','MIN=0;\r\nMAX=999;','',1,1,1,0),(236,'KPI_Colorize_Integer','javascript','--DIV_ID=This parameter must be set;\r\n--VALUE=This parameter must be set;\r\n--WARN_VALUE=Limit to paint it as a Warning;\r\n--CRIT_LEVEL=Limit to paint it as a Critical;','var div_id=\'{$DIV_ID}\'\r\nvar div_obj=document.getElementById(div_id);\r\nif(div_obj == null) {\r\n  console.log(\"Error: Div id not found \'\" + div_id + \"\'\");\r\n  exit;\r\n}\r\nvar value={$VALUE};\r\nif(value < {$WARN_LIMIT}) {\r\n  div_obj.style.backgroundColor=\"{$NORMAL_BG_COLOR}\";\r\n  div_obj.style.color=\"{$NORMAL_FG_COLOR}\";\r\n} else if (value < {$CRITICAL_LIMIT}) {\r\n  div_obj.style.backgroundColor=\"{$WARN_BG_COLOR}\";\r\n  div_obj.style.color=\"{$WARN_FG_COLOR}\";\r\n} else {\r\n  div_obj.style.backgroundColor=\"{$CRITICAL_BG_COLOR}\";\r\n  div_obj.style.color=\"{$CRITICAL_FG_COLOR}\";\r\n}\r\n','CRITICAL_LIMIT=90;\r\nWARN_LIMIT=75;\r\nCRITICAL_BG_COLOR=red;\r\nCRITICAL_FG_COLOR=white;\r\nWARN_BG_COLOR=yellow;\r\nWARN_FG_COLOR=black;\r\nNORMAL_BG_COLOR=#393;\r\nNORMAL_FG_COLOR=white;\r\n','',1,1,1,0),(237,'EXAMPLE_KPI_TEST','html','','<p>\r\n	{KPI_Integer|KPI_VALUE=95}</p>\r\n','','',1,1,1,0),(238,'Z_INDICATOR_TEST_2','html','','<p>\r\n	{KPI_Integer|KPI_VALUE={$KPI_VALUE}}</p>\r\n','KPI_VALUE={RANDOM|MAX=100};\r\n','',1,NULL,NULL,0),(239,'GENERIC_GRAPH','generic_graph','Generic GRAPH abstraction.\r\nSet VALUES parameter and also check the other TAG Type parameters to customize your output.','{$VALUES}','','',1,1,1,1),(240,'EXAMPLE_INDIRECT_REFERENCE','constant','How to call a TAG which name is not fixed, and will be given via PARAMETER.','{{$TAG_NAME}}','TAG_NAME=0_TEST_QUERY_GENERIC','',1,1,1,0),(241,'EXAMPLE_GRAPH_RADAR','generic_graph_radar','','KPI,CRE Services\r\nSCHIP,10\r\nTARDIS,5\r\nZEUS,8\r\nJaaS,7\r\nWS,3','TITLE=CRE Services global agreement;\r\nTHEME=Orange;','',1,1,1,0),(242,'KPI_Colorize_Status','javascript','--DIV_ID=This parameter must be set;\r\n--VALUE=This parameter must be set;\r\n--OK_VALUE=Value as Ok status\r\n--WARN_VALUE=Value as Warning  status\r\n--CRIT_VALUE=Value as Critical status','var div_id=\'{$DIV_ID}\'\r\nvar div_obj=document.getElementById(div_id);\r\nif(div_obj == null) {\r\n  console.log(\"Error: Div id not found \'\" + div_id + \"\'\");\r\n  exit;\r\n}\r\nvar value=\'{$VALUE}\';\r\nif(value == \'{$OK_VALUE}\') {\r\n  div_obj.style.backgroundColor=\"{$NORMAL_BG_COLOR}\";\r\n  div_obj.style.color=\"{$NORMAL_FG_COLOR}\";\r\n} else if (value == \'{$WARN_VALUE}\') {\r\n  div_obj.style.backgroundColor=\"{$WARN_BG_COLOR}\";\r\n  div_obj.style.color=\"{$WARN_FG_COLOR}\";\r\n} else if (value == \'{$CRIT_VALUE}\') {\r\n  div_obj.style.backgroundColor=\"{$CRITICAL_BG_COLOR}\";\r\n  div_obj.style.color=\"{$CRITICAL_FG_COLOR}\";\r\n} else {\r\n  div_obj.style.backgroundColor=\"white\";\r\n  div_obj.style.color=\"black\";\r\n}\r\n','OK_VALUE=Up;\r\nWARN_VALUE=unknown;\r\nCRIT_VALUE=Down;\r\nCRITICAL_BG_COLOR=red;\r\nCRITICAL_FG_COLOR=white;\r\nWARN_BG_COLOR=yellow;\r\nWARN_FG_COLOR=black;\r\nNORMAL_BG_COLOR=#393;\r\nNORMAL_FG_COLOR=white;\r\n','',1,NULL,NULL,0),(243,'KPI_Status','html','KPI Box to display a string showing a status','<div id=\"{$RANDOM_ID}\" style=\"display: block; width: 100px; height:80px; font-size:24px; text-align: center;vertical-align: middle;line-height: 90px;\">\r\n	{$KPI_VALUE}</div>\r\n<p>\r\n	{KPI_Colorize_Status|DIV_ID={$RANDOM_ID};VALUE={$KPI_VALUE}}</p>\r\n','KPI_VALUE=1;\r\nRANDOM_ID={RANDOM};\r\n','',1,NULL,NULL,0),(244,'Z_INDICATOR_TEST_3','html','','<p>\r\n	{KPI_Status|KPI_VALUE={$KPI_VALUE}}</p>\r\n','KPI_VALUE=Up;\r\n','',1,NULL,NULL,0),(245,'Z_TEST_GENERIC_GRAPH_2','generic_graph','','1,{RANDOM|ID=1}\r\n2,{RANDOM|ID=2}\r\n3,{RANDOM|ID=3}\r\n4,{RANDOM|ID=4}\r\n5,{RANDOM|ID=5}\r\n6,{RANDOM|ID=6}\r\n7,{RANDOM|ID=7}\r\n8,{RANDOM|ID=8}\r\n9,{RANDOM|ID=9}','WIDTH=800;\r\nTHEME=Orange;\r\nGRAPH_TYPE=LinePlot;\r\nTITLE=Generic Graph;\r\nSHOW_VALUES=true;\r\nSHOW_LEGEND=false;','',1,NULL,NULL,0);
/*!40000 ALTER TABLE `report_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id_report` mediumint(9) NOT NULL AUTO_INCREMENT,
  `report_name` varchar(60) DEFAULT NULL,
  `id_group` mediumint(9) DEFAULT NULL,
  `content` text,
  `description` varchar(255) DEFAULT NULL,
  `periodicity` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id_report`),
  UNIQUE KEY `report` (`report_name`,`id_group`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (9,'Example - Mark reports',NULL,'<p>\r\n	Hi {USER_NAME},</p>\r\n<p>\r\n	This is your mark report for the current month:</p>\r\n<p>\r\n	{EXAMPLE_MARK_MONTHLY_REPORT}</p>\r\n','','working_daily'),(10,'Histeria TRENDS',NULL,'<h2>\r\n	Histeria trend graph for current month</h2>\r\n<p>\r\n	{OP_DATE_TODAY_YEAR}/{OP_DATE_TODAY_MONTH}/01</p>\r\n<p>\r\n	{EXAMPLE_GRAPH_DAY_POLL_CURRENT_MONTH}</p>\r\n','','working_daily'),(12,'Example - HTTP Extracts',NULL,'<h2>\r\n	Search for jobs in the nearbys</h2>\r\n<h2>\r\n	&nbsp;</h2>\r\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"data_box_rows\" height=\"94\" style=\"border: 1px solid rgb(170, 187, 170); padding: 15px;\" width=\"720\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(170, 187, 170);\">\r\n				<h2>\r\n					<strong>Loc.</strong></h2>\r\n			</td>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(170, 187, 170);\">\r\n				<h2>\r\n					<strong>Results</strong></h2>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan=\"2\" style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(238, 238, 238);\">\r\n				<p>\r\n					<strong>Sant Pere de Riudebitlles</strong></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				&nbsp;</td>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				<p>\r\n					{X_INFOJOBS_SANT_PERE_RIUDEBITLLES|INCLUDE_SCRIPTS=0;INCLUDE_STYLES=0}</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan=\"2\" style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(238, 238, 238);\">\r\n				<p>\r\n					<strong>Sant Sadurn&iacute;</strong></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				&nbsp;</td>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				<p>\r\n					{X_INFOJOBS_SANTSADURNI|INCLUDE_SCRIPTS=0;INCLUDE_STYLES=0}</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	&nbsp;</p>\r\n<hr />\r\n<h2>\r\n	&nbsp;</h2>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n','','daily'),(13,'MAIN_PAGE',NULL,'<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Welcome {USER_NAME}.</span></span></p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">This is the main page.</span></span></p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Please, <a href=\"./tools/reports.php\">go to the reports section</a> in order to create and manage your reports and dashboards. </span></span></p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">There you will find:</span></span></p>\r\n<ul>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">TAGs, which are the pieces that you use to build reports and even other tags. You can create TAGs of many different types, for example:</span></span></p>\r\n		<ul>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Graphs,</strong> of many differents types (bar, line, pie, ...)</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Constants,</strong> which value does not change</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>HTML,</strong> which allows you to add rich-text</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>HTTP,</strong> gets the content from a URL</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>HTTP Extract,</strong> parse the content from a URL</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Query,</strong> which will execute a SQL sentence</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Php code,</strong> this means that you can add your own PHP code here and will be executed on the fly</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>System commands, </strong>as you can imagine, execute anything executable on your system</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Search, </strong>to extract parts of a given input</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">And more...</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">And also<strong> build your own</strong> TAGs types, just inherit the main class and code it.</span></span></p>\r\n			</li>\r\n		</ul>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">The TAGs are evaluated in cascade and recursivelly, meaning that if you have a Report with N tags, they will be evaluated as they are defined on the Report, but for each TAG, it is evaluated in depth before going for the next TAG. This is because... </span></span></p>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">You can use the output of one TAG as input for another, for example, use a TAG of type query as value for a Graph, and it will represent the resulting data.</span></span></p>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Also you can use TAGs into the value definition of other TAGs, for example, into a Query TAG you can insert other TAGs as {USER_NAME}. As mentioned before, the TAGs are evaluated in depth, so, the Query will not be evaluated until are evaluated all the TAGs it contains, and this process is recursive.</span></span></p>\r\n	</li>\r\n</ul>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Have a nice day!</span></span></p>\r\n','','never'),(14,'CRE_DASHBOARD1',NULL,'<style type=\"text/css\">\r\nbody {\r\nbackground-color: #330066;\r\ncolor: #ddd;\r\n}</style>\r\n<h1>\r\n	CRE Dashboard #1</h1>\r\n<p>\r\n	&nbsp;</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" style=\"border: solid 1px #ddd;width: 500px\">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				KPI 1</td>\r\n			<td>\r\n				KPI 2</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				KPI 3</td>\r\n			<td>\r\n				KPI 4</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				KP 5</td>\r\n			<td>\r\n				KPI 6</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	Here, some kind of graphs</p>\r\n<p>\r\n	{Z_TEST_GENERIC_GRAPH}</p>\r\n<p>\r\n	&nbsp;</p>\r\n','','never');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id_ptl` mediumint(9) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(60) NOT NULL,
  `script` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `periodicity` varchar(25) DEFAULT NULL,
  `hour` varchar(5) DEFAULT NULL,
  `send_report` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_ptl`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,'02. Report launcher','/include/cron/report_launcher.php','','daily','07:00',1);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_marks`
--

DROP TABLE IF EXISTS `time_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_marks` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `mark_date` date NOT NULL,
  `marks` varchar(255) DEFAULT NULL,
  `minutes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_mark` (`user_id`,`mark_date`)
) ENGINE=MyISAM AUTO_INCREMENT=271 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_marks`
--

LOCK TABLES `time_marks` WRITE;
/*!40000 ALTER TABLE `time_marks` DISABLE KEYS */;
INSERT INTO `time_marks` VALUES (7,216,'2012-08-02','08:00, 14:00',360),(9,216,'2012-08-08','08:00, 14:10,15:10,17:30',510),(3,216,'2012-07-17','08:00',0),(4,216,'2012-08-07','08:00, 14:00,15:35,16:30',415),(5,216,'2012-08-06','08:00, 14:10, 15:30, 17:30',490),(6,216,'2012-08-09','08:00, 09:00, 10:00, 14:15, 15:30, 17:20',425),(8,216,'2012-08-05','09:00, 14:00',300),(10,216,'2012-08-10','08:00, 14:10, 15:30, 17:30',490),(11,216,'2012-08-01','07:45, 14:25, 15:30, 17:30',520),(12,216,'2012-08-03','08:40, 14:10',330),(13,216,'2012-08-31','07:45, 14:25, 15:30, 17:30',520),(14,216,'2012-08-13','07:45, 14:25, 15:30, 17:30',520),(15,216,'2012-09-03','07:30, 13:30',360),(16,216,'2012-09-04','08:00, 14:00, 15:30, 16:30',420),(17,216,'2012-09-05','08:00, 14:00, 15:30, 16:30',420),(18,216,'2012-09-06','07:30,14:25',415),(19,190,'2012-09-06','11:22',0),(23,216,'2012-09-07','09:50, 14:00',250),(22,294,'2012-09-07','08:15,08:41,08:41',26),(24,294,'2012-09-10','07:49,16:55',546),(25,216,'2012-09-10','07:45,14:00',375),(26,216,'2012-09-12','07:30,14:05,14:33,17:25',567),(27,294,'2012-09-12','07:49,14:05',376),(28,294,'2012-09-13','08:04,14:00,14:32,19:13',637),(29,216,'2012-09-13','08:24,14:02,14:32,16:57',483),(30,294,'2012-09-14','07:47,14:01',374),(31,216,'2012-09-14','08:30,14:02',332),(32,216,'2012-09-17','07:56,14:02,14:40, 17:22',528),(33,216,'2012-09-11','08:00, 15:00',420),(34,294,'2012-09-17','08:02,13:41',339),(35,294,'2012-09-18','07:49,14:00,14:30,18:46',627),(36,216,'2012-09-18','09:05,13:59,14:30,17:24',468),(37,216,'2012-09-19','8:00,15:07',427),(38,294,'2012-09-19','07:50,14:12,14:35,17:16',543),(39,216,'2012-09-20','07:41,14:06,14:45, 17:06',526),(40,294,'2012-09-20','07:49,14:09,14:55,17:17',522),(41,216,'2012-09-21','07:31,14:02',391),(42,294,'2012-09-21','07:45,14:06',381),(43,216,'2012-09-25','06:51,14:59',488),(44,294,'2012-09-25','07:39,14:05,14:29,17:00',537),(45,216,'2012-09-26','07:34,15:11',457),(46,294,'2012-09-26','07:41,14:09,14:24,17:13',557),(47,216,'2012-09-27','07:02,14:00',418),(48,294,'2012-09-27','08:06,14:13,14:35,18:46',618),(49,216,'2012-09-28','07:00',0),(50,294,'2012-09-28','07:40,14:11',391),(51,294,'2012-10-01','07:48,14:12,14:35,17:10',539),(52,216,'2012-10-01','08:03,15:01',418),(53,216,'2012-10-02','07:25,15:05',460),(54,294,'2012-10-02','07:37,14:13,14:36,17:51',591),(55,216,'2012-10-03','07:26,14:07,14:59,15:21',423),(56,294,'2012-10-03','07:54,14:10,14:32,17:55',579),(57,216,'2012-10-04','06:55,09:30, 12:45,14:00, 14:30,20:28',588),(58,294,'2012-10-04','07:42,14:30,14:47,21:01',782),(59,216,'2012-10-05','07:34',0),(60,294,'2012-10-05','08:38,17:41',543),(61,216,'2012-10-08','07:43,15:10',447),(62,294,'2012-10-08','07:50,14:21,14:45,18:29',615),(63,216,'2012-10-09','07:42,15:02',440),(64,294,'2012-10-09','07:53,14:05,14:31,17:41',562),(66,294,'2012-10-10','07:59,14:05,14:29,18:02',579),(67,190,'2012-10-10','08:04',0),(68,216,'2012-10-10','08:32,15:07',395),(69,216,'2012-10-11','06:48,13:57',429),(70,294,'2012-10-11','07:45,14:18,14:42,17:54',585),(71,294,'2012-10-15','08:02,14:02,14:30,17:00',510),(72,190,'2012-10-15','08:16',0),(73,216,'2012-10-15','08:29,15:06',397),(74,216,'2012-10-16','07:29,15:08',459),(75,294,'2012-10-16','07:58,14:16,14:39,18:52',631),(76,216,'2012-10-17','07:32,15:00, 15:30,17:19',557),(77,190,'2012-10-17','07:57',0),(78,294,'2012-10-17','08:00,14:13,14:35,17:17',535),(79,216,'2012-10-18','07:31,14:05,14:34,17:12',552),(80,294,'2012-10-18','07:52,14:05,14:34,17:52',571),(81,216,'2012-10-12','',420),(82,216,'2012-10-19','07:14,14:02',408),(83,294,'2012-10-19','8:00,14:04',364),(84,294,'2012-10-22','07:51,14:05,14:28,17:57',583),(85,216,'2012-10-22','08:46,14:03,14:52,16:39',424),(86,216,'2012-10-23','07:37,14:11,14:41,17:22',555),(87,294,'2012-10-23','07:45,14:13,14:41,17:46',573),(88,216,'2012-10-24','07:08,14:03,14:22,15:14',467),(89,294,'2012-10-24','07:42,14:02,14:23,16:57',534),(90,294,'2012-10-25','07:24,14:02,14:25,17:49',602),(91,216,'2012-10-25','08:11,14:00, 15:00, 16:00',409),(92,216,'2012-10-26','07:37,13:58',381),(93,294,'2012-10-26','07:54,14:00',366),(94,190,'2012-10-27','14:30',0),(95,294,'2012-10-29','07:45,14:34,14:52,17:12',549),(96,216,'2012-10-29','09:10,14:30, 15:00, 17:26',466),(97,294,'2012-10-30','07:24,14:05,14:28,17:09',562),(98,216,'2012-10-30','07:30,14:05,14:28,17:21',568),(99,216,'2012-10-31','07:26,14:56',450),(100,294,'2012-10-31','07:57,14:06,14:34,17:10',525),(101,294,'2012-11-02','07:49,14:09',380),(102,294,'2012-11-05','08:01,14:06,14:34,17:15',526),(103,216,'2012-11-05','08:00,14:06,14:35,17:24',535),(104,216,'2012-11-06','07:40,17:01',561),(105,294,'2012-11-06','07:51,14:30,14:50,17:02',531),(106,216,'2012-11-07','07:42,15:00',438),(107,294,'2012-11-07','07:57,14:21,14:47,17:55',572),(108,216,'2012-11-08','07:33,17:27',594),(109,294,'2012-11-08','07:44,14:04,14:19,18:02',603),(110,216,'2012-11-09','06:58,13:57',419),(111,294,'2012-11-09','07:50,13:58',368),(112,294,'2012-11-12','07:10,14:07,14:41',417),(113,216,'2012-11-13','07:07,16:38',571),(114,216,'2012-11-12','7:30,14:00,14:30,17:15',555),(115,294,'2012-11-13','07:30,14:30,14:50,18:50',660),(116,294,'2012-11-14','07:58,14:10',372),(117,294,'2012-11-15','07:44,14:00,14:25,18:14',605),(118,216,'2012-11-15','08:05,14:30',385),(119,294,'2012-11-16','07:41,14:05',384),(120,216,'2012-11-16','09:15,14:02',287),(121,216,'2012-11-19','07:32,13:59,14:45,17:12',534),(122,294,'2012-11-19','07:56,13:59,14:22,17:14',535),(123,294,'2012-11-20','07:49,17:17',568),(124,216,'2012-11-20','08:53,14:04,14:45,17:19',465),(125,216,'2012-11-21','07:31,15:19',468),(126,294,'2012-11-21','07:52,14:03,14:22,18:47',636),(127,294,'2012-11-22','07:56,14:10,14:32,16:55',517),(128,216,'2012-11-22','08:10,14:05,14:32,16:45',488),(129,294,'2012-11-23','07:40,14:03',383),(130,216,'2012-11-23','7:50,14:08',378),(131,216,'2012-11-26','07:33,14:59',446),(132,294,'2012-11-26','07:49,16:46',537),(133,294,'2012-11-27','07:51,17:28',577),(134,216,'2012-11-27','08:50,14:00, 14:30,16:37',437),(135,216,'2012-11-28','07:30,14:01,14:47,17:15',539),(136,294,'2012-11-28','07:53,18:43',650),(137,216,'2012-11-29','07:31,17:19',588),(138,294,'2012-11-29','07:47',0),(139,216,'2012-11-30','07:28,14:02',394),(140,294,'2012-11-30','14:03',0),(141,294,'2012-12-03','07:44,14:03,14:30,17:02',531),(142,216,'2012-12-03','09:30,14:04,14:27,16:42',409),(143,294,'2012-12-04','07:39,17:49',610),(144,216,'2012-12-04','09:00, 14:05, 14:31,16:49',443),(145,216,'2012-12-05','07:21,14:09',408),(146,294,'2012-12-05','08:14',0),(147,294,'2012-12-10','07:28,14:00,14:24,18:01',609),(148,216,'2012-12-10','07:58,14:00, 14:30,17:25',537),(149,216,'2012-12-11','08:39,16:27',468),(150,216,'2012-12-12','07:32,14:02,14:32',390),(151,294,'2012-12-12','07:36,14:03,14:32,17:54',589),(152,216,'2012-12-13','07:40,14:14',394),(153,294,'2012-12-13','07:50',0),(154,294,'2012-12-14','07:43',0),(155,216,'2012-12-14','08:15,14:04',349),(156,216,'2012-12-17','07:33,17:27',594),(157,294,'2012-12-17','07:52,14:03,14:49,17:00',502),(158,294,'2012-12-18','07:50,14:08,17:39',378),(159,216,'2012-12-18','08:37,13:59,14:44,16:23',421),(160,294,'2012-12-19','07:43,17:18',575),(161,216,'2012-12-19','08:49, 14:20, 15:00, 17:30',481),(162,216,'2012-12-20','08:06,14:06',360),(163,216,'2012-12-21','09:17,13:52',275),(164,216,'2013-01-07','09:27, 14:00, 15:00, 17:10',403),(165,216,'2013-01-08','08:20,14:05,14:34,17:17',508),(166,216,'2013-01-09','08:50, 14:08,14:58,17:26',466),(167,216,'2013-01-10','08:53,14:01,14:53,17:23',458),(168,216,'2013-01-11','07:25,14:00',395),(169,216,'2013-01-14','07:46,14:00,14:36,17:27',545),(170,216,'2013-01-15','08:32,14:05, 14:33,17:05',485),(171,216,'2013-01-16','08:23,14:15, 15:35,17:24',461),(172,216,'2013-01-17','07:35,15:40, 15:55, 17:04',554),(173,216,'2013-01-18','08:47,14:00',313),(174,216,'2013-01-21','08:35,17:27',532),(175,216,'2013-01-22','08:40,14:07,14:55, 17:00',452),(176,216,'2013-01-23','09:00,14:02,14:45,17:23',460),(177,216,'2013-01-24','07:31,13:45, 14:00, 15:30',464),(178,216,'2013-01-28','06:47, 14:00',433),(179,216,'2013-01-29','08:50,14:02',312),(180,216,'2013-01-30','07:18,16:31',553),(181,216,'2013-01-31','08:44, 17:20',516),(182,216,'2013-02-01','07:13, 14:00',407),(183,216,'2013-02-04','08:30,14:05,14:57,17:20',478),(184,216,'2013-02-05','07:30,14:05,14:24',395),(185,216,'2013-02-06','07:10,14:05,14:26,17:08',577),(186,216,'2013-02-07','07:08, 15:30',502),(187,216,'2013-02-08','09:16,14:05',289),(188,216,'2013-02-11','08:24,14:02,14:39,17:19',498),(189,216,'2013-02-12','08:22,14:04,14:43,15:30',389),(190,216,'2013-02-13','07:31,15:23',472),(191,216,'2013-02-14','07:27,17:30',603),(192,216,'2013-02-15','07:28,14:00',392),(193,216,'2013-02-18','09:05,14:14,14:33,16:46',442),(194,216,'2013-02-19','07:29, 14:10, 14:45,17:00',536),(195,216,'2013-02-20','07:49,12:53,13:48, 15:00',376),(196,216,'2013-02-21','07:32,17:24',592),(197,216,'2013-02-22','07:45,14:05',380),(198,216,'2013-02-25','08:48,13:59,14:24,17:24',491),(199,216,'2013-02-26','06:55,14:10,14:40, 17:05',580),(200,216,'2013-02-27','07:59, 15:15',436),(201,216,'2013-02-28','07:41,17:34',593),(202,216,'2013-03-01','07:05,14:01',416),(203,216,'2013-03-04','07:33,14:03,17:28',390),(204,216,'2013-03-05','07:33,14:01,14:28, 17:00',540),(205,216,'2013-03-06','07:28,14:04,14:36,17:27',567),(206,216,'2013-03-07','07:01,21:57',896),(207,216,'2013-03-08','08:24,13:59',335),(208,216,'2013-03-11','07:30,14:15,15:01,17:24',548),(209,216,'2013-03-12','07:28,14:05,14:32,17:00',545),(210,216,'2013-03-13','07:58,16:56',538),(211,216,'2013-03-14','07:29, 17:00',571),(212,216,'2013-03-15','09:10,13:59',289),(213,216,'2013-03-18','07:29,14:46,17:27',437),(214,216,'2013-03-19','07:27, 14:00, 14:30, 17:00',543),(215,216,'2013-03-20','07:27',0),(216,216,'2013-03-21','07:26,17:29',603),(217,216,'2013-03-22','07:24',0),(218,216,'2013-03-26','09:19, 14:00, 14:30, 17:45',476),(219,216,'2013-03-27','06:58',0),(220,216,'2013-04-02','09:32',0),(221,216,'2013-04-08','09:36,14:00, 14:30, 16:40',394),(222,216,'2013-04-10','09:08,14:00, 14:30,17:11',453),(223,216,'2013-04-09','9:30, 17:00',450),(224,216,'2013-04-11','08:01',0),(225,216,'2013-05-06','07:15,14:15',420),(226,216,'2013-05-07','08:53, 14:00, 14:30, 17:00',457),(227,216,'2013-05-08','09:06,14:10, 14:40,17:00',444),(228,216,'2013-05-09','07:50,14:00, 14:10,17:16',556),(229,216,'2013-05-10','08:11,14:00',349),(230,216,'2013-05-13','07:29,14:00,14:40, 17:30,17:08',561),(231,216,'2013-05-14','08:56, 14:00, 14:30, 17:10',464),(232,216,'2013-05-15','07:16,14:10, 14:40, 18:55',669),(233,216,'2013-05-16','07:27, 15:30',483),(234,216,'2013-05-17','07:42,14:10',388),(235,216,'2013-05-21','07:29, 16:30',541),(236,216,'2013-05-22','08:30,14:10, 14:45, 17:34',509),(237,216,'2013-05-23','07:32',0),(238,216,'2013-05-27','07:30,15:05',455),(239,216,'2013-05-28','07:48, 16:30',522),(240,216,'2013-05-29','09:02, 14:00, 14:30, 17:10',458),(241,216,'2013-05-30','09:06, 17:40',514),(242,216,'2013-05-31','09:07,13:59',292),(243,216,'2013-06-03','06:54, 14:00',426),(244,216,'2013-06-04','08:51,14:00, 14:30, 17:00',459),(245,216,'2013-06-05','07:29,14:00, 14:30,17:17',558),(246,216,'2013-06-06','08:25, 17:00',515),(247,216,'2013-06-07','07:12,14:00',408),(248,216,'2013-06-10','08:47, 14:20, 14:40, 17:40',513),(249,216,'2013-06-11','08:47, 14:00, 14:30, 17:30',493),(250,216,'2013-06-12','09:00,14:12,14:33,17:17',476),(251,216,'2013-06-13','08:18, 17:00',522),(252,216,'2013-06-14','09:12,14:04',292),(253,216,'2013-06-17','07:31, 14:10, 14:40, 16:00',479),(254,216,'2013-06-18','09:21, 14:00, 14:30, 17:00',429),(255,216,'2013-06-19','07:45,15:20',455),(256,216,'2013-06-20','08:34, 15:30',416),(257,216,'2013-06-21','09:02, 14:00',298),(258,216,'2013-06-26','07:29,14:03',394),(259,216,'2013-06-25','8:45, 14:10, 14:40, 17:10',475),(260,216,'2013-06-27','08:49,17:24',515),(261,216,'2013-06-28','8:30, 14:00',330),(262,216,'2013-07-01','08:47, 14:00, 14:45, 16:00',388),(263,216,'2013-07-02','08:10, 14:30, 15:00, 16:30',470),(264,216,'2013-07-03','08:10, 14:30, 15:00, 16:45',485),(265,216,'2013-07-04','07:35,14:19,14:41',404),(266,216,'2013-07-05','09:00,14:00',300),(267,216,'2013-07-08','07:50,14:02',372),(268,216,'2013-07-11','07:22',0),(269,216,'2013-07-09','07:30,14:30',420),(270,216,'2013-07-10','07:30,14:30',420);
/*!40000 ALTER TABLE `time_marks` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER insert_time_marks BEFORE INSERT ON time_marks
  FOR EACH ROW
  BEGIN
    DECLARE current_id integer;
    DECLARE tag_id integer;
    DECLARE next integer;
    DECLARE date_field varchar(255);
    DECLARE next_sep integer;
    DECLARE current_mark varchar(255);
    DECLARE current_mark_time_str varchar(255);
    DECLARE current_mark_time integer;
    DECLARE previous_mark_time integer;
    DECLARE right_mark varchar(255);
    DECLARE tmp_seconds integer;
    DECLARE seconds integer;
    DECLARE minutes integer;
    
 
    
    SET date_field = NEW.marks;
    SET seconds = 0;
    SET previous_mark_time = 0;
 
    
    IF (CHAR_LENGTH(date_field) <> 0) THEN
        
       set next = 1;
       WHILE next = 1 DO
         
         SELECT INSTR(date_field, ',') INTO next_sep;
         IF (next_sep > 0) THEN
            SELECT SUBSTR(date_field, 1, next_sep - 1) INTO current_mark;
            SELECT SUBSTR(date_field, next_sep + 1, CHAR_LENGTH(date_field)) INTO right_mark;
            set date_field = right_mark;
         ELSE
           set next = 0;
           set current_mark = date_field;
         END IF;
 
         
         SELECT TRIM(current_mark) INTO current_mark;

         SELECT STR_TO_DATE(current_mark, '%H:%i') INTO current_mark_time_str;

         SELECT UNIX_TIMESTAMP(CONCAT('01/01/01 ', current_mark_time_str)) INTO current_mark_time;

         
         
         IF (previous_mark_time > 0) then
         	SELECT seconds + (current_mark_time - previous_mark_time) INTO seconds;
         	SET previous_mark_time = 0;        
         ELSE
         	SET previous_mark_time = current_mark_time;
         END IF;

 
       END WHILE;

  
       
       SELECT seconds / 60 INTO minutes;
       SET NEW.minutes = minutes;

    END IF;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER update_time_marks BEFORE UPDATE ON time_marks
  FOR EACH ROW
  BEGIN
    DECLARE current_id integer;
    DECLARE tag_id integer;
    DECLARE next integer;
    DECLARE date_field varchar(255);
    DECLARE next_sep integer;
    DECLARE current_mark varchar(255);
    DECLARE current_mark_time_str varchar(255);
    DECLARE current_mark_time integer;
    DECLARE previous_mark_time integer;
    DECLARE right_mark varchar(255);
    DECLARE tmp_seconds integer;
    DECLARE seconds integer;
    DECLARE minutes integer;
    
 
    
    SET date_field = NEW.marks;
    SET seconds = 0;
    SET previous_mark_time = 0;
 
    
    IF (CHAR_LENGTH(date_field) <> 0) THEN
        
       set next = 1;
       WHILE next = 1 DO
         
         SELECT INSTR(date_field, ',') INTO next_sep;
         IF (next_sep > 0) THEN
            SELECT SUBSTR(date_field, 1, next_sep - 1) INTO current_mark;
            SELECT SUBSTR(date_field, next_sep + 1, CHAR_LENGTH(date_field)) INTO right_mark;
            set date_field = right_mark;
         ELSE
           set next = 0;
           set current_mark = date_field;
         END IF;
 
         
         SELECT TRIM(current_mark) INTO current_mark;

         SELECT STR_TO_DATE(current_mark, '%H:%i') INTO current_mark_time_str;

         SELECT UNIX_TIMESTAMP(CONCAT('01/01/01 ', current_mark_time_str)) INTO current_mark_time;

         
         
         IF (previous_mark_time > 0) then
         	SELECT seconds + (current_mark_time - previous_mark_time) INTO seconds;
         	SET previous_mark_time = 0;        
         ELSE
         	SET previous_mark_time = current_mark_time;
         END IF;

 
       END WHILE;

  
       
       SELECT seconds / 60 INTO minutes;
       SET NEW.minutes = minutes;

    END IF;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `user_keys`
--

DROP TABLE IF EXISTS `user_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_keys` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `user_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_ukey` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_keys`
--

LOCK TABLES `user_keys` WRITE;
/*!40000 ALTER TABLE `user_keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id_user` mediumint(9) NOT NULL AUTO_INCREMENT,
  `username` char(60) NOT NULL DEFAULT '',
  `password` char(60) NOT NULL DEFAULT '',
  `external` tinyint(4) NOT NULL DEFAULT '0',
  `name` char(60) NOT NULL DEFAULT '',
  `surname` char(60) DEFAULT NULL,
  `id_group` mediumint(9) NOT NULL DEFAULT '0',
  `email` char(100) NOT NULL DEFAULT '',
  `level` tinyint(4) NOT NULL DEFAULT '100',
  `send_notifications` tinyint(4) DEFAULT NULL,
  `hiredate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lang` char(10) DEFAULT 'en',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3',0,'Administrador','',1,'sebastian.gomez@upcnet.es',0,1,'2018-06-04 12:25:57','en',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-25  9:09:55
