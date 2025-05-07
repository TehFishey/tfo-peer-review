--tables
USE tpr_db;

--creatures
CREATE TABLE IF NOT EXISTS `Creatures` (
  `code` varchar(5) PRIMARY KEY NOT NULL,
  `imgSrc` varchar(60) NOT NULL,
  `gotten` varchar(10) NOT NULL,
  `name` varchar(30),
  `growthLevel` varchar(1) NOT NULL,
  `isStunted` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--flags
CREATE TABLE IF NOT EXISTS `FlaggedCodes` (
  `uuid` varchar(36) NOT NULL,
  `code` varchar(5) NOT NULL,
  PRIMARY KEY(uuid, code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--clicks
CREATE TABLE IF NOT EXISTS `Clicks` (
  `uuid` varchar(36) NOT NULL,
  `code` varchar(5) NOT NULL,
  `time` varchar(10) NOT NULL,
  PRIMARY KEY(uuid, code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--sessions
CREATE TABLE IF NOT EXISTS `Sessions` (
  `sessionId` int NOT NULL UNIQUE KEY AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL PRIMARY KEY,
  `time` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--sessioncache
CREATE TABLE IF NOT EXISTS `SessionCache` (
  `sessionId` int NOT NULL,
  `code` varchar(5) NOT NULL,
  `imgSrc` varchar(60) NOT NULL,
  `gotten` varchar(10) NOT NULL,
  `name` varchar(30),
  `growthLevel` varchar(1) NOT NULL,
  `isStunted` varchar(5) NOT NULL,
  PRIMARY KEY(sessionId, code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--logs
CREATE TABLE IF NOT EXISTS `Log_Metrics` (
  `weekId` varchar(7) PRIMARY KEY NOT NULL,
  `uniques` int NOT NULL DEFAULT 0,
  `clicks` int NOT NULL DEFAULT 0,
  `curls` int NOT NULL DEFAULT 0,
  `creatureAdds` int NOT NULL DEFAULT 0,
  `creatureRemoves` int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Log_Labs` (
  `weekId` varchar(7) NOT NULL,
  `labname` varchar(20) NOT NULL,
  PRIMARY KEY(weekId, labname)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Log_Creatures` (
  `weekId` varchar(7) NOT NULL,
  `code` varchar(5) NOT NULL,
  PRIMARY KEY(weekId, code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `Log_Uniques` (
  `weekId` varchar(7) NOT NULL,
  `ip` varchar(45) NOT NULL,
  PRIMARY KEY(weekId, ip)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--rate limiter logs
CREATE TABLE IF NOT EXISTS `RateLimits` (
  `ip` varchar(45) PRIMARY KEY NOT NULL,
  `microtime` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
