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
 * Class that implements the view for posts_list.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

tx_div::load('tx_lib_smartyView');

class tx_keforum_view_posts_list extends tx_lib_smartyView {

		function render($view,$parameters){
		
				$pid=$this->get("pid");
				
				$link = new tx_lib_link();
				$link->designator("tx_keforum");
				
				// Link Cat
				$link->destination($pid["categories"]);
				$this->set("url_categories",$link->makeUrl());	
				
				// Backlink
				$link->destination($pid["threads"]);
				$link->parameters(array("category"=>$parameters->get("category")));			
				$this->set("url_threads",$link->makeUrl());	
				$posts=array();
				
				// Add
				$link->destination($pid["posts_add"]);
				$link->parameters(array("category"=>$parameters->get("category"),"thread"=>$parameters->get("thread"),"action"=>"add"));			
				$link->noHash();
				// $this->set("url_add",$link->makeUrl());	
				$addUrl = $link->makeUrl();
				$addUrl = t3lib_div::locationHeaderUrl($addUrl);
				$this->set("url_add",$addUrl);	
				
				// set flag
				$this->set("flag",$parameters->get("flag"));
				
				// Printview Link
				// AK 27.07.2010
				$this->cObj = t3lib_div::makeInstance('tslib_cObj');
				unset($linkconf);
				$linkconf['parameter'] = $pid['printview'].' 800x600:resizable=1,location=1,menubar=1,scrollbars=1';
				$linkconf['additionalParams'] = '&tx_keforum[category]='.$parameters->get('category');
				$linkconf['additionalParams'] .= '&tx_keforum[thread]='.$parameters->get('thread');
				$linkconf['additionalParams'] .= '&tx_keforum[print]=1';
				$linkconf['useCacheHash'] = 0;
				$linkconf['no_cache'] = 1;
				$linkconf['JSwindow'] = 1;
				$printLink  = $this->cObj->typoLink('%%%posts.print%%%',$linkconf);
				$this->set("popup_print",$printLink);
				
				
				
				// UNIVERSAL KEWORKS BROWSER
				// AK 13.04.2010
				if (t3lib_extMgm::isLoaded('ke_ukb')) {
					$this->set('ukb', true);
					require_once(t3lib_extMgm::extPath('ke_ukb').'class.ke_ukb.php');
					$ukb = t3lib_div::makeInstance('ke_ukb');
					$this->set('ukb_content',$ukb->renderContent('tx_keforum_threads', $parameters->get("thread")));
					$this->set('ukb_form',$ukb->renderForm());
				}
				
				// printview
				if ($parameters['print']) return parent::render("thread_print.html");
				// normal view
				else return parent::render($view.".html");

		}
		
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_posts_list.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_posts_list.php']);
}

?>