DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `courses`;

CREATE TABLE `users` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
);

CREATE TABLE `courses` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Description` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
);

CREATE TABLE `enrollments` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `UserID` int DEFAULT NULL,
  `CourseID` int DEFAULT NULL,
  `CompletionStatus` enum('not started','in progress','completed') DEFAULT 'not started',
  PRIMARY KEY (`ID`),
  KEY `UserID` (`UserID`),
  KEY `CourseID` (`CourseID`),
  CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`),
  CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `courses` (`ID`)
);