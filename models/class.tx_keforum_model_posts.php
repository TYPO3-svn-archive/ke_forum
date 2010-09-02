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
 * Class that implements the model for table tx_keforum_posts.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

class tx_keforum_model_posts extends tx_lib_object {
	
	
	/*
	* function __construct
	*/
	function __construct($controller = null, $parameter = null) {
		parent::tx_lib_object($controller, $parameter);
		tx_div::load("tx_div_ff");
		if($this->controller->context) tx_div_ff::load($this->controller->context->getData("field:pi_flexform"));
		//$this->set("field",tx_div_ff::get("field","sGeneral"));
		
		$this->set("enableFields"," AND deleted=0 AND hidden=0");
		$this->set("pid",$this->controller->configurations->get("pid.storage"));
	}
	
	
	/*
	 * function load
	 */
	function load($parameters = null,$getNumberOfPosts=false,$paginate=false) {
		
		// fix settings
		$fields = '*';
		$tables = 'tx_keforum_posts';
		$groupBy = null;
		$orderBy = $this->controller->configurations->get('orderBy')?$this->controller->configurations->get('orderBy'):'crdate ASC';
		$where = "1=1 ".$this->get("enableFields");
		if($this->get("pid")) $where.=" AND pid=".$this->get("pid");
		
		$limit="";
		
		// variable settings
		if($parameters) {
			if($parameters->get("thread")) $where.=" AND thread=".$parameters->get("thread");
			if($parameters->get("limit")) $limit=$parameters->get("limit");
			if($parameters->get("uid")) $where.=" AND uid=".$parameters->get("uid");
			if($parameters->get("fe_user")) $where.=" AND author=".$parameters->get("fe_user");
			if($parameters->get("searchphrase")) $where.=" AND (title LIKE '%".$parameters->get("searchphrase")."%' OR content LIKE '%".$parameters->get("searchphrase")."%')";
			if($parameters->get("orderBy")) $orderBy=$parameters->get("orderBy");
			if($parameters->get("postsOnly")) $where.=" AND parent>=1";
			if($parameters->get("hiddenOnly")) $where=str_replace(" AND hidden=0"," AND hidden=1",$where);
			if($parameters->get("showHidden")) $where=str_replace(" AND hidden=0","",$where);
			if($parameters->get("threadUids")) $where.=" AND thread IN (".$parameters->get("threadUids").")";
			if($parameters->get("timeStart")) $where.=" AND crdate >= ".$parameters->get("timeStart");
			if($parameters->get("timeEnd")) $where.=" AND crdate <= ".$parameters->get("timeEnd");
		}
		
		// pagination
		if($paginate && !$parameters->get('print')){
			$itemsPerPage = $this->controller->configurations->get('pagination.itemsPerPage')?$this->controller->configurations->get('pagination.itemsPerPage'):10;
			$itemsInBrowser = $this->controller->configurations->get('pagination.itemsInBrowser')?$this->controller->configurations->get('pagination.itemsInBrowser'):5;
			
			$last=($orderBy=="crdate ASC" && $parameters->get("last"));
			
			$pages=tx_keforum_model_threads::paginate(null,$itemsPerPage, $itemsInBrowser,$parameters, $tables, $where, $groupBy,$last);
			$limit=$pages['limit'];
			$this->set("pages",$pages);
		}
		
		// printview -> no limit
		if ($parameters->get('print')) {
			$limit = 9999;
			$this->set('pages',array());
		}
		
		// query
		$records = array();
		$res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($fields, $tables, $where, $groupBy, $orderBy,$limit);
		$modelFeUsers=new tx_keforum_model_fe_users($this->controller);
		
		foreach($res as $row) {
			$parametersFeUser=new tx_lib_parameters($this->controller);
			$parametersFeUser->set("uid",$row["author"]);
			
			if($row["author"]) {
				$modelFeUsers->load($parametersFeUser);
				$row["_author"]=$modelFeUsers->get("fe_users");	
				if($getNumberOfPosts) {
					$numberOfPosts=$this->getNumberOfPostsPerUser($row["_author"][0]["uid"]);
					$row["_author"][0]["numberOfPosts"]=$numberOfPosts;
				}						
			} else {
					$row["_author"]=array();
			}
			$records[]=$row;	
		}
		$this->set("posts",$records);
	}
	
	
	/*
	 * function loadToModerate
	 */
        function loadToModerate($parameters = null,$getNumberOfPosts=false,$paginate=false) {
		$parameters->set("hiddenOnly",1);
		$parameters->set("uid",null);
		
		$this->load($parameters,true);
		$records=$this->get("posts");

		$parametersPost=new tx_lib_parameters($this->controller);
		
		$posts=array();
		foreach($records as $post){
			
			$parametersPost->set("thread",$post["thread"]);
			$access=$this->moderateAccess($parametersPost);
			if(!$access) continue;
			$posts[]=$post;
		}
		
		$this->set("posts",$posts);
	}
	
	
	/*
	 * function  acceptPosts
	 */
	function acceptPosts($parameters){
		if(!$parameters->get("accept")) return array();
		
		$accepted=array();
		foreach($parameters->get("accept") as $postUid){				
			$parameters->set("post",$postUid);
			$access=$this->moderateAccess($parameters);
			if(!$access) continue;
			
			$parameters->set("uid",$postUid);
			$parameters->set("showHidden",1);
			
			
			$this->load($parameters);
			$posts=$this->get("posts");
			
			$accepted[]=$posts[0];
			
	
			$sql="UPDATE tx_keforum_posts SET hidden=0 WHERE uid=$postUid";
			$GLOBALS["TYPO3_DB"]->sql_query($sql);
			
			$sql="UPDATE tx_keforum_threads SET hidden=0 WHERE uid IN(SELECT thread FROM tx_keforum_posts WHERE uid=$postUid)";
			$GLOBALS["TYPO3_DB"]->sql_query($sql);	
		}
		return $accepted;
	}
	
	
	/*
	 * function registerHit
	 */
	function registerHit($parameters){
		if(!$parameters->get("thread")) return false;
		if($parameters->get("noHit")) return true;
		
		$sql="UPDATE tx_keforum_threads SET views=IF(views IS NULL,1,views+1) WHERE uid=".$parameters->get("thread");

		$GLOBALS['TYPO3_DB']->debugOutput=TRUE;
		$GLOBALS["TYPO3_DB"]->sql_query($sql);
	}
	
	
	/*
	 * function loadForm
	 */
	function loadForm($parameters){
		$modelThreads=new tx_keforum_model_threads($this->controller);
		$parametersThreads=new tx_lib_parameters($this->controller);
		$parametersThreads->set("uid",$parameters["thread"]);			
		$modelThreads->load($parametersThreads);
		
		$thread = $modelThreads->get("threads");
		$thread[0]['lastPost'] = array_reverse($thread[0]['lastPost'],1);
		$this->set("threads",$thread);
	}
	
	
	/*
	 * function getNotifyRecipients
	 */
	function getNotifyRecipients($threadUid){
		$modelFeUsers=new tx_keforum_model_fe_users($this->controller);
		$userId=$modelFeUsers->getUserId();
		
		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("thread",$threadUid);
		
		$this->load($parameters);
		$out=array();$userUids=array();
		
		foreach($this->get("posts") as $post){
			if(!$post["author"]) continue;
			if(!$post["_author"][0]["tx_keforum_notification_answer"]) continue;
			if($post["author"]==$userId) continue;
			if(in_array($post["author"],$userUids)) continue;
			
			$userUids[]=$post["author"];
			$out[]=$post["_author"][0];
			
		}
		
		return $out;
		
	}
		
