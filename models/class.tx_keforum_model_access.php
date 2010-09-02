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
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

class tx_keforum_model_access extends tx_lib_object {

     function __construct($controller = null, $parameter = null) {
	  parent::tx_lib_object($controller, $parameter);
     }
	
     ### Public Functions used by models
     function accessOnCategorybyCategoryRecord($categoryRecord,$mode="read",$user=false){
	  $allowedFeGroups=$this->getAllowedFeGroupsByCategoryRecord($categoryRecord);
	  #t3lib_div::debug($categoryRecord,'category');
	  #t3lib_div::debug($allowedFeGroups,"Cat: ".$categoryRecord["title"]);
	  $out=$this->hasAccess($allowedFeGroups,$mode,$user);		
	  return $out;
     }
     
     function accessOnCategorybyCategoryId($categoryId,$mode="read",$user=false){
	  $allowedFeGroups=$this->getAllowedFeGroupsByCategoryId($categoryId);
	  $out=$this->hasAccess($allowedFeGroups,$mode,$user);		
	  return $out;
     }

     function accessOnCategorybyThreadId($threadId,$mode="read"){		
	  $allowedFeGroups=$this->getAllowedFeGroupsByThreadId($threadId);
	  $out=$this->hasAccess($allowedFeGroups,$mode);		
	  return $out;
     }

     function allowedToModerateByPostId($postId){
	  $model=new tx_keforum_model_posts($this->controller);	
	  $parameters=new tx_lib_parameters($this->controller);
	  $parameters->set("uid",$postId);
	  $parameters->set("showHidden",1);
	  $model->load($parameters);
	  $posts=$model->get("posts");
	  if(!$posts[0]) return false;
	  $out=$this->allowedToModerateByThreadId($posts[0]["thread"]);
	  return $out;	
     }
     
     function allowedToModerateByThreadId($threadId){
	  $model=new tx_keforum_model_threads($this->controller);	
	  $parameters=new tx_lib_parameters($this->controller);
	  $parameters->set("uid",$threadId);
	  $parameters->set("showHidden",1);
	  
	  $model->load($parameters);
	  $threads=$model->get("threads");
	  
	  if(!$threads[0]) return false;
	  
	  $out=$this->allowedToModerateByCategoryId($threads[0]["category"]);
	  return $out;	
     }
	
	
     function allowedToModerateByCategoryId($categoryId){
	  $userId=tx_keforum_model_fe_users::getUserId();
	  
	  $table="tx_keforum_categories_moderators_mm";
	  $res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("uid_foreign", $table, "uid_local=".$categoryId);
	  
	  $out=false;
	  foreach($res as $row){
	       if($row["uid_foreign"]==$userId) $out=true;
	  }
	  return $out;
     }
     
     
     ### Private Functions
     function hasAccess($allowedFeGroups,$mode="read",$user=false){
	  
	  // public raus, das macht keinen Sinn
	  #if($allowedFeGroups["public"]) return true;
	  
	  $groups=$allowedFeGroups[$mode];
	  
	  // access if no group is selected in category
	  #t3lib_div::debug($groups,'allowedFeGroups');
	  if (!sizeof($groups)) return true;
	  
	  
	  if(!is_array($groups)) $groups=array();
	  $modelFeUsers=new tx_keforum_model_fe_users($this->controller);
	  if(!$user) $user=$modelFeUsers->getUser();
	  if(!$user) return false;
	  
	  $usergroups=t3lib_div::trimExplode(",",$user["usergroup"]);
	  foreach($usergroups as $usergroup){
	       if(in_array($usergroup,$groups)) return true;
	  }
	  return false;
     }
     
     function getAllowedFeGroupsByCategoryId($uid){
	  $model=new tx_keforum_model_categories($this->controller);	
     
	  $parameters=new tx_lib_parameters($this->controller);
	  $parameters->set("uid",$uid);
	  $model->load($parameters);
	  $categories=$model->get("categories");
     
	  if(!$categories[0]) return false;
     
	  $out=$this->getAllowedFeGroupsByCategoryRecord($categories[0],$mode);
	  return $out;
     }
     
     function getAllowedFeGroupsByThreadId($uid){
	  $model=new tx_keforum_model_threads($this->controller);	
     
	  $parameters=new tx_lib_parameters($this->controller);
	  $parameters->set("uid",$uid);
	  $model->load($parameters);
	  $threads=$model->get("threads");
     
	  if(!$threads[0]) return false;
     
	  $out=$this->getAllowedFeGroupsByCategoryId($threads[0]["category"],$mode);
	  return $out;
     }
     
     
     function getAllowedFeGroupsByCategoryRecord($categoryRecord){
	  $out=array("moderate"=>array(),"write"=>array(),"read"=>array());
	  
	  #$out["public"]=$categoryRecord["public"];
	  
	  /*
	  FIXME Moderated gehen nicht auf Benutzergruppe, sondern auf User
	  */
	  
	  // Moderate
	  $table="tx_keforum_categories_moderators_mm";
	  $res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("uid_foreign", $table, "uid_local=".$categoryRecord["uid"]);
	  foreach($res as $row){
	       $out["moderate"][]=$row["uid_foreign"];
	  }
	  
	  // Write
	  $out["write"]=$out["moderate"]; // Moderators are allowed to write
	  $table="tx_keforum_categories_write_access_mm";
	  $res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("uid_foreign", $table, "uid_local=".$categoryRecord["uid"]);
	  foreach($res as $row){
	       $out["write"][]=$row["uid_foreign"];
	  }		
	  
	  // Read
	  #$out["read"]=$out["write"]; // Writers are allowed to read
	  
	  $table="tx_keforum_categories_read_access_mm";
	  $res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows("uid_foreign", $table, "uid_local=".$categoryRecord["uid"]);
	  foreach($res as $row){
	       $out["read"][]=$row["uid_foreign"];
	  }
	  
	  return $out;
     }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_access.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_access.php']);
}

?>