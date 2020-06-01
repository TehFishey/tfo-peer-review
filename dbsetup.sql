--db
CREATE DATABASE tfopeerreview_db;

--user
GRANT ALL ON tfopeerreview_db.* 
	TO 'tfopeerreview_user'@'localhost' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;

--tables
use tfopeerreview_db;

--creatures
CREATE TABLE `Creatures` (
  `code` varchar(5) PRIMARY KEY NOT NULL,
  `imgSrc` varchar(60) NOT NULL,
  `gotten` varchar(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `growthLevel` varchar(1) NOT NULL,
  `isStunted` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--flags
CREATE TABLE `FlaggedCodes` (
  `uuid` varchar(36) NOT NULL,
  `code` varchar(5) NOT NULL,
  PRIMARY KEY(uuid, code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--clicks
CREATE TABLE `Clicks` (
  `uuid` varchar(36) NOT NULL,
  `code` varchar(5) NOT NULL,
  `time` varchar(10) NOT NULL,
  PRIMARY KEY(uuid, code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--sessions
CREATE TABLE `Sessions` (
  `sessionId` int NOT NULL UNIQUE KEY AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL PRIMARY KEY,
  `time` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--sessioncache
CREATE TABLE `SessionCache` (
  `sessionId` int NOT NULL,
  `code` varchar(5) NOT NULL,
  `imgSrc` varchar(60) NOT NULL,
  `gotten` varchar(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `growthLevel` varchar(1) NOT NULL,
  `isStunted` varchar(5) NOT NULL,
  PRIMARY KEY(sessionId, code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--logs
CREATE TABLE `Log_Weekly` (
  `ip` varchar(45) NOT NULL,
  `action` varchar(36) NOT NULL,
  `count` int NOT NULL,
  PRIMARY KEY(ip, action)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--long-term logs
CREATE TABLE `Log_Compiled` (
  `weekId` varchar(7) PRIMARY KEY NOT NULL,
  `pageViews` int NOT NULL,
  `uniques` int NOT NULL,
  `clicks` int NOT NULL,
  `curls` int NOT NULL,
  `creatureAdds` int NOT NULL,
  `creatureRemoves` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--rate limiter logs
CREATE TABLE `RateLimits` (
  `ip` varchar(45) PRIMARY KEY NOT NULL,
  `microtime` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
