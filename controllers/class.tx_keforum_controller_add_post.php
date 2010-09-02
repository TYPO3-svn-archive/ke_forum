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
 * Class that implements the controller "posts" for tx_keforum.
 *
 * @author	Andreas Kiefer, www.kennziffer.com GmbH <kiefer@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_keforum
 */

tx_div::load('tx_lib_controller');


class tx_keforum_controller_add_post extends tx_lib_controller {

    var $targetControllers = array();

    function __construct($parameter1 = null, $parameter2 = null) {
        parent::tx_lib_controller($parameter1, $parameter2);
        $this->setDefaultDesignator('tx_keforum');
    }
    
    /**
     * Implementation of addAction()
     */
    function addAction() {
		
		
		
		$modelClassName = tx_div::makeInstanceClassName('tx_keforum_model_posts');
		$viewClassName = tx_div::makeInstanceClassName('tx_keforum_view_posts_add');
		$translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
		
		$model = new $modelClassName($this);
		if(!$model->writeAccess($this->parameters)) return;
		
		$submit=$this->parameters->get('submit');		
		$this->set('submit',$submit);
		if($submit) $valid=$model->validate($this->parameters);
		
		// form sent and data is valid
		if ($submit && $valid) {
			
			$lastId=$model->save($this->parameters);
			
			if($lastId) {
				$model->clearCache();
				$modelThread=new tx_keforum_model_threads($this);
				$moderated=$modelThread->isModerated($this->parameters['thread']);
				if($moderated) {
					$modelCategories=new tx_keforum_model_categories($this);
					$recipients=$modelCategories->getModeratorsByThreadUid($this->parameters->get('thread'));
					$this->sendNotifyMails($recipients,$lastId,'notify_moderator','mail.subject.notify_moderator');
					$flag='moderate';
				} else {
					$recipients=$model->getNotifyRecipients($this->parameters->get('thread'));				
					$this->sendNotifyMails($recipients,$lastId,'notify_single','mail.subject.notify_single');
					$flag='insert';
				}
				
				$model->loadForm($this->parameters);
				$viewClassName = tx_div::makeInstanceClassName('tx_keforum_view_posts_add_response');
				$view = new $viewClassName($this,$model);
				
				$view->setPathToTemplateDirectory($this->configurations->get('pathToTemplateDirectory'));
				$view->set('pid',$this->configurations->get('pid.'));
				$view->set('flag',$flag);
				
				$out=$view->render('posts_add_response',$this->parameters);
				
				$translator = new $translatorClassName($this, $view);
				$translator->setPathToLanguageFile('EXT:tx_keforum/locallang.xml');
				$out = $translator->translate($out);
					
				return $out;
			}
		}
		
		$model->loadForm($this->parameters);
		$view = new $viewClassName($this,$model);
		
		$view->setPathToTemplateDirectory($this->configurations->get('pathToTemplateDirectory'));
		$view->set('pid',$this->configurations->get('pid.'));
		$view->set("dateFormat",$this->configurations->get('date.format.default'));
		$view->set('showLatestPosts', $this->configurations->get("showLatestPosts"));
		$view->set('attachment', $this->configurations->get('attachment.'));
		
		$out=$view->render('posts_add',$this->parameters);
		
		$translator = new $translatorClassName($this, $view);
		$translator->setPathToLanguageFile('EXT:tx_keforum/locallang.xml');
		$out = $translator->translate($out);
		
		return $out;
    }
    
    
    function sendNotifyMails($recipients,$postUid,$template='notify_single',$keySubject='mail.subject.notify_single'){
    
		$modelClassName = tx_div::makeInstanceClassName('tx_keforum_model_posts');
		$modelClassNameThreads = tx_div::makeInstanceClassName('tx_keforum_model_threads');
		$modelClassNameCategories = tx_div::makeInstanceClassName('tx_keforum_model_categories');
		
		$this->parameters->set('uid',$postUid);
		$model = new $modelClassName($this);
		$model->load($this->parameters);
		$posts=$model->get('posts');
	
		// Thread laden, Titel wird angezeigt
		$modelThreads = new $modelClassNameThreads($this);
		$parametersThreads=$this->parameters;
		$parametersThreads->set('uid',$posts[0]['thread']);
		$modelThreads->load($parametersThreads);
		$threads=$modelThreads->get('threads');
		
		//Kategorie holen, Titel wird angezeigt
		$modelCategories = new $modelClassNameCategories($this);
		$category=$modelCategories->getCategoryByThreadUid($posts[0]['thread']);	
		
		$viewClassName = tx_div::makeInstanceClassName('tx_keforum_view_notify_single');
		$translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
	
		$view = new $viewClassName($this,$model);
		$view->setPathToTemplateDirectory($this->configurations->get('pathToTemplateDirectory'));
		
		$view->set('pid',$this->configurations->get('pid.'));
		$view->set('maildata',$this->configurations->get('mail.'));
		$view->set('post',$posts[0]);
		$view->set('thread',$threads[0]);
		$view->set('category',$category);
		$view->set('dateFormat',$this->configurations->get('date.format.default'));
	
		$modelFeUsers=new tx_keforum_model_fe_users($this);
		$view->set('user',$modelFeUsers->getUser());
		
		$translator = new $translatorClassName($this, $view);
		$translator->setPathToLanguageFile('EXT:tx_keforum/locallang.xml');
		
		foreach ($recipients as $recipient) {
			$view->set('recipient',$recipient);
			$mailtext=$view->render($template,$this->parameters);
			$mailtext = $translator->translate($mailtext);
			$this->sendMail($mailtext,$recipient['email'],$this->configurations->get($keySubject),$this->configurations->get('mail.'));
		}
    }
	
	
	/*
	 * function sendMail
	 *
	 * send notification email
	 * 
	 * @param $mailtext
	 * @param $recipient
	 * @param $subject
	 * @param $config
	 */
    function sendMail($mailtext,$recipient,$subject,$config){
		
		// Only ASCII is allowed in the header
		$subject = html_entity_decode(t3lib_div::deHSCentities($subject), ENT_QUOTES, $GLOBALS['TSFE']->renderCharset);
		$subject = t3lib_div::encodeHeader($subject, 'base64', $GLOBALS['TSFE']->renderCharset);
	
		// create the plain message body
		$plainMessage = html_entity_decode(strip_tags($mailtext), ENT_QUOTES, $GLOBALS['TSFE']->renderCharset);
		
		$this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
		$this->htmlMail->start();
		
		$this->htmlMail->recipient = $recipient;
		$this->htmlMail->subject = $subject;
		$this->htmlMail->from_email = $config['sender_email'];
		$this->htmlMail->from_name = $config['sender_name'];
		$this->htmlMail->replyto_name = $config['sender_name'];
		$this->htmlMail->organisation = $config['sender_name'];					
		$this->htmlMail->returnPath = $config['sender_email'];
		
		
		$this->htmlMail->theParts['html']['content'] = $mailtext;
		$this->htmlMail->theParts['html']['path'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST') . '/';
		$this->htmlMail->extractMediaLinks();
		$this->htmlMail->extractHyperLinks();
		$this->htmlMail->fetchHTMLMedia();
		$this->htmlMail->substMediaNamesInHTML(0);	// 0 = relative
		$this->htmlMail->substHREFsInHTML();
		$this->htmlMail->setHTML($this->htmlMail->encodeMsg($this->htmlMail->theParts['html']['content']));
		$this->htmlMail->addPlain($plainMessage);
		$out=$this->htmlMail->send($email);
		
		return $out;
    }
    
    function redirect($flag) {
		$pid=$this->configurations->get('pid.');
		$link = new tx_lib_link();
		$link->designator('tx_keforum');
		$link->parameters = array(
			'category' 	=> $this->parameters->get('category'),
			'thread'	=> $this->parameters->get('thread'),
			'noHit'		=> 1,
			'last'		=> 1,
			'flag'		=> $flag,
		);
		$link->destination($pid['posts']);
		$link->redirect();
    }
    
    
    
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/controllers/class.tx_keforum_controller_add_post.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_forum/controllers/class.tx_keforum_controller_add_post.php']);
}

?>