	/*
		CS: TODO letzte Beiträge unter Form zeigen, Anzahl per TS konfigurierbar
	*/
	/*
	* function save
	*/
	function save($parameters){
		
		$modelFeUsers=new tx_keforum_model_fe_users($this->controller);
		$userId=$modelFeUsers->getUserId();
		
		$success=true;
		$initialPost=$this->getInitialPostOfThread($parameters->get("thread"));
		
		$modelThread=new tx_keforum_model_threads($this->controller);
		$moderated=$modelThread->isModerated($this->parameters["thread"]);
		
		$hidden=$moderated?!$this->moderateAccess($parameters):0;
		
		if($initialPost){
			$insertArr=array(
				'pid'				=> $initialPost[0]['pid'],
				'parent'		=> $initialPost[0]['uid'],
				'thread'		=> $initialPost[0]['thread'],
				'tstamp'		=> time(),
				'crdate'		=> time(),
				'cruser_id'	=> $userId,
				'author'		=> $userId,
				'content'		=> t3lib_div::removeXSS($parameters->get('text')),
				'hidden'		=> $hidden,
				'attachment' => $parameters->get('attachment'),
			);
			$success=$GLOBALS["TYPO3_DB"]->exec_INSERTquery("tx_keforum_posts", $insertArr);
		} else {
			$modelThreads=new tx_keforum_model_threads($this->controller);	
			$parametersThread=new tx_lib_parameters($this->controller);
			$parametersThread->set("uid",$parameters->get("thread"));	
			$parametersThread->set("showHidden",1);	
			$modelThreads->load($parametersThread);
			$threads=$modelThreads->get("threads");
			
			if(!$threads) $success=false;
			else {
				$insertArr = array(
					'pid'				=> $threads[0]["pid"],
					'parent'		=> null,
					'thread'		=> $threads[0]["uid"],
					'tstamp'		=> time(),
					'crdate'		=> time(),
					'cruser_id'	=> $userId,
					'author'		=> $userId,
					'content' 		=> t3lib_div::removeXSS($parameters->get("text")),
					'hidden' 		=> $hidden,
					'attachment' => $parameters->get('attachment'),
				);
				$success=$GLOBALS["TYPO3_DB"]->exec_INSERTquery("tx_keforum_posts", $insertArr);
			}
		}
		
		
		if(!$success) {
			$this->set("errorMsg","%%%error.save%%%");
			return false;
		}
		else {
			#$this->clearCache();
			return $GLOBALS['TYPO3_DB']->sql_insert_id();
		}
	}
	
	
	
	
	/**
	* Clear Cache
	*/
	function clearCache() {
		
		// get pid config
		$pid = $this->controller->configurations->get("pid.");
		// get pids of USER modules
		$pageIDs = $pid['posts'].','.$pid['categories'].','.$pid['threads'];
		// GET Params
		$GET = t3lib_div::_GET();
		
		// get all cache entries USER module pages
		$fields = '*';
		$table = 'cache_pages';
		$where = 'page_id IN ('.$pageIDs.') ';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields,$table,$where,$groupBy='',$orderBy='',$limit='');
		while ($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			// get unserialized hash data value
			$cache_data = unserialize($row['cache_data']);
			// get unserialized hash base value
			$hash_base = unserialize($cache_data['hash_base']);
			// get cHash values
			$cHashParams = $hash_base['cHash'];
			
			// check if cHash params fit required values
			// and add to rows that will be deleted if fits 
			
			// categories page: always clear cache
			if ($row['page_id'] == $pid['categories']) $deleteRows[] = $row['id'];
			// threads page: if category fits
			if ($row['page_id'] == $pid['threads'] && $GET['tx_keforum']['category'] == $cHashParams['tx_keforum[category]']) {
				$deleteRows[] = $row['id'];
			}
			// posts page: if category and thread fits
			if ($row['page_id'] == $pid['posts']
				&& $GET['tx_keforum']['category'] == $cHashParams['tx_keforum[category]']
				&& $GET['tx_keforum']['thread'] == $cHashParams['tx_keforum[thread]']) {
				$deleteRows[] = $row['id'];
			}
		}
		
