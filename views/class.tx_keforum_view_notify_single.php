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
 * Class that implements the view for notify_single.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */
tx_div::load('tx_lib_smartyView');

class tx_keforum_view_notify_single extends tx_lib_smartyView {

		function render($view,$parameters=null) {
				
				// get params
				$maildata=$this->get("maildata");
				$user=$this->get("user");
				$post=$this->get("post");
				$recipient=$this->get("recipient");
				$pid=$this->get("pid");
				$thread = $this->get('thread');
				$category = $this->get('category');
				$dateFormat = $this->get('dateFormat');
				
				// set params
				$this->set("forumname",$maildata["forum_name"]);
				$this->set("footer",$maildata["footer"]);
				$this->set("username",$user["username"]);
				$this->set("recipient_name",$recipient["username"]);
				
				// crop text?
				$text=$post["content"];
				if(strlen($text) > 300) $text=substr($text,0,300)."...";
				$this->set("text",$text);
				
				// link to profile
				$link = new tx_lib_link();
				$link->designator("tx_keforum");
				$link->destination($pid["profile"]);
				$this->set("linkProfile",t3lib_div::locationHeaderUrl('').$link->makeUrl());
				
				// link to post
				$link = new tx_lib_link();
				$link->designator("tx_keforum");
				$link->destination($pid["posts"]);
				$link->parameters(
						array(
								'category' => $category['uid'],
								'thread' => $thread['uid'],
								'last' => 1,
								#'uid'=>$post['uid'],
						)
				);
				$link->anchor("post_".$post["uid"]);
				$this->set("linkPost",t3lib_div::locationHeaderUrl('').$link->makeUrl());
				
				// link to moderation
				$link = new tx_lib_link();
				$link->designator("tx_keforum");
				$link->destination($pid["moderate"]);				
				$link->parameters(array());
				$link->anchor("");
				$this->set("linkModerate",t3lib_div::locationHeaderUrl('').$link->makeUrl());					
				
				// render view
				return parent::render($view.".html");
		
		}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_notify_single.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_notify_single.php']);
}

?>