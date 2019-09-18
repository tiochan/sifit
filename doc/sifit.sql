-- MySQL dump 10.13  Distrib 5.7.25, for Linux (x86_64)
--
-- Host: localhost    Database: sifit_tmp
-- ------------------------------------------------------
-- Server version	5.7.25-0ubuntu0.18.04.2

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboards`
--

LOCK TABLES `dashboards` WRITE;
/*!40000 ALTER TABLE `dashboards` DISABLE KEYS */;
INSERT INTO `dashboards` VALUES (1,'CRE_DASHBOARD_TACTICAL',NULL,'<p>\r\n	<style type=\"text/css\">\r\nbody {\r\nbackground-color: #444;\r\ncolor: #ddd;\r\n}\r\ntable {\r\n    border-collapse: collapse;\r\n}\r\ntd {\r\n    border: 1px solid #666;\r\n}	</style>\r\n</p>\r\n<h1>\r\n	CRE dashboard - Tactical overview</h1>\r\n<p>\r\n	<em>Displaying data as user <strong>{{USER_NAME}}</strong>, level<strong> {{USER_LEVEL_NAME}}</strong>. Time refresh: 60s</em></p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" height=\"186\">\r\n	<thead>\r\n		<tr>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n		</tr>\r\n	</thead>\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"1\" rowspan=\"1\">\r\n				<p>\r\n					<span style=\"color:#dddddd;\"><strong style=\"font-size: 20px;\">CRE-HC</strong></span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>New (unmanaged)&nbsp;issues:</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_GET_TASKS|JIRA_PROJECTS={{JIRA_CREHC_PROJECTS}};ISSUE_STATUS=Backlog}};WARN_LIMIT=1;CRITICAL_LIMIT=3;refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Feature requests:</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_GET_TASKS|JIRA_PROJECTS={{JIRA_CREHC_PROJECTS}};ISSUE_STATUS=Feature request}};WARN_LIMIT=1;CRITICAL_LIMIT=3;refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p>\r\n					&nbsp;</p>\r\n			</td>\r\n			<td>\r\n				&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<span style=\"color:#dddddd;\"><strong style=\"font-size: 20px;\">Kanban</strong></span></td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><b>TO-DO</b></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_TODO}};refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>In progress</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_IN_PROGRESS}};refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Blocked</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_BLOCKED}};WARN_LIMIT=1;CRITICAL_LIMIT=4;refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Pending approval</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_PENDING_APPROVAL}};refresh_time=600}}</span></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<p>\r\n					<span style=\"color:#ddd;\"><strong style=\"font-size: 20px;\">Github</strong></span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Pending PRs</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{GITHUB_PENDING_PRs|OUTPUT_MODE=count}};refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Incomming discussions</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{GITHUB_INCOMING_DISCUSSIONS|OUTPUT_MODE=count}};refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				&nbsp;</td>\r\n			<td>\r\n				&nbsp;</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n',''),(3,'CRE_DASHBOARD_SERVICES',NULL,'<style type=\"text/css\">\r\nbody {\r\n/*background-color: #330066;*/\r\nbackground-color: #444;\r\ncolor: #ddd;\r\n}</style>\r\n<h1>\r\n	CRE Dashboard #2:<em> Overall status</em></h1>\r\n<p>\r\n	<em>(View data as user <strong>{{USER_NAME}}</strong>, level<strong> {{USER_LEVEL_NAME}}</strong>.)</em></p>\r\n<p>\r\n	&nbsp;</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" height=\"186\" style=\"border: 1px solid rgb(221, 221, 221);\" width=\"562\">\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"2\" style=\"background-color: rgb(102, 102, 102);\">\r\n				<h1 style=\"text-align: center;\">\r\n					<span style=\"color:#fff0f5;\">SCHIP</span></h1>\r\n			</td>\r\n			<td colspan=\"2\" style=\"background-color: rgb(102, 102, 102);\">\r\n				<h1 style=\"text-align: center;\">\r\n					<span style=\"color:#fff0f5;\">ZEUS</span></h1>\r\n			</td>\r\n			<td colspan=\"2\" style=\"background-color: rgb(102, 102, 102);\">\r\n				<h1 style=\"text-align: center;\">\r\n					<span style=\"color:#fff0f5;\">TARDIS</span></h1>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Blocked pods:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{{Z_INDICATOR_TEST_2|ID=1;refresh_time=10}}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Hosts down:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{{Z_INDICATOR_TEST_2|ID=2;refresh_time=10}}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>EMR Status:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{{Z_INDICATOR_TEST_3|ID=3}}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Memory usage:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{{Z_INDICATOR_TEST_2|ID=4;refresh_time=10}}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Queue length:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{{Z_INDICATOR_TEST_2|ID=10;refresh_time=10}}</span></p>\r\n			</td>\r\n			<td style=\"vertical-align: top;\">\r\n				<p>\r\n					<span style=\"color:#fff0f5;\"><strong>Depend. status:</strong></span></p>\r\n				<p>\r\n					<span style=\"color:#fff0f5;\">{{Z_INDICATOR_TEST_3|ID=11;KPI_VALUE=Down}} </span></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{{Z_TEST_GENERIC_GRAPH_2|ID=5;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}}4</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{{Z_TEST_GENERIC_GRAPH|ID=6;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}}</td>\r\n			<td colspan=\"2\" rowspan=\"1\">\r\n				{{Z_TEST_GENERIC_GRAPH|ID=12;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}}</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KP 5</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KPI 6</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KPI 7</span></td>\r\n			<td>\r\n				<span style=\"color:#fff0f5;\">KP 8</span></td>\r\n			<td>\r\n				&nbsp;</td>\r\n			<td>\r\n				&nbsp;</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	&nbsp;</p>\r\n',''),(4,'CRE_GITHUB_STATUS',NULL,'<style type=\"text/css\">\r\nbody {\r\nbackground-color: #444;\r\ncolor: #ddd;\r\n}</style>\r\n<h1>\r\n	<span style=\"color:#dddddd;\">CRE dashboard - Github status</span></h1>\r\n<p>\r\n	<span style=\"color:#dddddd;\"><em>Displaying data as user <strong>{{USER_NAME}}</strong>, level<strong> {{USER_LEVEL_NAME}}</strong>.</em></span></p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" height=\"186\" style=\"border-style: solid; width: 90%\" width=\"562\">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				<span style=\"color:#dddddd;\"><span style=\"font-size:16px;\"><strong>Incomming discussions</strong></span></span></td>\r\n			<td>\r\n				<span style=\"color:#dddddd;\"><span style=\"font-size:16px;\"><strong>Pending PRs</strong></span></span></td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"vertical-align: top; background-color: rgb(170, 170, 170);\">\r\n				<span style=\"color:#dddddd;\">{{GITHUB_INCOMING_DISCUSSIONS}}</span></td>\r\n			<td style=\"vertical-align: top; background-color: rgb(170, 170, 170);\">\r\n				<span style=\"color:#dddddd;\">{{GITHUB_PENDING_PRs}}</span></td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	<br />\r\n	&nbsp;</p>\r\n',''),(5,'CRE_JIRA_STATUS',NULL,'<p>\r\n	<style type=\"text/css\">\r\nbody {\r\nbackground-color: #444;\r\ncolor: #ddd;\r\n}	</style>\r\n</p>\r\n<h1>\r\n	CRE dashboard - Jira status</h1>\r\n<p>\r\n	<em>Displaying data as user <strong>{{USER_NAME}}</strong>, level<strong> {{USER_LEVEL_NAME}}</strong>. Time refresh: 60s</em></p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" height=\"186\" style=\"border-style: solid; border-color: rgb(221, 221, 221);\" width=\"562\">\r\n	<thead>\r\n		<tr>\r\n			<th colspan=\"1\" scope=\"col\" style=\"width: 200px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n			<th scope=\"col\" style=\"width: 150px;\">\r\n				&nbsp;</th>\r\n		</tr>\r\n	</thead>\r\n	<tbody>\r\n		<tr>\r\n			<td colspan=\"1\" rowspan=\"1\">\r\n				<p>\r\n					<span style=\"color:#dddddd;\"><strong style=\"font-size: 20px;\">CRE-HC</strong></span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>New (unmanaged)&nbsp;issues:</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_GET_TASKS|JIRA_PROJECTS={{JIRA_CREHC_PROJECTS}};ISSUE_STATUS=Backlog}};WARN_LIMIT=1;CRITICAL_LIMIT=3;refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Feature requests:</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_GET_TASKS|JIRA_PROJECTS={{JIRA_CREHC_PROJECTS}};ISSUE_STATUS=Feature request}};WARN_LIMIT=1;CRITICAL_LIMIT=3;refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p>\r\n					&nbsp;</p>\r\n			</td>\r\n			<td>\r\n				&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<span style=\"color:#dddddd;\"><strong style=\"font-size: 20px;\">Kanban</strong></span></td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><b>TO-DO</b></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_TODO}};refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>In progress</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_IN_PROGRESS}};refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Blocked</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_BLOCKED}};WARN_LIMIT=1;CRITICAL_LIMIT=4;refresh_time=600}}</span></p>\r\n			</td>\r\n			<td>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\"><strong>Pending approval</strong></span></p>\r\n				<p style=\"text-align: center;\">\r\n					<span style=\"color:#dddddd;\">{{KPI_Integer|KPI_VALUE={{JIRA_TASKS_PENDING_APPROVAL}};refresh_time=600}}</span></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				<p>\r\n					<span style=\"color:#ddd;\"><strong style=\"font-size: 20px;\">Github</strong></span></p>\r\n			</td>\r\n			<td>\r\n				<p>\r\n					&nbsp;</p>\r\n			</td>\r\n			<td>\r\n				<p>\r\n					&nbsp;</p>\r\n			</td>\r\n			<td>\r\n				&nbsp;</td>\r\n			<td>\r\n				&nbsp;</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n',''),(6,'Global CRE Dashboard',NULL,'<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<link href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\" rel=\"stylesheet\" />\r\n	{{html_script|value=https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js}} {{html_script|value=https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js}}</p>\r\n<h1>\r\n	<span style=\"color:#dddddd;\">CRE dashboard - Tactical overview</span></h1>\r\n<p>\r\n	<span style=\"color:#dddddd;\"><em>Displaying data as user <strong>{{USER_NAME}}</strong>, level<strong> {{USER_LEVEL_NAME}}</strong>.</em></span></p>\r\n<div class=\"carousel slide\" data-ride=\"carousel\" id=\"myCarousel\">\r\n	<!-- Indicators -->\r\n	<ol class=\"carousel-indicators\">\r\n		<li class=\"active\" data-slide-to=\"0\" data-target=\"#myCarousel\">\r\n			&nbsp;</li>\r\n		<li data-slide-to=\"1\" data-target=\"#myCarousel\">\r\n			&nbsp;</li>\r\n		<li data-slide-to=\"2\" data-target=\"#myCarousel\">\r\n			&nbsp;</li>\r\n	</ol>\r\n	<!-- Wrapper for slides -->\r\n	<div class=\"carousel-inner\">\r\n		<div class=\"item active\">\r\n			{{CRE_DASHBOARD_TACTICAL}}</div>\r\n		<div class=\"item\">\r\n			{{CRE_DASHBOARD_GITHUB_STATUS}}</div>\r\n		<div class=\"item\">\r\n			{{CRE_DASHBOARD_SERVICES_STATUS}}</div>\r\n	</div>\r\n	<!-- Left and right controls --><!--\r\n  <a class=\"left carousel-control\" href=\"#myCarousel\" data-slide=\"prev\">\r\n    <span class=\"glyphicon glyphicon-chevron-left\"></span>\r\n    <span class=\"sr-only\">Previous</span>\r\n  </a>\r\n  <a class=\"right carousel-control\" href=\"#myCarousel\" data-slide=\"next\">\r\n    <span class=\"glyphicon glyphicon-chevron-right\"></span>\r\n    <span class=\"sr-only\">Next</span>\r\n  </a>\r\n--></div>\r\n<p>\r\n	&nbsp;</p>\r\n','');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'SIFIT Adm','SIFIT Administrators group'),(2,'CRE','');
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
  `description` text,
  `value` text,
  `extrainfo` text,
  `connection` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `id_user` mediumint(9) DEFAULT NULL,
  `id_group` mediumint(9) DEFAULT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB AUTO_INCREMENT=285 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_tags`
