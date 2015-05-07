<?php
/***************************************************************
* Copyright notice
*
* (c) 2009 by Avonis - New Media Agency
*
* All rights reserved
*
* This script is part of the EZB/DBIS-Extention project. The EZB/DBIS-Extention project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
* Project sponsored by:
*  Avonis - New Media Agency - http://www.avonis.com/
***************************************************************/

/**
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
 
require_once(t3lib_extMgm::extPath('libconnect') . 'Classes/UserFunctions/IsfirstPlugInUserFunction.php');

class Tx_Libconnect_Controller_EzbController extends Tx_Extbase_MVC_Controller_ActionController {

     /**
     * shows a list of journals (for general, search, choosed subject)
     */
    public function displayListAction() {
        $params = t3lib_div::_GET('libconnect');
        $cObject = t3lib_div::makeInstance('tslib_cObj');

        //get PageID
        $Pid = intval($GLOBALS['TSFE']->id);

        //include CSS
        $this->decideIncludeCSS();

        if ((!empty($params['subject'])) || (!empty($params['notation']))) {//choosed subject after start point
            $config['detailPid'] = $this->settings['flexform']['detailPid'];
            
            $options['index'] = $params['index'];
            $options['sc'] = $params['sc'];
            $options['lc'] = $params['lc'];
            $options['notation'] = $params['notation'];
            $options['colors'] = $params['colors'];
            
            //it´s for there is not NULL in the request or there will be a problem
            if(!isset($params['subject'])){
                $params['subject'] = "";
            }
            $liste =  $this->ezbRepository->loadList(
                $params['subject'], 
                $options,
                $config
            );

            $listURL = $cObject->getTypolink_URL( $Pid );
            
            $formParameter = array(
                'libconnect[subject]' => $params['subject'],
                'libconnect[index]' => $params['index'],
                'libconnect[sc]' => $params['sc'],
                'libconnect[lc]' => $params['lc'],
                'libconnect[notation]' => $params['notation']
            );
            
            //variables for template
            $this->view->assign('journals', $liste);
            $this->view->assign('listUrl', $listURL);
            $this->view->assign('colors', $params['colors']);
            $this->view->assign('formParameter', $formParameter);

        } else if (!empty($params['search'])) {//search results
            $config['detailPid'] = $this->settings['flexform']['detailPid'];
            
            $journals = array();
            
            if(!empty($params['colors'])){
                unset($params['search']['selected_colors']);
                foreach($params['colors'] as  $color){
                    $params['search']['selected_colors'][] = $color;
                }
                $journals['colors'] = $params['colors'];
            } else {
                $journals['colors'] = array(
                    1 => 1,
                    2 => 2,
                    4 => 4,
                    6 => 6
                );
            }
            
            $journals =  $this->ezbRepository->loadSearch($params['search'], $journals['colors'], $config);
            
            if(!empty($params['colors'])){
                $journals['colors'] = $params['colors'];
            } else {
                $journals['colors'] = array(
                    1 => 1,
                    2 => 2,
                    4 => 4,
                    6 => 6
                );
            }
            
            //change view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displaySearch');
            $this->view->setControllerContext($controllerContext);

            //variables for template
            $this->view->assign('journals', $journals);
            $this->view->assign('listUrl', $listURL);
            $this->view->assign('colors', $params['colors']);
            $this->view->assign('formParameter', $params['search']);
        } else {//start point
            $liste =  $this->ezbRepository->loadOverview();
            
            //change view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displayOverview');
            $this->view->setControllerContext($controllerContext);
            
            //variables for template
            $this->view->assign('list', $liste);
        }
    }

    /**
     * creates instance of EzbRepository
     */
    public function injectEzbRepository(Tx_Libconnect_Domain_Repository_EzbRepository $ezbRepository){
        $this->ezbRepository = $ezbRepository;
    }
    
    /**
     * creates instance of SubjectRepository
     */
    public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
        $this->subjectRepository = $subjectRepository;
    }
    
    /**
     * shows details
     */
    public function displayDetailAction() {
        $params = t3lib_div::_GET('libconnect');
        $config['participantsPid'] = $this->settings['flexform']['participantsPid'];

        //include CSS
        $this->decideIncludeCSS();

        //$this->set('bibid', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid']);
        if (!($params['jourid'])){
            $this->view->assign('error', 'Error');
            //return "<strong>Fehler: Es wurde keine Zeitschrift mit der angegeben URL gefunden.</strong>";
            return;
        }
        
        //$this->ezbRepository->setLongAccessInfos($this->ezblongaccessinfos->de);

        $journal =  $this->ezbRepository->loadDetail($params['jourid'], $config);
        
    //BOF ZDB LocationData
        //check if locationData is enabled
        if($this->settings['enableLocationData'] == 1) {
            $locationData = $this->ezbRepository->loadLocationData($journal);
            
            if($locationData) {
                $journal['locationData'] = $locationData;
            }

        }
    //EOF ZDB LocationData
        
        //variables for template
        $this->view->assign('journal', $journal);
        $this->view->assign('bibid', $this->ezbRepository->getBibid());
    }
    
    /**
     * zeigt die Sidebar
     */
    public function displayMiniFormAction() {
        $params = t3lib_div::_GET('libconnect');
        
        //include CSS
        $this->decideIncludeCSS();
        
        $cObject = t3lib_div::makeInstance('tslib_cObj');
        
        $form = $this->ezbRepository->loadMiniForm();
        
        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//current URL
        $this->view->assign('searchUrl', $cObject->getTypolink_URL($this->settings['flexform']['searchPid']));//link to search
        $this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//link to search results
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list
        
        //if subject is choosed link  to subject list is displayed
        if ((!empty($params['subject'])) || (!empty($params['notation']))) {
            $this->view->assign('showSubjectLink', true);
            
            //if new activated should here the new for subject be active
            if(!empty($this->settings['flexform']['newPid'])){
                    
                $cObject = t3lib_div::makeInstance('tslib_cObj');
                
                if(!empty($params['subject'])){
                    $count = (int) $this->getNewCount($params['subject']);
                    
                    if($count >0){
                        $this->view->assign('newInSubjectCount',  $count);

                        $this->view->assign('newUrlSub', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid']), 
                            array('libconnect' => array('subject' => $params['subject'] )) ) );//URL of new list
                    }
                }
            }

        }elseif(!empty($this->settings['flexform']['newPid'])){
            $count = (int) $this->getNewCount(FALSE);
            
            //show "new in EZB" only if there is something new
            if($count >0){
                $this->view->assign('newUrl', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
                $this->view->assign('newInSubjectCount',  $count);
            }
        }
    }
    
    /**
     * zeigt die Suche
     */
    public function displayFormAction() {
        $params = t3lib_div::_GET('libconnect');
        
        //include CSS
        $this->decideIncludeCSS();
        
        $cObject = t3lib_div::makeInstance('tslib_cObj');
        $form =  $this->ezbRepository->loadForm();
        
        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//current URL
        $this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//url to search
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list view
    }
    
    /**
     * shows list of new entries
     */
    public function displayNewAction() {
        $params = t3lib_div::_GET('libconnect');
        $params['jq_type1'] = 'ID';
        $params['sc'] = $params['search']['sc'];
        if(!empty($params['subject'])){
            $subject = $this->ezbRepository->getSubject($params['subject']);
            $params['Notations']=array($subject['ezbnotation']);
        }
        unset($params['subject']);
        unset($params['search']);
        
        //include CSS
        $this->decideIncludeCSS();
        
        date_default_timezone_set('GMT+1');//@todo get the information from system
        
        $oneDay = 86400;//seconds
        $numDays = 7; //default are 7 days
        $today = strtotime('now');
  
        if(!empty($this->settings['flexform']['countDays'])){
            $numDays = $this->settings['flexform']['countDays'];
        }
        
        //calcaulate date
        $date = date("d-m-Y",$today-($numDays * $oneDay));
        $params['jq_term1'] = $date;//Datum bis wann Eintrag neu
        
        $config['detailPid'] = $this->settings['flexform']['detailPid'];
        
        //request
        $journals =  $this->ezbRepository->loadSearch($params, false, $config);
        
        //variables for template
        $this->view->assign('journals', $journals);
        $this->view->assign('new_date', date("d.m.Y",$today-($numDays * $oneDay)));
        $this->view->assign('subject', $subject['title']);
    }
    
    /**
     * count the new entries
     */
    public function getNewCount($subjectId = FALSE) {
        $params = t3lib_div::_GET('libconnect');
        $params['jq_type1'] = 'ID';
        $params['sc'] = $params['search']['sc'];
        
        if($subjectId != FALSE){
            $subject = $this->ezbRepository->getSubject($subjectId);
            $params['Notations']=array($subject['ezbnotation']);
        }
        unset($params['subject']);
        unset($params['search']);
        
        date_default_timezone_set('GMT+1');//@todo get the information from system
        
        $oneDay = 86400;//seconds
        $numDays = 7; //default are 7 days
        $today = strtotime('now');
  
        if(!empty($this->settings['flexform']['countDays'])){
            $numDays = $this->settings['flexform']['countDays'];
        }
        
        //calcaulate date
        $date = date("d-m-Y",$today-($numDays * $oneDay));
        $params['jq_term1'] = $date;//Datum bis wann Eintrag neu
        
        $config['detailPid'] = $this->settings['flexform']['detailPid'];
        
        //request
        $journals =  $this->ezbRepository->loadSearch($params, false, $config);

        return $journals['page_vars']['search_count'];
    }
        
    public function displayParticipantsFormAction() {
        $params = t3lib_div::_GET('libconnect');
        //include CSS
        $this->decideIncludeCSS();
        //include js
        $this->response->addAdditionalHeaderData('<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Js/ezb.js" ></script>');    
        
        $ParticipantsList =  $this->ezbRepository->getParticipantsList($params['jourid']);

        $config['partnerPid'] = 0;
        $journal =  $this->ezbRepository->loadDetail($params['jourid'], $config);
        $titel = $journal['title'];
        unset($journal);
        
        //variables for template
        $this->view->assign('ParticipantsList', $ParticipantsList);
        $this->view->assign('jourid', $params['jourid']);
        $this->view->assign('titel', $titel);
    }
    
    /**
     * get contact information
     */
    public function displayContactAction() {
        $contact =  $this->ezbRepository->getContact();
        $this->view->assign('contact', $contact);
    }

    /**
     * check if css file is need and includes it
     */
    private function decideIncludeCSS(){
        //if user don´t want to use our css
        $noCSS = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezbNoCSS'];

        if($noCSS == 1){
            return;
        }

        //get UID of PlugIn
        $this->contentObj = $this->configurationManager->getContentObject();
        $uid = $this->contentObj->data['uid'];
        unset($this->contentObj);

        //only the first PlugIn needs to include the css
        if(IsfirstPlugInUserFunction('ezb', $uid)){
            $this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/ezb.css" />');    
        }
    }
}
?>