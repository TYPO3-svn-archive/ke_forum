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
 * Class that implements the controller "posts" for tx_keforum.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

tx_div::load('tx_lib_controller');


class tx_keforum_controller_posts extends tx_lib_controller {

    var $targetControllers = array();

    function __construct($parameter1 = null, $parameter2 = null) {
        parent::tx_lib_controller($parameter1, $parameter2);
        $this->setDefaultDesignator('tx_keforum');
    }


    /**
     * Implementation of listAction()
     */
    function listAction() {
	
		$modelClassName = tx_div::makeInstanceClassName('tx_keforum_model_posts');
        $modelClassNameThreads = tx_div::makeInstanceClassName('tx_keforum_model_threads');
    
		// set Models 
        $model = new $modelClassName($this);
		$model->load($this->parameters,1,true);
		$model->registerHit($this->parameters);

		if(!$model->readAccess($this->parameters)) return;
	
		// Thread laden, Titel wird angezeigt
		$modelThreads = new $modelClassNameThreads($this);
		$parametersThreads=$this->parameters;
		$parametersThreads->set("uid",$this->parameters->get("thread"));
		$modelThreads->load($this->parameters,false,true);
	
		$viewClassName = tx_div::makeInstanceClassName('tx_keforum_view_posts_list');
        $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
        $view = new $viewClassName($this,$model);
		
        $view->setPathToTemplateDirectory($this->configurations->get('pathToTemplateDirectory'));
        $view->set("pid",$this->configurations->get('pid.'));
        $view->set("dateFormat",$this->configurations->get('date.format.default'));
        $view->set("dateFormatMember",$this->configurations->get('date.format.member'));
		$view->set("threads",$modelThreads->get("threads"));
		$view->set("writeAccess",$model->writeAccess($this->parameters));
		
		
		
		$out=$view->render('posts_list',$this->parameters);
		
		$translator = new $translatorClassName($this, $view);
		$translator->setPathToLanguageFile('EXT:tx_keforum/locallang.xml');
		$out = $translator->translate($out);
		
		return $out;
    }
    
    
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/controllers/class.tx_keforum_controller_posts.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/controllers/class.tx_keforum_controller_posts.php']);
}

?>