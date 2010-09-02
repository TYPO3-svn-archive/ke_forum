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
 * Class that implements the view for threads_list.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

tx_div::load('tx_lib_smartyView');

class tx_keforum_view_threads_list extends tx_lib_smartyView {
	function render($view,$parameters){
		$pid=$this->get("pid");
		
		$link = new tx_lib_link();
		$link->designator("tx_keforum");
		
		// Suchform (Kategorie übergeben in url)
		$link->destination($pid["search"]);
		$category = $this->get('category');
		$categoryUid = $category[0]['uid'];
		$link->parameters(array('category' => $categoryUid));
		$this->set("url_search",$link->makeUrl());
				
		// Backlink
		$link->destination($pid["categories"]);
		$this->set("url_categories",$link->makeUrl());	
		$threads=array();
		
		$linkPost = $link;
		
		foreach($this->get("threads") as $thread){
			// Link Posts
			$linkPost->destination($pid["posts"]);
			$linkPost->parameters(array('thread'=>$thread['uid'],'category'=>$thread['category'],'last'=>1));
			$linkPost->anchor("post_".$thread["lastPost"][0]["uid"]);
			$thread["url_last_post"]=$linkPost->makeUrl();

			$linkPost->parameters(array('thread'=>$thread['uid'],'category'=>$thread['category']));
			$linkPost->anchor("");
			$thread["url_posts"]=$linkPost->makeUrl();
			
			if(!$thread["views"]) $thread["views"]="0";
			$threads[]=$thread;
			
		}
		
		$this->set("threads",$threads);
		
		// Add
		$link->destination($pid["threads_add"]);
		$link->parameters(array("category"=>$parameters->get("category"),"action"=>"add"));
		$link->noHash();
		// $this->set("url_add",$link->makeUrl());
		$addUrl = $link->makeUrl();
		$addUrl = t3lib_div::locationHeaderUrl($addUrl);
		$this->set("url_add",$addUrl);

		$this->set("flag",$parameters->get("flag"));
		
		
		return parent::render($view.".html");

	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_threads_list.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/views/class.tx_keforum_view_threads_list.php']);
}

?>