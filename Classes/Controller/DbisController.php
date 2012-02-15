<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Torsten Witt <witt@sub.uni-hamburg.de>, Stabi Hamburg
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Libconnect_Controller_DbisController extends Tx_Extbase_MVC_Controller_ActionController {
	
	public function displayListAction() {
		 $params = t3lib_div::_GET('libconnect');
		 
		if (!empty($params['subject'])) {//Gewaehltes Fach nach Einstiegspunkt
			$config['sort'] = $this->settings['flexform']['sortParameter'];
			$config['detailPid'] = $this->settings['flexform']['detailPid'];
			
			$liste =  $this->dbisRepository->loadList($params['subject'], $config);
			
			
			//var_dump($liste['list']['access_infos']);
			$this->view->assign('subject', $liste['subject']);
			$this->view->assign('list', $liste['list']);
			//$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('extkey') . 'Resources/Public/Styles/dbis.css" />');

		} else if (!empty($params['search'])) {//Suchergebnisse
			/*$model->loadSearch();
			$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
			$view->setTemplatePath($this->configurations->get('templatePath'));
			$output = $view->render("dbis_search.tpl");*/

		} else {//Einstiegspunkt
			$liste =  $this->dbisRepository->loadOverview();
			
			//andere View verwenden
			$controllerContext = $this->buildControllerContext();
			$controllerContext->getRequest()->setControllerActionName('displayOverview');
			$this->view->setControllerContext($controllerContext);
			
			$this->view->assign('list', $liste);
			//$this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('extkey') . 'Resources/Public/Styles/dbis.css" />');
		}
    }
	
	public function injectDbisRepository(Tx_Libconnect_Domain_Repository_DbisRepository $dbisRepository){
		$this->dbisRepository = $dbisRepository;
	}
	
	public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
		$this->subjectRepository = $subjectRepository;
	}
	
	public function displayDetailAction() {
		$params = t3lib_div::_GET('libconnect');

		if (!($params['titleid'])){
			$this->view->assign('error', 'Error');
			return;
		}
		$liste =  $this->dbisRepository->loadDetail($params['titleid']);
		
		if(!$liste){
			$this->view->assign('error', 'Error');
			
		}else{
			//BG> Hide start research link for internal access only items
			if($liste['access_id']!='access_4'){
				$liste['access_workaround']=$liste['access_id'];
			}
		
			$this->view->assign('db', $liste);
		}
		
		//if (! $this->parameters->get('titleid')) {
//			$this->flashMessageContainer->add();
	//		$this->pushFlashMessage("<strong>Error in DBIS displayDetailAction: No title id</strong>");
			 
		//}

		/*$model = $this->makeInstance('tx_libconnect_models_dbis');
		$model->loadDetail(intval($this->parameters->get('titleid')));*/

		
		
		
    }
	
	public function displayMiniFormAction() {
    	 $this->view->assign('name', 'Stefan Frömken');
    }
	
	public function displayFormAction() {
    	 $this->view->assign('name', 'Stefan Frömken');
    }

}
?>