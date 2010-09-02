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

class tx_keforum_model_fe_users extends tx_lib_object {

        function __construct($controller = null, $parameter = null) {
                parent::tx_lib_object($controller, $parameter);
				tx_div::load("tx_div_ff");
				if($this->controller->context) tx_div_ff::load($this->controller->context->getData("field:pi_flexform"));
				$this->set("enableFields"," AND disable=0 AND deleted=0");
        }


		
        function load($parameters = null) {
		
				// fix settings
				$fields = '*';
				$tables = 'fe_users';
				$groupBy = null;
				$orderBy = null;
				$where = "1=1 ".$this->get("enableFields");
				
				// variable settings
				if($parameters) {
						if($parameters->get("uid")) $where.=" AND uid=".$parameters->get("uid");
						if($parameters->get("daily_report")) $where.=" AND tx_keforum_daily_report=".$parameters->get("daily_report");
				}
				
				// query
				$records = array();
				$res=$GLOBALS["TYPO3_DB"]->exec_SELECTgetRows($fields, $tables, $where, $groupBy, $orderBy);
				foreach($res as $row) {
						$records[]=$row;	
				}
				$this->set("fe_users",$records);
        }

		function getUserId(){
				if(!$GLOBALS["TSFE"]->fe_user->user) return false;
				$out=$GLOBALS["TSFE"]->fe_user->user["uid"];
				return $out;
		}
	
		function getUser(){
				if(!$GLOBALS["TSFE"]->fe_user->user) return false;
				return $GLOBALS["TSFE"]->fe_user->user;			
		}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_fe_users.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/models/class.tx_keforum_model_fe_users.php']);
}

?>