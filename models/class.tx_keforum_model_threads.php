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
 * Class that implements the model for table tx_keforum_threads.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php'); 

class tx_keforum_model_threads extends tx_lib_object {

        function __construct($controller = null, $parameter = null) {
                parent::tx_lib_object($controller, $parameter);

				tx_div::load('tx_div_ff');
				if($this->controller->context) tx_div_ff::load($this->controller->context->getData('field:pi_flexform'));
				$this->set('enableFields', ' AND deleted=0 AND hidden=0');
				$this->set('pid', $this->controller->configurations->get('pid.storage'));
			
        }
		
		
        function load($parameters = null,$paginate=false,$getCategory=false) {
				
                // fix settings
                $fields = '*';
                $tables = 'tx_keforum_threads';
                $groupBy = null;
                $orderBy = $this->controller->configurations->get('orderBy')?$this->controller->configurations->get('orderBy'):'crdate ASC';
                $where = '1=1 '.$this->get('enableFields');
				if($this->get('pid')) $where.=' AND pid='.$this->get('pid');
				$limit='';
				
                // variable settings
                if($parameters) {
					if($parameters->get('category')) $where.=' AND category='.$parameters->get('category');
					if($parameters->get('uid')) $where.=' AND uid='.$parameters->get('uid');
					if($parameters->get('searchphrase')) $where.=' AND title LIKE "%'.$parameters->get('searchphrase').'%" ';
					if($parameters->get("orderBy")) $orderBy=$parameters->get('orderBy');
					if($parameters->get('showHidden')) $where=str_replace(' AND hidden=0','',$where);
					
                }

				// pagination
				if($paginate) {
						$itemsPerPage = $this->controller->configurations->get('pagination.itemsPerPage')?$this->controller->configurations->get('pagination.itemsPerPage'):10;
						$itemsInBrowser = $this->controller->configurations->get('pagination.itemsInBrowser')?$this->controller->configurations->get('pagination.itemsInBrowser'):5;
						$pages=$this->paginate(null,$itemsPerPage, $itemsInBrowser,$parameters, $tables, $where, $groupBy);
						$limit=$pages['limit'];
						$this->set('pages',$pages);
				}
				
				// printview -> no limit
				if ($parameters->get('print')) {
						$itemsPerPage = 2000;
						$itemsInBrowser = 1;
						$limit = 2000;
				}
				
				
                // query
				$records = array();
				$res=$GLOBALS['TYPO3_DB']->exec_SELECTgetRows($fields, $tables, $where, $groupBy, $orderBy,$limit);
				
				$modelFeUsers=new tx_keforum_model_fe_users($this->controller);
				$modelPosts=new tx_keforum_model_posts($this->controller);
				$modelCategories=new tx_keforum_model_categories($this->controller);
				$parametersCategories=new tx_lib_parameters($this->controller);			
				
                foreach($res as $row) {
					$parametersFeUser=new tx_lib_parameters($this->controller);
					$parametersFeUser->set('uid',$row['author']);
					
					if ($row['author']) {
						$modelFeUsers->load($parametersFeUser);
						$row['_author']=$modelFeUsers->get('fe_users');						
					} else {
						$row['_author']=array();
					}
					
					if ($getCategory) {
						$parametersCategories->set('uid',$row['category']);					
						$modelCategories->load($parametersCategories);
						$row['_category'] = $modelCategories->get('categories');
					}
					$row['numberOfPosts'] = $modelPosts->getNumberOfPostsPerThread($row['uid']);
					$row['lastPost'] = $modelPosts->getLastPostOfThread($row['uid']);
                    $records[] = $row;
				}
				$this->set('threads',$records);
        }
		
		
		