--

LOCK TABLES `report_tags` WRITE;
/*!40000 ALTER TABLE `report_tags` DISABLE KEYS */;
INSERT INTO `report_tags` VALUES (3,'OP_DATE_TODAY','operation','Current date in format dd-mm-yyyy','date(\\\"d-m-Y\\\")',NULL,NULL,1,NULL,NULL,1),(4,'OP_DATE_LAST_WEEK','operation','Date one week back in format dd-mm-yyyy','date(\\\"d-m-Y\\\",strtotime(\\\"-1 week\\\"))',NULL,NULL,1,NULL,NULL,1),(5,'USER_GROUP','system_var','','USER_GROUP',NULL,NULL,1,NULL,NULL,1),(6,'USER_ID','system_var','','USER_ID',NULL,NULL,1,NULL,NULL,1),(10,'USER_LEVEL','system_var','','USER_LEVEL',NULL,NULL,1,NULL,NULL,1),(11,'USER_GROUP_NAME','system_var','','USER_GROUP_NAME',NULL,NULL,1,NULL,NULL,1),(12,'USER_NAME','system_var','','USER_NAME',NULL,NULL,1,NULL,NULL,1),(13,'USER_LEVEL_NAME','system_var','','USER_LEVEL_NAME',NULL,NULL,1,NULL,NULL,1),(30,'OP_DATE_TODAY_MONTH','operation','Current date in format mm','date(\\\"m\\\")',NULL,NULL,1,NULL,NULL,1),(31,'OP_DATE_TODAY_YEAR','operation','Current date in format yyyy','date(\\\"Y\\\")',NULL,NULL,1,NULL,NULL,1),(131,'OP_DAY_OF_WEEK','operation','Returns the day of week (0 - 6)','date(\"w\")','',NULL,1,NULL,NULL,1),(132,'OP_LAST_SATURDAY','operation','','\"current_date - INTERVAL \" . ( date(\"w\") +1 ) . \" day\"','',NULL,1,NULL,NULL,1),(133,'OP_LAST_SUNDAY','operation','','\"current_date - INTERVAL \" . date(\"w\")  . \" day\"','',NULL,1,NULL,NULL,1),(134,'OP_LAST_MONDAY','operation','','\"current_date - INTERVAL \" . ( date(\"w\") +7 ) . \" day\"','',NULL,1,NULL,NULL,1),(135,'CONS_DATE_FORMAT','constant','The format (for PHP functions) of the given dates.','%d/%m/%Y','','',1,NULL,NULL,1),(137,'CONS_DATE_FORMAT_PHP','constant','The format (for PHP functions) of the given dates.','d/m/Y','','',1,NULL,NULL,1),(172,'CONS_DATE_FORMAT_SQL','constant','The format (for SQL queries) of the given dates.','Y/m/d','','',1,NULL,NULL,1),(173,'CONS_DATE_TIME_FORMAT','constant','The format (for PHP functions) of the given dates.','%d/%m/%Y %H:%M:%S','','',1,NULL,NULL,1),(174,'CONS_DATE_TIME_FORMAT_PHP','constant','The format (for PHP functions) of the given dates.','d/m/Y H:i:s','','',1,NULL,NULL,1),(175,'CONS_DATE_TIME_FORMAT_SQL','constant','The format (for SQL queries) of the given dates.','Y/m/d H:i:s','','',1,NULL,NULL,1),(204,'GENERIC_QUERY','query','Generic query abstractor TAG.\r\nSet QUERY parameter and check other parameters too to customize output.','{{$QUERY}}','CSV=false;\r\nSHOW_NO_DATA=false;\r\nSHOW_FIELD_NAMES=false;','APP_GENERIC_CONN',1,1,1,1),(239,'GENERIC_GRAPH','generic_graph','Generic GRAPH abstraction.\r\nSet VALUES parameter and also check the other TAG Type parameters to customize your output.','{{$VALUES}}','','',1,1,1,1),(246,'SYSHOME','php_code','','echo SYSHOME;','','',1,1,1,1),(247,'HOME','php_code','Returns the HOME constant definition','echo HOME;','','',1,1,1,1),(248,'SERVER_URL','php_code','Current service URL. Defined on app.conf.php file on the load.','echo SERVER_URL;','','',1,1,1,1);
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
INSERT INTO `reports` VALUES (9,'Example - Mark reports',NULL,'<p>\r\n	Hi {USER_NAME},</p>\r\n<p>\r\n	This is your mark report for the current month:</p>\r\n<p>\r\n	{Z_EXAMPLE_MARK_MONTHLY_REPORT}</p>\r\n','','working_daily'),(12,'Example - HTTP Extracts',NULL,'<h2>\r\n	Search for jobs in the nearbys</h2>\r\n<h2>\r\n	&nbsp;</h2>\r\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"data_box_rows\" height=\"94\" style=\"border: 1px solid rgb(170, 187, 170); padding: 15px;\" width=\"720\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(170, 187, 170);\">\r\n				<h2>\r\n					<strong>Loc.</strong></h2>\r\n			</td>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(170, 187, 170);\">\r\n				<h2>\r\n					<strong>Results</strong></h2>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan=\"2\" style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(238, 238, 238);\">\r\n				<p>\r\n					<strong>Sant Pere de Riudebitlles</strong></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				&nbsp;</td>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				<p>\r\n					{X_INFOJOBS_SANT_PERE_RIUDEBITLLES|INCLUDE_SCRIPTS=0;INCLUDE_STYLES=0}</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td colspan=\"2\" style=\"vertical-align: top; border-color: rgb(153, 153, 153); background-color: rgb(238, 238, 238);\">\r\n				<p>\r\n					<strong>Sant Sadurn&iacute;</strong></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				&nbsp;</td>\r\n			<td style=\"vertical-align: top; border-color: rgb(153, 153, 153);\">\r\n				<p>\r\n					{X_INFOJOBS_SANTSADURNI|INCLUDE_SCRIPTS=0;INCLUDE_STYLES=0}</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	&nbsp;</p>\r\n<hr />\r\n<h2>\r\n	&nbsp;</h2>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n','','daily'),(13,'MAIN_PAGE',NULL,'<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Welcome {{USER_NAME}}.</span></span></p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">This is the main page.</span></span></p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Please, <a href=\"./tools/reports.php\">go to the reports section</a> in order to create and manage your reports and dashboards. </span></span></p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">There you will find:</span></span></p>\r\n<ul>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">TAGs, which are the pieces that you use to build reports and even other tags. You can create TAGs of many different types, for example:</span></span></p>\r\n		<ul>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Graphs,</strong> of many differents types (bar, line, pie, ...)</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Constants,</strong> which value does not change</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>HTML,</strong> which allows you to add rich-text</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>HTTP,</strong> gets the content from a URL</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>HTTP Extract,</strong> parse the content from a URL</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Query,</strong> which will execute a SQL sentence</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Php code,</strong> this means that you can add your own PHP code here and will be executed on the fly</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>System commands, </strong>as you can imagine, execute anything executable on your system</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\"><strong>Search, </strong>to extract parts of a given input</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">And more...</span></span></p>\r\n			</li>\r\n			<li>\r\n				<p>\r\n					<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">And also<strong> build your own</strong> TAGs types, just inherit the main class and code it.</span></span></p>\r\n			</li>\r\n		</ul>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">The TAGs are evaluated in cascade and recursivelly, meaning that if you have a Report with N tags, they will be evaluated as they are defined on the Report, but for each TAG, it is evaluated in depth before going for the next TAG. This is because... </span></span></p>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">You can use the output of one TAG as input for another, for example, use a TAG of type query as value for a Graph, and it will represent the resulting data.</span></span></p>\r\n	</li>\r\n	<li>\r\n		<p>\r\n			<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Also you can use TAGs into the value definition of other TAGs, for example, into a Query TAG you can insert other TAGs as {USER_NAME}. As mentioned before, the TAGs are evaluated in depth, so, the Query will not be evaluated until are evaluated all the TAGs it contains, and this process is recursive.</span></span></p>\r\n	</li>\r\n</ul>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<span style=\"font-size:14px;\"><span style=\"font-family:tahoma,geneva,sans-serif;\">Have a nice day!</span></span></p>\r\n','','never'),(14,'CRE_DASHBOARD1',NULL,'<style type=\"text/css\">\r\nbody {\r\nbackground-color: #330066;\r\ncolor: #ddd;\r\n}</style>\r\n<h1>\r\n	CRE Dashboard #1</h1>\r\n<p>\r\n	&nbsp;</p>\r\n<table border=\"0\" cellpadding=\"5\" cellspacing=\"5\" style=\"border: solid 1px #ddd;width: 500px\">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				KPI 1</td>\r\n			<td>\r\n				KPI 2</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				KPI 3</td>\r\n			<td>\r\n				KPI 4</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n				KP 5</td>\r\n			<td>\r\n				KPI 6</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n<p>\r\n	Here, some kind of graphs</p>\r\n<p>\r\n	{Z_TEST_GENERIC_GRAPH}</p>\r\n<p>\r\n	&nbsp;</p>\r\n','','never');
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
  `parameters` text,
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
INSERT INTO `tasks` VALUES (1,'Report launcher','/include/cron/report_launcher.php','','','daily','07:00',1);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3',0,'Administrador','',1,'sebastian.gomez@upcnet.es',0,1,'2018-06-04 12:25:57','en',0),(2,'sebastian.gomez','7cd17ddaddad22a51592896fb640c20c',0,'Sebastian','',2,'sebastian.gomez@schibsted.com',3,1,'2018-12-14 12:56:01','en',0);
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

-- Dump completed on 2019-04-25 11:20:41
