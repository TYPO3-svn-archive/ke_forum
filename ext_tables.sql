#
# Table structure for table 'tx_keforum_categories_write_access_mm'
# 
#
CREATE TABLE tx_keforum_categories_write_access_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_keforum_categories_read_access_mm'
# 
#
CREATE TABLE tx_keforum_categories_read_access_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_keforum_categories_moderators_mm'
# 
#
CREATE TABLE tx_keforum_categories_moderators_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_keforum_categories'
#
CREATE TABLE tx_keforum_categories (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	public tinyint(3) DEFAULT '0' NOT NULL,
	write_access int(11) DEFAULT '0' NOT NULL,
	read_access int(11) DEFAULT '0' NOT NULL,
	moderated tinyint(3) DEFAULT '0' NOT NULL,
	moderators int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_keforum_threads'
#
CREATE TABLE tx_keforum_threads (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	category int(11) DEFAULT '0' NOT NULL,
	author text,
	views tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_keforum_posts'
#
CREATE TABLE tx_keforum_posts (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	content text,
	author text,
	thread int(11) DEFAULT '0' NOT NULL,
	parent int(11) DEFAULT '0' NOT NULL,
	category tinytext,
	attachment text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_keforum_notification_answer tinyint(3) DEFAULT '0' NOT NULL,
	tx_keforum_daily_report tinyint(3) DEFAULT '0' NOT NULL,
	tx_keforum_signature text
);