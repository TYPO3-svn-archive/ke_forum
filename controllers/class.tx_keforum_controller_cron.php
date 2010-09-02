<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */




/**
 * Class that implements the controller "threads" for tx_keforum.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

tx_div::load('tx_lib_controller');


class tx_keforum_controller_cron extends tx_lib_controller {

    var $targetControllers = array();

    function __construct($parameter1 = null, $parameter2 = null) {
        parent::tx_lib_controller($parameter1, $parameter2);
        $this->setDefaultDesignator('tx_keforum');
    }


	function sendAction(){
		
		$modelClassNameCron = tx_div::makeInstanceClassName('tx_keforum_model_cron');
	    $modelClassNamePosts = tx_div::makeInstanceClassName('tx_keforum_model_posts');
	    $modelClassNameThreads = tx_div::makeInstanceClassName('tx_keforum_model_threads');
	    
		// set Models 
	    $modelCron = new $modelClassNameCron($this);
		$modelCron->loadTempfile();
		if($modelCron->get("status")!="running") return "PAUSE";
		
		$modelCron->loadUsers();
		$modelCron->loadStructure();
		
		$viewClassName = tx_div::makeInstanceClassName('tx_keforum_view_notify_cron');
	    $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
	    $view = new $viewClassName($this,$modelCron);
		
        $view->setPathToTemplateDirectory($this->configurations->get('pathToTemplateDirectory'));
        $view->set("pid",$this->configurations->get('pid.'));
        $view->set("maildata",$this->configurations->get('mail.'));
		$view->set("dateFormat",$this->configurations->get('date.format.default'));
		
		#$controllerPosts = new tx_keforum_controller_posts();
		$controllerPosts = new tx_keforum_controller_add_post();
		$controllerPosts->parameters=$this->parameters;
		$controllerPosts->configurations=$this->configurations;
		
		$usersProcessed=array();
		$i=1;
		foreach($modelCron->get("users") as $user){
			if($i>$this->configurations->get('mailsPerRequest')) break;
			
			$modelCron->updatePermissionsOnStructure($user);
			$view->set("structure",$modelCron->get("structure"));
			$view->set("user",$user);
			
		    $mailtext=$view->render('notify_cron',$this->parameters);
			
			$translator = new $translatorClassName($this, $view);
			$translator->setPathToLanguageFile('EXT:tx_keforum/locallang.xml');
			$mailtext = $translator->translate($mailtext);	
			
			$usersProcessed[]=$user["uid"];
			$i++;
			$controllerPosts->sendMail($mailtext,$user["email"],$this->configurations->get("mail.subject.notify_cron"),$this->configurations->get('mail.'));
			
		}
		$modelCron->removeUsersFromTemp($usersProcessed);
	    return $out;

	
	}
	function updateTemp(){
		
	}
	function writeTemp(){
		
	}
	function getValueFromTemp(){
		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/controllers/class.tx_keforum_controller_cron.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/controllers/class.tx_keforum_controller_cron.php']);
}

?>