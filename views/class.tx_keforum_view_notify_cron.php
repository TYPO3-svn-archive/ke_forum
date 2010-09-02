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
 * Class that implements the view for notify_cron.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

tx_div::load('tx_lib_smartyView');

class tx_keforum_view_notify_cron extends tx_lib_smartyView {
		function render($view,$parameters=null){
			// t3lib_div::debug($this->get("structure"),"string");
			
			$maildata=$this->get("maildata");
			$pid=$this->get("pid");
			
			$this->set("forumname",$maildata["forum_name"]);
			$this->set("footer",$maildata["footer"]);

			$link = new tx_lib_link();
			$link->designator("tx_keforum");
			$link->destination($pid["profile"]);
			$this->set("linkProfile",t3lib_div::locationHeaderUrl('').$link->makeUrl());
			
			
			$threads=array();
			
			foreach($this->get("structure") as $category){
				if(!$category["access"]) continue;
				foreach($category["_threads"] as $thread){
					$posts=array();
					$thread["category_title"]=$category["title"];
					foreach($thread["_posts"] as $post){		
						$text=$post["content"];
						if($text>300) $text=substr($text,0,300)."...";
						$post["content"]=$text;
						
						$link->destination($pid["posts"]);				
						$link->parameters(array('uid'=>$post["uid"],"last"=>1));
						$link->anchor("post_".$post["uid"]);
						$post["link"]=t3lib_div::locationHeaderUrl('').$link->makeUrl();
						$posts[]=$post;
					}
					$thread["posts"]=$posts;
				}
				$threads[]=$thread;
			}
			
			$this->set("threads",$threads);
			
			


			// 
			// 
			$link->destination($pid["posts"]);				
			$link->parameters(array('uid'=>$post["uid"],"last"=>1));
			$link->anchor("post_".$post["uid"]);
			$this->set("linkPost",t3lib_div::locationHeaderUrl('').$link->makeUrl());
			// 

			
			return parent::render($view.".html");

		}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_notify_cron.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_notify_cron.php']);
}

?>