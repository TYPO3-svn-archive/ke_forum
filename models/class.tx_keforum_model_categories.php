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
 * Class that implements the model for table tx_keforum_categories.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

class tx_keforum_model_categories extends tx_lib_object {
	
	/*
	 * function __construct
	 */
	function __construct($controller = null, $parameter = null) {
		parent::tx_lib_object($controller, $parameter);
		tx_div::load("tx_div_ff");
		if($this->controller->context) tx_div_ff::load($this->controller->context->getData("field:pi_flexform"));
		$this->set("enableFields"," AND deleted=0 AND hidden=0");
		$this->set("pid",$this->controller->configurations->get("pid.storage"));
	}
	
	
	/*
	 * function load
	 */
    function load($parameters = null,$loadDetails=1,$checkAccess=1) {
		
		// fix settings
		$fields = '*';
		$tables = 'tx_keforum_categories';
		$groupBy = null;
		$orderBy = 'title';
		$where = 'hidden = 0 AND deleted = 0 ';
		if($this->get("pid")) $where.=" AND pid=".$this->get("pid");

		// variable settings
		if($parameters) if($parameters->get("uid")) $where.=" AND uid=".$parameters->get("uid");
		
		// query
		$records = array();
		$res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($fields, $tables, $where, $groupBy, $orderBy);
		$modelAccess=new tx_keforum_model_access($this->controller);
		$modelThreads=new tx_keforum_model_threads($this->controller);
		$modelPosts=new tx_keforum_model_posts($this->controller);
		
		foreach($res as $row){
			if($loadDetails){
				$row["numberOfThreads"]=$modelThreads->getNumberOfThreadsPerCategory($row["uid"]);
				$row["numberOfPosts"]=$this->getNumberOfPostsPerCategory($row["uid"]);
				$row["lastPost"]=$modelPosts->getLastPostOfCategory($row["uid"]);						
			}
			if($checkAccess && !$modelAccess->accessOnCategorybyCategoryRecord($row)) continue;
			$records[]=$row;	
		}
		$this->set("categories",$records);
	} 


	/*
	 * function getNumberOfPostsPerCategory
	 */
	function getNumberOfPostsPerCategory($categoryUid) {
		
		$modelThreads=new tx_keforum_model_threads($this->controller);
		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("category",$categoryUid);
		$modelThreads->load($parameters);	
		
		$threads=$modelThreads->get("threads");
		$out=0;
		foreach($threads as $thread) {
			$out += $thread["numberOfPosts"];
		}
		return $out;
		
	}
	
	
	/*
	 * function getCategoryByThreadUid
	 */	
	function getCategoryByThreadUid($threadUid) {
		$modelThreads=new tx_keforum_model_threads($this->controller);
		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("uid",$threadUid);
		$modelThreads->load($parameters);	
		
		$threads=$modelThreads->get("threads");
		
		$category=$threads[0]["category"];
		$parameters->set("uid",$category);
		
		$this->load($parameters);
		$categories=$this->get("categories");
		$out=$categories[0];
		
		return $out;		
	}
	
	
	/*
	 * function getModeratorsByThreadUid
	 */
	function getModeratorsByThreadUid($threadUid) {
		$modelFeUsers=new tx_keforum_model_fe_users($this->controller);
		$modelThreads=new tx_keforum_model_threads($this->controller);
		
		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("uid",$threadUid);
		$modelThreads->load($parameters);	
		
		$threads=$modelThreads->get("threads");
		
		$category=$threads[0]["category"];
		$parameters->set("uid",$category);
		
		$this->load($parameters);
		$categories=$this->get("categories");
		$categoryRecord=$categories[0];
		
		// Moderate
		$table="tx_keforum_categories_moderators_mm";
		$res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("uid_foreign", $table, "uid_local=".$categoryRecord["uid"]);
		$out=array();
		foreach($res as $row){
			$parameters->set("uid",$row["uid_foreign"]);
			$modelFeUsers->load($parameters);
			$fe_users=$modelFeUsers->get("fe_users");
			$out[]=$fe_users[0];
		}
		
		return $out;
		
	}
	
	
	/*
	 * function getModeratorsByCategoryUid
	 */
	function getModeratorsByCategoryUid($categoryUid){
		$modelFeUsers=new tx_keforum_model_fe_users($this->controller);
		
		$parameters=new tx_lib_parameters($this->controller);
	
		$parameters->set("uid",$categoryUid);
		
		$this->load($parameters);
		$categories=$this->get("categories");
		$categoryRecord=$categories[0];
		
		// Moderate
		$table="tx_keforum_categories_moderators_mm";
		$res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("uid_foreign", $table, "uid_local=".$categoryRecord["uid"]);
		$out=array();
		foreach($res as $row){
			$parameters->set("uid",$row["uid_foreign"]);
			$modelFeUsers->load($parameters);
			$fe_users=$modelFeUsers->get("fe_users");
			$out[]=$fe_users[0];
		}
		
		return $out;
	}
	
	/*
	 * function isModerated
	 */
	function isModerated($categoryUid){
		$parameters=new tx_lib_parameters($this->controller);
		$parameters->set("uid",$categoryUid);			
		$this->load($parameters);
		$categories=$this->get("categories");
		
		if(!$categories[0]) return false;
		$out=$categories[0]["moderated"];
		
		return $out;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_categories.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_categories.php']);
}

?>