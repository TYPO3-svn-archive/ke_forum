<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$TCA['tx_keforum_categories'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_categories',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',	
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_keforum_categories.gif',
	),
);

$TCA['tx_keforum_threads'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_threads',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_keforum_threads.gif',
	),
);

$TCA['tx_keforum_posts'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_posts',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_keforum_posts.gif',
	),
);

$tempColumns = array (
	'tx_keforum_notification_answer' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ke_forum/locallang_db.xml:fe_users.tx_keforum_notification_answer',		
		'config' => array (
			'type' => 'check',
		)
	),
	'tx_keforum_daily_report' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ke_forum/locallang_db.xml:fe_users.tx_keforum_daily_report',		
		'config' => array (
			'type' => 'check',
		)
	),
	'tx_keforum_signature' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ke_forum/locallang_db.xml:fe_users.tx_keforum_signature',		
		'config' => array (
			'type' => 'text',
			'cols' => '30',	
			'rows' => '5',
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','--div--;Forum,tx_keforum_notification_answer;;;;1-1-1, tx_keforum_daily_report, tx_keforum_signature');

# CATEGORIES
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_mvc1']='pi_flexform';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc1', 'Forum: Categories');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc1', 'FILE:EXT:ke_forum/configurations/mvc1/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_mvc1'),'list_type');

# THREADS
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc2']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_mvc2']='pi_flexform';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc2', 'Forum: Threads');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc2', 'FILE:EXT:ke_forum/configurations/mvc2/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY.'_mvc2'),'list_type');

# POSTS
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc3']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_mvc3']='pi_flexform';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc3', 'Forum: Posts');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc3', 'FILE:EXT:ke_forum/configurations/mvc3/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi3', $_EXTKEY.'_mvc3'),'list_type');

# SEARCH
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc4']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_mvc4']='pi_flexform';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc4', 'Forum: Search');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc4', 'FILE:EXT:ke_forum/configurations/mvc4/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi4', $_EXTKEY.'_mvc4'),'list_type');

# TEASER
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc5']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_mvc5']='pi_flexform';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc5', 'Forum: Teaser');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc5', 'FILE:EXT:ke_forum/configurations/mvc5/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi5', $_EXTKEY.'_mvc5'),'list_type');

# MODERATION
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc6']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_mvc6']='pi_flexform';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc6', 'Forum: Moderation');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc6', 'FILE:EXT:ke_forum/configurations/mvc6/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi6', $_EXTKEY.'_mvc6'),'list_type');

# CRON NOTIFICATION
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc7']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_mvc7']='pi_flexform';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc7', 'Forum: Cronjob');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc7', 'FILE:EXT:ke_forum/configurations/mvc7/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi7', $_EXTKEY.'_mvc7'),'list_type');

# ADD POST
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc8']='layout,select_key,pages,recursive';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc8', 'Forum: Add Post');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc8', 'FILE:EXT:ke_forum/configurations/mvc8/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi8', $_EXTKEY.'_mvc8'),'list_type');


# ADD THREAD
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_mvc9']='layout,select_key,pages,recursive';
t3lib_extMgm::addStaticFile('ke_forum', './configurations/mvc9', 'Forum: Add Thread');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_mvc9', 'FILE:EXT:ke_forum/configurations/mvc9/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:ke_forum/locallang_db.xml:tt_content.list_type_pi9', $_EXTKEY.'_mvc9'),'list_type');


?>