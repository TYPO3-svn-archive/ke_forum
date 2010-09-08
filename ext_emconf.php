<?php

########################################################################
# Extension Manager/Repository config file for ext "ke_forum".
#
# Auto generated 03-09-2010 12:58
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Forum',
	'description' => 'Forum software with basic functions',
	'category' => 'plugin',
	'author' => 'Andreas Kiefer (kennziffer.com)',
	'author_email' => 'kiefer@kennziffer.com',
	'shy' => '',
	'dependencies' => 'cms,div,lib,smarty',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'www.kennziffer.com GmbH',
	'version' => '0.0.3',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'div' => '',
			'lib' => '',
			'smarty' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:92:{s:9:"ChangeLog";s:4:"891d";s:10:"README.txt";s:4:"8d29";s:12:"ext_icon.gif";s:4:"194f";s:17:"ext_localconf.php";s:4:"c9da";s:14:"ext_tables.php";s:4:"071c";s:14:"ext_tables.sql";s:4:"0b90";s:30:"icon_tx_keforum_categories.gif";s:4:"74a0";s:25:"icon_tx_keforum_posts.gif";s:4:"2d7b";s:27:"icon_tx_keforum_threads.gif";s:4:"13a9";s:13:"locallang.xml";s:4:"1eec";s:16:"locallang_db.xml";s:4:"5cb3";s:23:"locallang_flexforms.xml";s:4:"0bb9";s:7:"tca.php";s:4:"142e";s:33:"configurations/mvc1/constants.txt";s:4:"96c1";s:32:"configurations/mvc1/flexform.xml";s:4:"fa28";s:29:"configurations/mvc1/setup.txt";s:4:"772d";s:32:"configurations/mvc2/flexform.xml";s:4:"fa28";s:29:"configurations/mvc2/setup.txt";s:4:"a141";s:32:"configurations/mvc3/flexform.xml";s:4:"fa28";s:29:"configurations/mvc3/setup.txt";s:4:"8d46";s:32:"configurations/mvc4/flexform.xml";s:4:"fa28";s:29:"configurations/mvc4/setup.txt";s:4:"72f3";s:32:"configurations/mvc5/flexform.xml";s:4:"fa28";s:29:"configurations/mvc5/setup.txt";s:4:"df84";s:32:"configurations/mvc6/flexform.xml";s:4:"fa28";s:29:"configurations/mvc6/setup.txt";s:4:"7504";s:32:"configurations/mvc8/flexform.xml";s:4:"fa28";s:29:"configurations/mvc8/setup.txt";s:4:"bda7";s:32:"configurations/mvc9/flexform.xml";s:4:"fa28";s:29:"configurations/mvc9/setup.txt";s:4:"8f09";s:52:"controllers/class.tx_keforum_controller_add_post.php";s:4:"a3a5";s:54:"controllers/class.tx_keforum_controller_add_thread.php";s:4:"8a93";s:54:"controllers/class.tx_keforum_controller_categories.php";s:4:"f858";s:53:"controllers/class.tx_keforum_controller_moderator.php";s:4:"bdf8";s:49:"controllers/class.tx_keforum_controller_posts.php";s:4:"ec86";s:50:"controllers/class.tx_keforum_controller_search.php";s:4:"29e4";s:50:"controllers/class.tx_keforum_controller_teaser.php";s:4:"4d46";s:51:"controllers/class.tx_keforum_controller_threads.php";s:4:"cd79";s:14:"doc/manual.sxw";s:4:"110e";s:40:"models/class.tx_keforum_model_access.php";s:4:"7dae";s:44:"models/class.tx_keforum_model_categories.php";s:4:"2aeb";s:42:"models/class.tx_keforum_model_fe_users.php";s:4:"8ab1";s:39:"models/class.tx_keforum_model_posts.php";s:4:"9707";s:40:"models/class.tx_keforum_model_search.php";s:4:"0292";s:41:"models/class.tx_keforum_model_threads.php";s:4:"164f";s:20:"res/css/ke_forum.css";s:4:"38df";s:18:"res/img/attach.gif";s:4:"4e27";s:25:"res/img/category_icon.gif";s:4:"d425";s:28:"res/img/forum_arrow_icon.gif";s:4:"7648";s:35:"res/img/forum_arrow_icon_bright.gif";s:4:"2080";s:33:"res/img/forum_arrow_icon_dark.gif";s:4:"292c";s:38:"res/img/forum_rubriken_icon_bright.gif";s:4:"d425";s:36:"res/img/forum_rubriken_icon_dark.gif";s:4:"961a";s:36:"res/img/forum_searchresults_icon.gif";s:4:"58d3";s:43:"res/img/forum_searchresults_icon_bright.gif";s:4:"2359";s:41:"res/img/forum_searchresults_icon_dark.gif";s:4:"aead";s:35:"res/img/forum_singleThread_icon.gif";s:4:"deb3";s:40:"res/img/forum_singleView_icon_bright.gif";s:4:"5901";s:38:"res/img/forum_singleView_icon_dark.gif";s:4:"17f8";s:36:"res/img/forum_teaser_icon_bright.gif";s:4:"0c7d";s:29:"res/img/forum_themen_icon.gif";s:4:"7d6c";s:31:"res/img/forum_themen_icon_1.gif";s:4:"573a";s:36:"res/img/forum_themen_icon_bright.gif";s:4:"7d6c";s:34:"res/img/forum_themen_icon_dark.gif";s:4:"8c01";s:27:"res/img/horizontal_line.gif";s:4:"96ed";s:39:"res/img/response-message-background.gif";s:4:"ac4d";s:29:"res/img/teaser_background.gif";s:4:"b7e6";s:30:"templates/categories_list.html";s:4:"38f0";s:24:"templates/moderator.html";s:4:"fb7c";s:31:"templates/notify_moderator.html";s:4:"15bf";s:28:"templates/notify_single.html";s:4:"f071";s:24:"templates/posts_add.html";s:4:"8fd4";s:33:"templates/posts_add_response.html";s:4:"025b";s:25:"templates/posts_list.html";s:4:"eb8d";s:29:"templates/search_results.html";s:4:"c705";s:21:"templates/teaser.html";s:4:"590e";s:27:"templates/thread_print.html";s:4:"5f07";s:26:"templates/threads_add.html";s:4:"40fb";s:35:"templates/threads_add_response.html";s:4:"29de";s:27:"templates/threads_list.html";s:4:"9baa";s:47:"views/class.tx_keforum_view_categories_list.php";s:4:"8e2c";s:41:"views/class.tx_keforum_view_moderator.php";s:4:"2e40";s:48:"views/class.tx_keforum_view_notify_moderator.php";s:4:"3864";s:45:"views/class.tx_keforum_view_notify_single.php";s:4:"65aa";s:41:"views/class.tx_keforum_view_posts_add.php";s:4:"773c";s:50:"views/class.tx_keforum_view_posts_add_response.php";s:4:"d744";s:42:"views/class.tx_keforum_view_posts_list.php";s:4:"d58a";s:46:"views/class.tx_keforum_view_search_results.php";s:4:"a6c3";s:38:"views/class.tx_keforum_view_teaser.php";s:4:"3cb0";s:43:"views/class.tx_keforum_view_threads_add.php";s:4:"d9fd";s:52:"views/class.tx_keforum_view_threads_add_response.php";s:4:"2c1f";s:44:"views/class.tx_keforum_view_threads_list.php";s:4:"030c";}',
);

?>