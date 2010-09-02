<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_keforum_categories=1
');

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

if(TYPO3_MODE == 'FE') tx_div::autoLoadAll($_EXTKEY);


?>