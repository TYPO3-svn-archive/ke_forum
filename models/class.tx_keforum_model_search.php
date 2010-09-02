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

class tx_keforum_model_search extends tx_lib_object {

        function __construct($controller = null, $parameter = null) {
                parent::tx_lib_object($controller, $parameter);

				tx_div::load("tx_div_ff");
				tx_div_ff::load($this->controller->context->getData("field:pi_flexform"));
				//$this->set("field",tx_div_ff::get("field","sGeneral"));
				
				$this->set("enableFields"," AND deleted=0 AND hidden=0");
				
				
        }

		
        function load($parameters = null,$paginate=false) {
				$modelClassName = tx_div::makeInstanceClassName('tx_keforum_model_categories');
		        $parametersSearch=$parameters;
			
				$modelThreads=new tx_keforum_model_threads($this->controller);
				$modelThreads->load($parametersSearch);
				$threads=$modelThreads->get("threads");
			
				$threadUids=array();
				$records=array();
				
				foreach($threads as $thread){
					if(in_array($thread["uid"],$threadUids)) continue;
					$threadUids[]=$thread["uid"];
					$records[]=$thread;
				}
				
				$modelPosts=new tx_keforum_model_posts($this->controller);
				$modelPosts->load($parametersSearch);
				$posts=$modelPosts->get("posts");
				
				$parametersSearch=new tx_lib_parameters($this->controller);
				
				foreach($posts as $post){
					if(in_array($post["thread"],$threadUids)) continue;
					
					$parametersSearch->set("uid",$post["thread"]);
					$parametersSearch->set("searchphrase",null);
					
					$modelThreads->load($parametersSearch); 
					$threads=$modelThreads->get("threads");
					
					if($threads) $records[]=$threads[0];
					$threadUids[]=$post["thread"];
					
				}
			
				
				$results=array();
				$modelCategories=new tx_keforum_model_categories($this->controller);
				$categories=$modelCategories->get("categories");
				
				foreach($records as $record){
					$parametersSearch->set("uid",$record["category"]);
					$parametersSearch->set("searchphrase",null);
					$modelCategories->load($parametersSearch,false);
					
					$record["_category"]=$modelCategories->get("categories");
					if(!$record["_category"]) continue; // no access
					
			
					
					$results[]=$record;
				}
				
				$this->set("numberOfResults",count($results));
				
				// pagination
				if($paginate && count($results)){
					$itemsPerPage = $this->controller->configurations->get('pagination.itemsPerPage')?$this->controller->configurations->get('pagination.itemsPerPage'):10;
					$itemsInBrowser = $this->controller->configurations->get('pagination.itemsInBrowser')?$this->controller->configurations->get('pagination.itemsInBrowser'):5;

					$page=$parameters->get("page")?$parameters->get("page"):1;
					$pages=tx_keforum_model_threads::paginate(count($results),$itemsPerPage, $itemsInBrowser,$parameters);
					$this->set("pages",$pages);
					
					//Drop non relevant results
					$croppedResults=array();
					$first=($page-1)*$itemsPerPage;
					$last=(($page*$itemsPerPage)-1)>count($results)-1?count($results)-1:(($page*$itemsPerPage)-1);
				
					
					for($i=$first;$i<=$last;$i++){
						
						$croppedResults[]=$results[$i];
					}
					$results=$croppedResults;
				}			
				
				$this->set("results",$results);
        }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_search.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_search.php']);
}

?>