		if (sizeof($deleteRows) && is_array($deleteRows)) {
			foreach ($deleteRows as $key => $id) {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pages','id="'.intval($id).'" ');
				$i++;
			}
		}
	}
	
	
	
	
	/*
	 * function readAccess
	 */
	function readAccess($parameters){
		
		$modelAccess=new tx_keforum_model_access($this->controller);
		$out=$modelAccess->accessOnCategorybyThreadId($parameters->get("thread"),"read");
		return $out;
		
	}
	
	
	/*
	 * function writeAccess
	 */
	function writeAccess($parameters){
		$modelAccess=new tx_keforum_model_access($this->controller);

		$out=$modelAccess->accessOnCategorybyThreadId($parameters->get("thread"),"write");
		
		return $out;
		
	}
	
	
	/*
	 * function moderateAccess
	 */	
	function moderateAccess($parameters){			
		$modelAccess=new tx_keforum_model_access($this->controller);
		if($parameters->get("thread")) $out=$modelAccess->allowedToModerateByThreadId($parameters->get("thread"));
		elseif($parameters->get("post")) $out=$modelAccess->allowedToModerateByPostId($parameters->get("post"));
		
		return $out;
	}
	
	
	function validate($parameters){
				
				// check for valid title and text
				$validContent = strlen($parameters->get("text")) > 0;
				
				// check for valid upload if file selected
				if (strlen($_FILES['tx_keforum']['name']['attachment'])) {
						$validUpload = true;
						$maxSizeBytes = $this->controller->configurations->get("attachment.maxFileSize") * 1024;
						// check filesize
						if ($_FILES['tx_keforum']['size']['attachment'] > $maxSizeBytes ) {
								$validUpload = false;
						}
						// check filetype
						$pathinfo = pathinfo($_FILES['tx_keforum']['name']['attachment']);
						if (!t3lib_div::inList($this->controller->configurations->get("attachment.allowedTypes"),$pathinfo['extension'])) {
								$validUpload = false;
						}
						
						// get the destination filename
						$filefuncs = new t3lib_basicFilefunctions();
						$uploadfile = $filefuncs->getUniqueName($filefuncs->cleanFileName($_FILES['tx_keforum']['name']['attachment']), 'uploads/tx_keforum/');
						
						if($validUpload && move_uploaded_file($_FILES['tx_keforum']['tmp_name']['attachment'], $uploadfile)) {
							chmod($uploadfile,octdec('0744'));
						} else {
							#t3lib_div::debug('Error: File upload was not successfull.',1);
							$validUpload=false;
						}
				
						if ($validUpload) {
							$this->controller->parameters->set("attachment", basename($uploadfile));
							
						}
						
				}
				else $validUpload = true;
				
				if(!$validContent) $this->set("errorMsg","%%%form.posts.add.error%%%");
				
				// upload error
				if(!$validUpload) {
						$maxFileSize = $this->controller->configurations->get('attachment.maxFileSize') / 1024;
						$maxFileSize = number_format($maxFileSize, 2, ',', '').' MB';
						$allowedFileTypes = $this->controller->configurations->get('attachment.allowedTypes');
						$allowedFileTypes = str_replace(',', ', ',$allowedFileTypes);
						$uploadErrorMessage = '%%%form.threads.add.errorUpload1%%% '.$maxFileSize.'<br />';
						$uploadErrorMessage .= '%%%form.threads.add.errorUpload2%%% '.$allowedFileTypes;
						$this->set("errorMsgUpload", $uploadErrorMessage);
				}
				
				$valid = ($validUpload && $validContent);
				$this->set("valid",$valid);
				
				return $valid;
		}
	
	
	/*
	 * function getPostsForTeaser
	 */
		
	function getPostsForTeaser($parameters,$numberOfPosts=2) {
		
		$parameters->set('orderBy','crdate DESC');
		$this->load($parameters);
		$allPosts=$this->get("posts");
		$posts=array();
		
		$i=1;
		
		$modelCategories=new tx_keforum_model_categories($this->controller);
		
		$parametersAccess=new tx_lib_parameters($this->controller);
		
		
		foreach($allPosts as $post){
			$category=$modelCategories->getCategoryByThreadUid($post["thread"]);
			$post["_category"]=$category;
			
			$parametersAccess->set("thread",$post["thread"]);
			if(!$this->readAccess($parametersAccess)) continue;
			
			$posts[]=$post;
			if($i>=$numberOfPosts) break;
			$i++;
		}
		$this->set("posts",$posts);	
	}
	
	
	/*
	 * function getNumberOfPostsPerUser
	 */
	function getNumberOfPostsPerUser($feUserUid){
		
		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("fe_user",$feUserUid);
		$parameters->set("thread",'');
		$parameters->set("category",'');
		
		$this->load($parameters);
		
		$out=count($this->get("posts"));
		
		return $out;
		
	}


	/*
	 * function getInitialPostOfThread
	 */
	function getInitialPostOfThread($threadUid){

		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("limit",1);
		$parameters->set("orderBy",'tstamp ASC');
		$parameters->set("thread",$threadUid);
					
		$this->load($parameters);
		$out=$this->get("posts");
		
		return $out;
	}
	
	
	/*
	 * function getLastPostOfCategory
	 */
	function getLastPostOfCategory($categoryUid){
		$modelThreads=new tx_keforum_model_threads($this->controller);			
		$parametersThreads=new tx_lib_parameters($this->controller);
		$parametersThreads->set("category",$categoryUid);
		$threadUids=$modelThreads->getThreadUidListByCategoryUid($categoryUid);
		if(!$threadUids) return array();
		
		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("limit",1);
		$parameters->set("orderBy",'tstamp DESC');
		$parameters->set("threadUids",$threadUids);
		
					
		$this->load($parameters);
		$out=$this->get("posts");
		
		return $out;
	}
	
	
	/*
	 * function getNumberOfPostsPerThread
	 */
	function getNumberOfPostsPerThread($threadUid){

		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("thread",$threadUid);
		$parameters->set("postsOnly",1);
		$parameters->set("searchphrase","");
		
		$this->load($parameters);
		$out=count($this->get("posts"));
		
		return $out;
		
	}
		
		
	/*
	 * function getLastPostOfThread
	 */
	function getLastPostOfThread($threadUid){
		$parameters=new tx_lib_parameters($this->controller);
		$limit = $this->controller->configurations->get('showLatestPosts') > 1 ? $this->controller->configurations->get('showLatestPosts') : 1;
		$parameters->set("limit",$limit);
		$parameters->set("thread",$threadUid);
		$parameters->set("orderBy",'tstamp DESC');
		$parameters->set("searchphrase","");
		$this->load($parameters);
		$out=$this->get("posts");
		
		return $out;
	}		
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_posts.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_posts.php']);
}

?>