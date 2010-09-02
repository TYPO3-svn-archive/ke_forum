<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_keforum_categories'] = array (
	'ctrl' => $TCA['tx_keforum_categories']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,public,write_access,read_access,moderated,moderators'
	),
	'feInterface' => $TCA['tx_keforum_categories']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_categories.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'public' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_categories.public',		
			'config' => array (
				'type' => 'check',
				'default' => 1,
			)
		),
		'write_access' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_categories.write_access',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_groups',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 99,	
				"MM" => "tx_keforum_categories_write_access_mm",
			)
		),
		'read_access' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_categories.read_access',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_groups',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 99,	
				"MM" => "tx_keforum_categories_read_access_mm",
			)
		),
		'moderated' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_categories.moderated',		
			'config' => array (
				'type' => 'check',
			)
		),
		'moderators' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_categories.moderators',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_users',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 99,	
				"MM" => "tx_keforum_categories_moderators_mm",
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, public;;;;3-3-3, write_access, read_access, moderated, moderators')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);



$TCA['tx_keforum_threads'] = array (
	'ctrl' => $TCA['tx_keforum_threads']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,title,category,author,views'
	),
	'feInterface' => $TCA['tx_keforum_threads']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_threads.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'category' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_threads.category',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_keforum_categories',	
				'foreign_table_where' => 'AND tx_keforum_categories.pid=###CURRENT_PID### ORDER BY tx_keforum_categories.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'author' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_threads.author',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_users',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'views' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_threads.views',		
			'config' => array (
				'type' => 'none',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, category;;;;3-3-3, author, views')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime')
	)
);



$TCA['tx_keforum_posts'] = array (
	'ctrl' => $TCA['tx_keforum_posts']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,title,content,author,thread,parent'
	),
	'feInterface' => $TCA['tx_keforum_posts']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_posts.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'content' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_posts.content',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'author' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_posts.author',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_users',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'thread' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_posts.thread',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_keforum_threads',	
				'foreign_table_where' => 'AND tx_keforum_threads.pid=###CURRENT_PID### ORDER BY tx_keforum_threads.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'parent' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_posts.parent',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_keforum_posts',	
				'foreign_table_where' => 'AND tx_keforum_posts.pid=###CURRENT_PID### ORDER BY tx_keforum_posts.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
			
		'attachment' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ke_forum/locallang_db.xml:tx_keforum_posts.attachment',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => '',	
				'disallowed' => 'php,php3',	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_keforum',
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),	
		
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, content;;;;3-3-3, author, thread, parent')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>