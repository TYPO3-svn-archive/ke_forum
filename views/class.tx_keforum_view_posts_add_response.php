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

class tx_keforum_view_posts_add_response extends tx_lib_smartyView {

		function render($view,$parameters){
		
				$pid=$this->get("pid");
				$flag = $this->get("flag");
				
				// Backlink
				$link = new tx_lib_link();
				$link->designator("tx_keforum");
				$link->parameters=array(
						"category"=>$parameters->get("category"),
						"thread"=>$parameters->get("thread"),
						"noHit"=>1,
						"last"=>1,
				);
				$link->destination($pid["posts"]);
				$this->set("url_posts",$link->makeUrl());
				
				return parent::render($view.".html");

		}
		
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_posts_list.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_posts_list.php']);
}

?>