		function loadForm($parameters){
				$modelCategories=new tx_keforum_model_categories($this->controller);
				$parametersCategories=new tx_lib_parameters($this->controller);
				$parametersCategories->set("uid",$parameters["category"]);			
				$modelCategories->load($parametersCategories);
				$this->set("categories",$modelCategories->get("categories"));
		}
		
		
		function save($parameters){
				$modelFeUsers=new tx_keforum_model_fe_users($this->controller);
				$userId=$modelFeUsers->getUserId();
				
				$modelCategories=new tx_keforum_model_categories($this->controller);
				$parametersCategories=$parameters;
				$parametersCategories->set("uid",$parameters->get("category"));
				$modelCategories->load($parameters);
				$categories=$modelCategories->get("categories");
				
				$moderated=$modelCategories->isModerated($parameters["category"]);
				$hidden=$moderated?!$this->moderateAccess($parameters):0;
				
				$insertArr=array(
						'pid'				=> $categories[0]['pid'],
						'category'	=> $categories[0]['uid'],
						'tstamp'		=> time(),
						'crdate'		=> time(),
						'cruser_id'	=> $userId,
						'author'		=> $userId,
						'title'				=> t3lib_div::removeXSS($parameters->get('title')),
						'hidden'		=> $hidden,						
				);
				
				$success=$GLOBALS["TYPO3_DB"]->exec_INSERTquery("tx_keforum_threads", $insertArr);
				
				if($success){
						$threadId=$GLOBALS["TYPO3_DB"]->sql_insert_id();
						$modelPosts=new tx_keforum_model_posts($this->controller);
						
						$parameters->set("thread",$threadId);
						$success=$modelPosts->save($parameters);				
				}		
				
				if(!$success) {
						$this->set("errorMsg","%%%error.save%%%");
						return false;
				} else {
						$this->set("moderated",$moderated);
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
		
		
		
		function readAccess($parameters){
			
			$modelAccess=new tx_keforum_model_access($this->controller);
			$out=$modelAccess->accessOnCategorybyCategoryId($parameters->get("category"),"read");
			return $out;
			
		}
		
		
		function writeAccess($parameters){
			$modelAccess=new tx_keforum_model_access($this->controller);

			$out=$modelAccess->accessOnCategorybyCategoryId($parameters->get("category"),"write");
			
			return $out;
			
		}
		
		
		function moderateAccess($parameters){			
			$modelAccess=new tx_keforum_model_access($this->controller);
			$out=$modelAccess->allowedToModerateByCategoryId($parameters->get("category"));
						
			return $out;
		}	
			
		
		function validate($parameters){
			
				
				// check for valid title and text
				$validContent = (strlen($parameters->get("text"))>0 && strlen($parameters->get("title"))>0);
				
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
				
				if(!$validContent) $this->set("errorMsg","%%%form.threads.add.error%%%");
				
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
		
		
		function getNumberOfThreadsPerCategory($categoryUid){
			$parameters=new tx_lib_parameters($this->controller);
			$parameters->set("category",$categoryUid);
						
			$this->load($parameters);
			$out=count($this->get("threads"));
			
			return $out;
			
		}
		function getCategoryUidByThreadUid($threadUid){
			$parameters=new tx_lib_parameters($this->controller);
			$parameters->set("uid",$threadUid);
						
			$this->load($parameters);
			$out=false;
			foreach($this->get("threads") as $thread){
				$out=$thread["category"];
			}
			
			return $out;			
		}
		function getThreadUidListByCategoryUid($categoryUid){
			$parameters=new tx_lib_parameters($this->controller);
			$parameters->set("category",$categoryUid);
						
			$this->load($parameters);
			$uids=array();
			foreach($this->get("threads") as $thread){
				$uids[]=$thread["uid"];
			}
			$out=implode(",",$uids);
			
			return $out;
			
		}
		function isModerated($threadUid){
			$parameters=new tx_lib_parameters($this->controller);
			$parameters->set("uid",$threadUid);			
			$this->load($parameters);
			$threads=$this->get("threads");
			if(!$threads[0]) return false;
			$categoryUid=$threads[0]["category"];
			
			$modelCategory=new tx_keforum_model_categories($this->controller);
			$out=$modelCategory->isModerated($categoryUid);
			
			return $out;
		}
	
		function paginate($resCount=false,$itemsPerPage, $itemsInBrowser, $parameters, $tables="", $where="", $groupBy="",$last=0){
			if(!$itemsPerPage) return "";
			
			if(!$resCount) {
				list($pageCount) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(*) as count', $tables, $where, $groupBy);
				$pageCount = ceil($pageCount['count'] / $itemsPerPage);				
			} else {
				$pageCount = ceil($resCount / $itemsPerPage);
			}
			
			if($parameters) $currentPage = $parameters->get('page');
			if(!intval($currentPage)) $currentPage = 1;
			elseif($currentPage>$pageCount) $currentPage = $pageCount;
			
			if($last) $currentPage=$pageCount;
			
			$link = new tx_lib_link();
			$link->designator("tx_keforum");
			$link->destination($GLOBALS['TSFE']->id);
			
			$minPage=0;$maxPage=0;
			if($pageCount>$itemsInBrowser){
				$minPage=$currentPage-floor($itemsInBrowser/2);
				
				if($minPage<1)$minPage=1;
				if(($minPage+$itemsInBrowser)>$pageCount)$minPage=$pageCount-$itemsInBrowser+1;
				$maxPage=$minPage+$itemsInBrowser-1;
			}
			
			
			for($p=1;$p<=$pageCount;$p++){
				
				$parameters->set("page",$p);
				$parameters->set("last",0);
				
				$link->parameters($parameters);
				
				$page = array(
					'number' => $p,
					'link' => $link->makeUrl(),
					'isCurrent' => ($p==$currentPage)?true:false,
					'isFirst' => ($p==1)?true:false,
					'isLast' => ($p==$pageCount)?true:false,
				);
				
				if($minPage){										
					if($p>=$minPage && $p<=$maxPage) $pages['list'][$p] = $page;
				}else{
					$pages['list'][$p] = $page;
				}
				
				if($p==$currentPage) $pages['current'] = $page;
				if($p==1) $pages['first'] = $page;
				if($p==$pageCount) $pages['last'] = $page;
				if($p==$currentPage-1) $pages['previous'] = $page;
				if($p==$currentPage+1) $pages['next'] = $page;
			}
			$pages['limit'] = ($itemsPerPage*($currentPage-1)).','.$itemsPerPage;
			$pages['count'] = $pageCount;
			
			return $pages;
		}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_threads.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_threads.php']);
}

?>