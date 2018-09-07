-- MySQL dump 10.16  Distrib 10.2.13-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: d_one
-- ------------------------------------------------------
-- Server version	10.2.13-MariaDB-10.2.13+maria~stretch
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Question`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Question` (
  `QuestionID` int(10) unsigned NOT NULL,
  `Prompt` text NOT NULL,
  `Solution` text NOT NULL,
  `Options` text NOT NULL,
  `TestID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`QuestionID`),
  KEY `fk_questionid_testid` (`TestID`),
  CONSTRAINT `fk_questionid_testid` FOREIGN KEY (`TestID`) REFERENCES `Test` (`TestID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prompt_has_underscore` CHECK (`Prompt` regexp '^[^_]*_[^_]*$')
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Question`
--

INSERT INTO `Question` VALUES (1,'D1 is about _ maths.','discrete','pure,statistical,applied',1);
INSERT INTO `Question` VALUES (2,'A graph is semi-Eulerian if it has _ odd nodes.','2','0,1,4',1);
INSERT INTO `Question` VALUES (3,'A graph that can be traversed completely and return to the first node is _.','Eulerian','Semi-Eulerian,Non-Eulerian',1);
INSERT INTO `Question` VALUES (4,'Bubblesort has order O(_).','n²','n,n log(n),n!',2);
INSERT INTO `Question` VALUES (5,'Shuttle sort is generally _ than bubblesort.','faster','slower,as fast as',2);
INSERT INTO `Question` VALUES (6,'First fit bin-packing algorithms produce _ solutions.','approximate','perfect,incorrect',2);
INSERT INTO `Question` VALUES (7,'There are _ possible routes in a Travelling Salesman problem with n nodes','(n-1)!','n!,n²,2n,log n',2);
INSERT INTO `Question` VALUES (8,'Djikstra\'s algorithm finds _ ?','the shortest path between nodes','the shortest round trip through a network,the minimum spanning tree of a network',2);
INSERT INTO `Question` VALUES (9,'_ is the fastest algorithm under most circumstances.','Quicksort','Bubblesort,Shuttle sort,Bogosort,Stoogesort',2);
INSERT INTO `Question` VALUES (11,'_ is a difficult problem to find an exact, optimal solution for.','The Travelling Salesman Problem','The Route Inspection Problem',2);

--
-- Table structure for table `Student`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Student` (
  `Username` varchar(20) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `PasswordHash` varchar(128) NOT NULL,
  `TeacherID` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`Username`),
  KEY `fk_teacherid_username` (`TeacherID`),
  CONSTRAINT `fk_teacherid_username` FOREIGN KEY (`TeacherID`) REFERENCES `Teacher` (`Username`) ON UPDATE CASCADE
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Student`
--

INSERT INTO `Student` VALUES ('<b>aaa</b>','This should not be <b>BOLD</b>','$2y$10$Kxr3shZX8HyoPA9NbmkUcu3aOquk1.IrPOPNqB9ttwweY/878yu/W',NULL);
INSERT INTO `Student` VALUES ('<i>username</i>','<script>alert(\"XSS!\")</script>','$2y$10$fT8cLAkIysDIoWruvVkZLO.lEpLX754lsmEFehEl/2XG.BUqOuDCq','teacher0');
INSERT INTO `Student` VALUES ('noresults-t1','No Results-t1','$2y$10$MNKVhze.KYxGJHxqY6E0c.GJrDuBvPzORNIPe043zQMKFK/jRLpFi','teacher1');
INSERT INTO `Student` VALUES ('noteacher','No Teacher','$2y$10$SBTan1RY1dSLZJWLdZDVYOvjUe1oFQacd5kpfTP2P/3LCWarwZym6',NULL);
INSERT INTO `Student` VALUES ('student0-t0','Student Zero-t0','$2y$10$OzKNIkgqrlR4HNQ0RCr19.MQn9EDKzVn1ExEYIXAW8K./Nsn.fBvS','teacher0');
INSERT INTO `Student` VALUES ('student0-t1','Student Zero-t1','$2y$10$wDaoL/SP9DDAyCi75VMN6e3n7LdBHUtqabZoI3HHlB6b70DBwQGWm','teacher1');
INSERT INTO `Student` VALUES ('student1-t0','Student One-t0','$2y$10$oMa6tEM1C97WbKayUmtane4Fu1NWzexFjH02z0sml7NkS3dynymKy','teacher0');
INSERT INTO `Student` VALUES ('student2-t0','Student Two-t0','$2y$10$j/1fOlxCPh0l0rARC8qS..FyF7OgYMuFmHn4fIWq3ykQmqjzxG3DK','teacher0');
INSERT INTO `Student` VALUES ('student3-t0','Student Three-t0','$2y$10$An/6FuLsB5lvWyoFMj2E6.MYktkyLcTAuGFtoQiqn5lYdFsAmy8Bq','teacher0');
INSERT INTO `Student` VALUES ('testingstudentnew','TestingStudentNew','$2y$10$N.MvrKcTrlK2vZE9eNLZmePfBePv/Gtc7.xhuWrJHgmjDW9ByQhxy',NULL);

--
-- Table structure for table `Teacher`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Teacher` (
  `Username` varchar(20) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `PasswordHash` varchar(128) NOT NULL,
  PRIMARY KEY (`Username`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Teacher`
--

INSERT INTO `Teacher` VALUES ('nostudents','No Students','$2y$10$HEwRm9FIpFqZ4cF/Y4XnlOG1oAFn7hk9.mr50vkrAa0QpFojNpnE.');
INSERT INTO `Teacher` VALUES ('teacher0','Teacher Zero','$2y$10$e8LmurhADGuB3UkeykTNoeCUCTrlAERO3sbVYDUMlrM07nCfj9I56');
INSERT INTO `Teacher` VALUES ('teacher1','Teacher One','$2y$10$voacbcUnvRSjIzPEvwwMQu.MxpsaXDHu5u0NhxaIj55bq5aHKDRhi');

--
-- Table structure for table `Test`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Test` (
  `TestID` int(10) unsigned NOT NULL,
  `Name` varchar(100) NOT NULL,
  PRIMARY KEY (`TestID`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Test`
--

INSERT INTO `Test` VALUES (1,'Basics of D1');
INSERT INTO `Test` VALUES (2,'Algorithms');

--
-- Table structure for table `TestResult`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TestResult` (
  `ResultID` int(10) unsigned NOT NULL,
  `StudentID` varchar(20) NOT NULL,
  `TestID` int(10) unsigned NOT NULL,
  `Score` int(10) unsigned NOT NULL,
  `ResultTime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ResultID`),
  KEY `fk_studentid_username` (`StudentID`),
  KEY `fk_testid_testid` (`TestID`),
  CONSTRAINT `fk_studentid_username` FOREIGN KEY (`StudentID`) REFERENCES `Student` (`Username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_testid_testid` FOREIGN KEY (`TestID`) REFERENCES `Test` (`TestID`) ON UPDATE CASCADE
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TestResult`
--

INSERT INTO `TestResult` VALUES (1,'student0-t1',1,2,'2018-03-14 13:45:06');
INSERT INTO `TestResult` VALUES (2,'student0-t1',1,3,'2018-03-14 19:04:48');
INSERT INTO `TestResult` VALUES (3,'student0-t1',1,2,'2018-03-15 12:09:46');
INSERT INTO `TestResult` VALUES (4,'student0-t1',1,2,'2018-03-15 12:11:24');
INSERT INTO `TestResult` VALUES (5,'student0-t1',1,0,'2018-03-16 09:22:43');
INSERT INTO `TestResult` VALUES (6,'student0-t1',1,3,'2018-03-16 10:34:48');
INSERT INTO `TestResult` VALUES (7,'student0-t1',1,1,'2018-03-16 11:04:17');
INSERT INTO `TestResult` VALUES (8,'student0-t1',1,3,'2018-03-16 11:06:18');
INSERT INTO `TestResult` VALUES (9,'student2-t0',2,7,'2018-03-16 11:10:06');
INSERT INTO `TestResult` VALUES (10,'student0-t1',2,3,'2018-03-16 11:20:38');
INSERT INTO `TestResult` VALUES (11,'student0-t1',2,3,'2018-03-16 11:23:53');
INSERT INTO `TestResult` VALUES (12,'student0-t1',2,3,'2018-03-16 11:28:34');
INSERT INTO `TestResult` VALUES (13,'student0-t1',2,1,'2018-03-16 11:30:59');
INSERT INTO `TestResult` VALUES (14,'student0-t1',2,3,'2018-03-16 11:35:20');
INSERT INTO `TestResult` VALUES (15,'student0-t1',1,3,'2018-03-16 11:37:23');
INSERT INTO `TestResult` VALUES (16,'student0-t1',1,0,'2018-03-16 11:40:23');
INSERT INTO `TestResult` VALUES (17,'student0-t1',1,1,'2018-03-16 11:40:49');
INSERT INTO `TestResult` VALUES (18,'student0-t1',1,3,'2018-03-16 11:44:05');
INSERT INTO `TestResult` VALUES (19,'student2-t0',2,5,'2018-03-16 11:45:03');
INSERT INTO `TestResult` VALUES (20,'student2-t0',1,1,'2018-03-16 11:45:48');
INSERT INTO `TestResult` VALUES (21,'student2-t0',2,5,'2018-03-16 11:50:17');
INSERT INTO `TestResult` VALUES (22,'student0-t1',1,3,'2018-03-18 15:50:07');
INSERT INTO `TestResult` VALUES (23,'student0-t1',1,2,'2018-03-18 15:55:58');
INSERT INTO `TestResult` VALUES (24,'student2-t0',1,1,'2018-03-18 15:58:54');
INSERT INTO `TestResult` VALUES (25,'student2-t0',1,3,'2018-03-18 16:00:24');
INSERT INTO `TestResult` VALUES (26,'student3-t0',1,3,'2018-03-18 16:13:44');
INSERT INTO `TestResult` VALUES (27,'student3-t0',1,0,'2018-03-18 16:14:34');
INSERT INTO `TestResult` VALUES (28,'student3-t0',1,0,'2018-03-18 16:22:16');
INSERT INTO `TestResult` VALUES (29,'noteacher',1,3,'2018-03-18 16:22:39');
INSERT INTO `TestResult` VALUES (30,'noteacher',2,3,'2018-03-18 16:23:41');
INSERT INTO `TestResult` VALUES (31,'student3-t0',2,1,'2018-03-18 16:25:15');
INSERT INTO `TestResult` VALUES (32,'student0-t0',1,3,'2018-03-19 13:13:48');
INSERT INTO `TestResult` VALUES (33,'<i>username</i>',2,3,'2018-03-19 14:32:14');
INSERT INTO `TestResult` VALUES (34,'student3-t0',2,3,'2018-03-24 14:35:13');
INSERT INTO `TestResult` VALUES (35,'student1-t0',2,4,'2018-03-28 18:37:06');
INSERT INTO `TestResult` VALUES (36,'student3-t0',1,3,'2018-03-29 21:06:26');
INSERT INTO `TestResult` VALUES (37,'student0-t0',1,2,'2018-09-07 13:43:34');
INSERT INTO `TestResult` VALUES (38,'student0-t0',1,0,'2018-09-07 15:39:44');
INSERT INTO `TestResult` VALUES (39,'student0-t1',1,3,'2018-09-07 15:44:44');
INSERT INTO `TestResult` VALUES (40,'student0-t0',1,1,'2018-09-07 16:00:08');
INSERT INTO `TestResult` VALUES (41,'student0-t0',1,2,'2018-09-07 16:03:07');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-07 19:32:58
