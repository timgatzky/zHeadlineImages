-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_content`
-- 

CREATE TABLE `tl_content` (
  `headline_addImage` char(1) NOT NULL default '',
  `headline_singleSRC` varchar(255) NOT NULL default '',
  `headline_alt` varchar(255) NOT NULL default '',
  `headline_size` varchar(64) NOT NULL default '',
  `headline_imagemargin` varchar(128) NOT NULL default '',
  `headline_imageUrl` varchar(255) NOT NULL default '',
  `headline_fullsize` char(1) NOT NULL default '',
  `headline_caption` varchar(255) NOT NULL default '',
  `headline_floating` varchar(32) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;