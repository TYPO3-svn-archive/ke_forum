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
 * Class that implements the view for search_results.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

tx_div::load('tx_lib_smartyView');

class tx_keforum_view_search_results extends tx_lib_smartyView {
		function render($view,$parameters) {
				$pid=$this->get("pid");
				
				$link = new tx_lib_link();
				$link->designator("tx_keforum");
				$link->destination($pid["categories"]);
				$this->set("url_categories",$link->makeUrl());
				
				$linkPost = new tx_lib_link();
				$linkPost->designator("tx_keforum");
				$linkPost->destination($pid["posts"]);
				
				// Suchform
				$link->destination($pid["search"]);
				$this->set("url_search",$link->makeUrl());
				$linkPost->destination($pid["posts"]);
				
				
				$results=array();
				foreach($this->get("results") as $result){
				$link->destination($pid["threads"]);				
				$link->parameters(array('category'=>$result["category"]));
				$result["url_category"]=$link->makeUrl();
				
				$link->destination($pid["posts"]);				
				$link->parameters(array('category'=>$result["category"],'thread'=>$result["uid"]));
				$result["url_thread"]=$link->makeUrl();
				
				$linkPost->parameters(array('thread'=>$result['uid'],'category'=>$result['category'],'last'=>1));
				$linkPost->anchor("post_".$result["lastPost"][0]["uid"]);
				$result["url_last_post"]=$linkPost->makeUrl();
				
				$results[]=$result;
				}
				// t3lib_div::debug($results,"results");
				
				// t3lib_div::debug($this->get("threads"),"threads");
				$this->set("results",$results);
				$this->set("searchphrase",$parameters->get("searchphrase"));
		
		
		return parent::render($view.".html");

		}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_search_results.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_search_results.php']);
}

?>