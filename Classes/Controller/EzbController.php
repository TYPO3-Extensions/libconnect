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
class Tx_Libconnect_Controller_EzbController extends Tx_Extbase_MVC_Controller_ActionController {

	public function displayListAction() {	
		$params = t3lib_div::_GET('libconnect');
	
		
		if (!empty($params['subject'])) {//Gewaehltes Fach nach Einstiegspunkt
			$config['detailPid'] = $this->settings['flexform']['detailPid'];
			
			$options['index'] = $params['index'];
			$options['sc'] = $params['sc'];
			$options['lc'] = $params['lc'];
			
			$liste =  $this->ezbRepository->loadList(
				$params['subject'], 
				$options,
				$config
			);
			
			$this->view->assign('journals', $liste);
				
		} else if (!empty($params['search'])) {//Suchergebnisse
			/*$liste =  $this->ezbRepository->loadSearch();
			
			$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
			$view->setTemplatePath($this->configurations->get('templatePath'));
			$output = $view->render("ezb_search.tpl");*/

		} else {
			$liste =  $this->ezbRepository->loadOverview();
			
			//andere View verwenden
			$controllerContext = $this->buildControllerContext();
			$controllerContext->getRequest()->setControllerActionName('displayOverview');
			$this->view->setControllerContext($controllerContext);
			
			$this->view->assign('list', $liste);
		}
	}
	
	public function injectEzbRepository(Tx_Libconnect_Domain_Repository_EzbRepository $ezbRepository){
		$this->ezbRepository = $ezbRepository;
	}
	
	public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
		$this->subjectRepository = $subjectRepository;
	}
	
	public function displayDetailAction() {
		$params = t3lib_div::_GET('libconnect');
		
		//$this->set('bibid', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid']);
		if (!($params['jourid'])){
			$this->view->assign('error', 'Error');
			//return "<strong>Fehler: Es wurde keine Zeitschrift mit der angegeben URL gefunden.</strong>";
			return;
		}

		$journal =  $this->ezbRepository->loadDetail($params['jourid']);
		$this->view->assign('journal', $journal);
		$this->view->assign('bibid', 'bibid');
	}
	
	public function displayMiniFormAction() {	
		echo "miniForm";
	}
	
	public function displayFormAction() {	
		echo "Form";
	}
}
?>