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

Class Tx_Libconnect_Domain_Repository_DbisRepository extends Tx_Extbase_Persistence_Repository {
    private $dbis_to_t3_subjects = array();
    private $t3_to_dbis_subjects = array();
    
    /**
     * shows top databases
     * 
     * @param array $config
     * @return array
     */
    public function loadTop($config) {
        $cObject = t3lib_div::makeInstance('tslib_cObj');

        $this->loadSubjects();
        $subject = $this->t3_to_dbis_subjects[$config['subject']];
        $dbis_id = $subject['dbisid'];

        $dbis = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_dbis');
        $result = $dbis->getDbliste($dbis_id);

        foreach(array_keys($result['list']['top']) as $db) {
            $result['list']['top'][$db]['detail_link'] = $cObject->getTypolink_URL(
                intval($config['detailPid']),
                array(
                    'libconnect[titleid]' => $result['list']['top'][$db]['id'],
                )
            );
        }

        return $result['list']['top'];
    }
    
    /**
     * shows a list of databases(for start, search and list of chose subject)
     * 
     * @param integer $subject_id
     * @param array array('subject'=>array(), 'list'=>array())
     */
    public function loadList($subject_id, $config) {
        $cObject = t3lib_div::makeInstance('tslib_cObj');

        $this->loadSubjects();

        $dbis = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_dbis');
        
        if(is_numeric($subject_id)){
            $subject = $this->t3_to_dbis_subjects[$subject_id];

            $dbis_id = $subject['dbisid'];
    
            $result = $dbis->getDbliste($dbis_id, $config['sort']);
        }else{//for own collection
            $result = $dbis->getDbliste($subject_id, $config['sort']);
            
            $subject['title'] = $result['headline'];
        }
        
        foreach(array_keys($result['list']['top']) as $db) {
            $result['list']['top'][$db]['detail_link'] = $cObject->getTypolink_URL(
                intval($config['detailPid']),
                array(
                    'libconnect[titleid]' => $result['list']['top'][$db]['id'],
                )
            );
        }
        foreach(array_keys($result['list']['groups']) as $group) {
            foreach(array_keys($result['list']['groups'][$group]['dbs']) as $db) {
                $result['list']['groups'][$group]['dbs'][$db]['detail_link'] = $cObject->getTypolink_URL(
                    intval($config['detailPid']),
                    array(
                        'libconnect[titleid]' => $result['list']['groups'][$group]['dbs'][$db]['id'],
                    )
                );
            }
        }

        // sort groups by name
        $alph_sort_groups = array();
        foreach ($result['list']['groups'] as $group) {
            $alph_sort_groups[$group['title']] = $group;
        }
        ksort($alph_sort_groups, SORT_STRING); //added sort-flag SORT_STRING for correct sorting of alphabetical listings
        $result['list']['groups'] = $alph_sort_groups;
        
        return array('subject' => $subject['title'], 'list' => $result['list']);
    }
    
    /**
     * show start
     * 
     * @return array $list
     */
    public function loadOverview() {
        $this->loadSubjects();
        $cObject = t3lib_div::makeInstance('tslib_cObj');

        $dbis = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_dbis');

        $list = $dbis->getFachliste();

        foreach($list as $el) {
        
            if($el['lett'] != "c"){
                //id aus Datenbank holen
                $subject = $this->dbis_to_t3_subjects[$el['id']];
                
                $el['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
                    'libconnect[subject]' => $subject['uid'])
                );
            }else{
                $el['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
                    'libconnect[subject]' => $el['id'])
                );
            }
            
            $list[$el['id']] = $el;
        }
        
        return $list;
    }

    /**
     * load subjects from datahase
     * 
     */
    private function loadSubjects() {
        $res = $this->subjectRepository->findAll();
        
        foreach($res as $row){        

            $this->dbis_to_t3_subjects[$row->getDbisId()]['dbisid'] = $row->getDbisId();
            $this->dbis_to_t3_subjects[$row->getDbisId()]['title'] = $row->getTitle();
            $this->dbis_to_t3_subjects[$row->getDbisId()]['uid'] = $row->getUid();
            
            $this->t3_to_dbis_subjects[$row->getUid()]['uid'] = $row->getUid();
            $this->t3_to_dbis_subjects[$row->getUid()]['dbisid'] = $row->getDbisId();
            $this->t3_to_dbis_subjects[$row->getUid()]['title'] = $row->getTitle();
        }
    }
    
    /**
     * show details
     * 
     * @param integer $title_id
     * 
     * @return array $db
     */
    public function loadDetail($title_id) {
        $cObject = t3lib_div::makeInstance('tslib_cObj');
        $dbis = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_dbis');
        $db = $dbis->getDbDetails($title_id);
        
        if (! $db ){
            return FALSE;
        }
        
        return $db;
    }
    
    /**
     * execute search
     * 
     * @param array $searchVars
     * @param array $config
     * 
     * @return array $result
     */
    public function loadSearch($searchVars, $config) {
        $cObject = t3lib_div::makeInstance('tslib_cObj');
        $this->loadSubjects();

        $term = $searchVars['sword'];//wird bei MiniForm verwendet
        unset($searchVars['sword']);

        //execute search
        $dbis = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_dbis');
        $result = $dbis->search($term, $searchVars);
        
        foreach(array_keys($result['list']['top']) as $db) {
            $result['list']['top'][$db]['detail_link'] = $cObject->getTypolink_URL(
                intval($config['detailPid']),
                array(
                    'libconnect[titleid]' => $result['list']['top'][$db]['id'],
                )
            );
        }

        foreach(array_keys($result['list']['values']) as $value) {
            $result['list']['values'][$value]['detail_link'] = $cObject->getTypolink_URL(
                intval($config['detailPid']),
                array(
                    'libconnect[titleid]' => $result['list']['values'][$value]['id'],
                )
            );
        }

        return $result['list'];
    }
    
     /**
     * return miniform
     *
     * @return array
     */
    public function loadMiniForm() {
        $dbis = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_dbis');
        $form = $dbis->detailSucheFormFelder();

        return $form;
    }

    /**
     * return form for detail search
     *
     * @return array
     */
    public function loadForm() {
        $dbis = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_dbis');
        $form = $dbis->detailSucheFormFelder();
        
        return $form;
    }
    
    /**
     * creates instance of SubjectRepository (for TYPO3 4.7)
     */
    public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
        $this->subjectRepository = $subjectRepository;
    }
    
    /**
     * returns a subject
     * 
     * @param integer $subjectId Id of subject
     */
    public function getSubject($subjectId){
        $this->loadSubjects();
        
        return $this->t3_to_dbis_subjects[$subjectId];
    }
}
?>