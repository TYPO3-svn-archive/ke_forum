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
 * Class that implements the model for table fe_users.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

class tx_keforum_model_cron extends tx_lib_object {
		var $urlTempFile="typo3temp/tx_keforum_model_cron.tmp";
		
		function __construct($controller = null, $parameter = null) {
				parent::tx_lib_object($controller, $parameter);
		}
		
		
		function loadUsers(){
				$userUidList=$this->get("users");
				$modelUsers=new tx_keforum_model_fe_users($this->controller);
				$parameters=new tx_lib_parameters($this->controller);
				
				$out=$modelUsers->get("fe_users");
				
				$users=array();
				
				foreach(t3lib_div::trimExplode(",",$userUidList) as $uid){
						$parameters->set("uid",$uid);
						$modelUsers->load($parameters); 
						list($user)=$modelUsers->get("fe_users");
						$users[]=$user;
				}
				$this->set("users",$users);
		}
		
		
		function updatePermissionsOnStructure($user){
				$modelAccess=new tx_keforum_model_access($this->controller);
				$structure=$this->get("structure");
				foreach($this->get("structure") as $categoryUid=>$category){
						$access=$modelAccess->accessOnCategorybyCategoryRecord($category,"read",$user);
						$structure[$categoryUid]["access"]=$access;
				}
				$this->set("structure",$structure);
		}
		
		
		function loadStructure(){
				$structure=array();
				
				$modelCategories=new tx_keforum_model_categories($this->controller);
				$modelThreads=new tx_keforum_model_threads($this->controller);
				$modelPosts=new tx_keforum_model_posts($this->controller);
				$parameters=new tx_lib_parameters($this->controller);
				
				$cats=array();$threads=array();$posts=array();
				foreach($this->get("postArr") as $row){
						if(!isset($cats[$row["category"]])){
								$parameters->set("uid",$row["category"]);
								$modelCategories->load($parameters,0,0);
								list($category)=$modelCategories->get("categories");
								$structure[$row["category"]]=$category;
								$cats[$row["category"]]=1;				
						}
						if(!isset($threads[$row["thread"]])){
								$parameters->set("uid",$row["thread"]);
								$modelThreads->load($parameters);
								list($thread)=$modelThreads->get("threads");
								$structure[$row["category"]]["_threads"][$row["thread"]]=$thread;
								$threads[$row["thread"]]=1;
						}
						if(!isset($posts[$row["uid"]])){
								$parameters->set("uid",$row["uid"]);
								$modelPosts->load($parameters);
								list($post)=$modelPosts->get("posts");
								$structure[$row["category"]]["_threads"][$row["thread"]]["_posts"][$row["uid"]]=$post;				
								$posts[$row["uid"]]=1;
						}
				}
				$this->set("structure",$structure);
		}
		
		
		function loadTempfile() {
				// Init
				$start=time();
				$users="";
				$posts="";
				$postArr=array();
				$status="init";
				
				
				if(file_exists($this->urlTempFile)){
						$data=t3lib_div::getURL($this->urlTempFile);
						
						list($start,$users,$posts,$postArr)=explode("\n",$data);
						$start=array_pop(explode("|",$start));
						$users=array_pop(explode("|",$users));
						$posts=array_pop(explode("|",$posts));
						$postArr=unserialize(array_pop(explode("|",$postArr)));
						
						// t3lib_div::debug($start,"start");
						// t3lib_div::debug($users,"users");
						// t3lib_div::debug($posts,"posts");
						// t3lib_div::debug($postArr,"postArr");
						
						if($users!=""){
								$status="running";
						} else {
								$diff=time()-$start;
								$maxDiff=$this->controller->configurations->get("interval")*60;
								$status=$diff>$maxDiff?"init":"pause";
						}
				}
				
				if($status=="init"){
						$start=time();
						
						$postsRecords=$this->getPostsInInterval($this->controller->configurations->get("interval")*60);	
						$posts=$this->buildUidList($postsRecords);
						$postArr=$this->buildPostArr($postsRecords);
						
						$usersRecords=$this->getAllCronUsers();
						$users=$this->buildUidList($usersRecords);
						
						$this->writeTempFile($start,$users,$posts,$postArr);
						$status="running";
				}
				
				$this->set("start",$start);
				$this->set("users",$users);
				$this->set("posts",$posts);
				$this->set("postArr",$postArr);
				$this->set("status",$status);
		
		}
		
		
		function removeUsersFromTemp($userUidList){
				$start=$this->get("start");
				$users=$this->get("users");
				$posts=$this->get("posts");
				$postArr=$this->get("postArr");
				
				$users=$this->buildUidList($users,$userUidList);
				$this->writeTempFile($start,$users,$posts,$postArr);
		}
		
		
		function writeTempFile($start,$users,$posts,$postArr){
				$tmpData="start|$start\n";
				$tmpData.="users|$users\n";
				$tmpData.="posts|$posts\n";
				$tmpData.="postArr|".serialize($postArr)."\n";
				
				$success=t3lib_div::writeFile($this->urlTempFile,$tmpData);			
		}
		
		
		function getAllCronUsers(){
				$modelUsers=new tx_keforum_model_fe_users($this->controller);
				$parameters=new tx_lib_parameters($this->controller);
				
				$parameters->set("daily_report",1);
				$modelUsers->load($parameters);
				$out=$modelUsers->get("fe_users");
				return $out;
		}
		
		
		function getPostsInInterval($interval){
				$modelPosts=new tx_keforum_model_posts($this->controller);
				$parameters=new tx_lib_parameters($this->controller);
				
				$start=time();
				$parameters->set("timeStart",$start-$interval);
				$parameters->set("timeEnd",$start);
				$modelPosts->load($parameters);
				$out=$modelPosts->get("posts");
				return $out;
		}
		
		
		function buildUidList($rows,$uidsToIgnore=array()){
				if(!$rows) return "";
				$recordsArr=array();
				foreach($rows as $row) {
						if(in_array($row["uid"],$uidsToIgnore)) continue;
						$recordsArr[]=$row["uid"];
				}
				$out=implode(",",$recordsArr);
				return $out;
		}
		
		
		function buildPostArr($posts){
				$out=array();
				$modelThreads=new tx_keforum_model_threads($this->controller);
				
				foreach($posts as $post){
						$record["uid"]=$post["uid"];
						$record["thread"]=$post["thread"];
						$record["category"]=$modelThreads->getCategoryUidByThreadUid($post["thread"]);
						$out[$post["uid"]]=$record;
				}
				return $out;
		}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_fe_users.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_fe_users.php']);
}

?>