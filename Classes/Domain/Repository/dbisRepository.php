<?php

require_once(t3lib_extMgm::extPath('libconnect') . 'Resources/Private/Lib/class_DBIS.php');

Class Tx_Libconnect_Domain_Repository_DbisRepository extends Tx_Extbase_Persistence_Repository {
	private $dbis_to_t3_subjects = array();
	private $t3_to_dbis_subjects = array();
	
	public function loadList($subject_id, $config) {
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		
		$this->loadSubjects();

		$dbis = new DBIS();
		$subject = $this->t3_to_dbis_subjects[$subject_id];

		$dbis_id = $subject['dbisid'];
		//$sort = $this->controller->configurations->get('sortParameter');
		
	
		$result = $dbis->getDbliste($dbis_id, $config['sort']);
		
		

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
//var_dump($result['list']);
		// sort groups by name
		$alph_sort_groups = array();
		foreach ($result['list']['groups'] as $group) {
			$alph_sort_groups[$group['title']] = $group;
		}
		ksort($alph_sort_groups);
		$result['list']['groups'] = $alph_sort_groups;
		
		return array('subject' => $subject['title'], 'list' => $result['list']);
		
		/*$this->set('subject', $subject['title']);
		$this->set('list', $result['list']);*/
		
		 //$this->view->assign('hallo', 'Halli Hallo');
	}
	
	public function loadOverview() {
		$this->loadSubjects();
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		
		
		$dbis = new DBIS();
		$list = $dbis->getFachliste();

		foreach($list as $el) {
			$subject = $this->dbis_to_t3_subjects[$el['id']];
			$el['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
				'libconnect[subject]' => $subject['uid'])
			);
			
			$list[$el['id']] = $el;
		}
		
		return $list;
	}
	
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
	
	public function loadDetail($title_id) {
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$dbis = new DBIS();
		$db = $dbis->getDbDetails($title_id);
		
		if (! $db ){
			return false;
		}
		
		return $db;
	}
	
	public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
		$this->subjectRepository = $subjectRepository;
	}

}
?>