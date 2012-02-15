<?php

require_once(t3lib_extMgm::extPath('libconnect') . 'Resources/Private/Lib/class_EZB.php');

Class Tx_Libconnect_Domain_Repository_EzbRepository extends Tx_Extbase_Persistence_Repository {
	private $ezb_to_t3_subjects = array();
	private $t3_to_ezb_subjects = array();
	
	public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
		$this->subjectRepository = $subjectRepository;
	}
	
	public function loadOverview() {
		$this->loadSubjects();
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		

		$ezb = new EZB();
		$fb = $ezb->getFachbereiche();

		foreach($fb as $el) {
			$subject = $this->ezb_to_t3_subjects[$el['id']];
			$el['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
				'libconnect[subject]' => $subject['uid']
			));
			$list[$el['id']] = $el;
		}
		
		return $list;
	}
	
	private function loadSubjects() {
		$res = $this->subjectRepository->findAll();
		
		foreach($res as $row){			
			$this->ezb_to_t3_subjects[$row->getEzbnotation()]['ezbnotation'] = $row->getEzbnotation();
			$this->ezb_to_t3_subjects[$row->getEzbnotation()]['title'] = $row->getTitle();
			$this->ezb_to_t3_subjects[$row->getEzbnotation()]['uid'] = $row->getUid();
			
			$this->t3_to_ezb_subjects[$row->getUid()]['uid'] = $row->getUid();
			$this->t3_to_ezb_subjects[$row->getUid()]['ezbnotation'] = $row->getEzbnotation();
			$this->t3_to_ezb_subjects[$row->getUid()]['title'] = $row->getTitle();
		}
	}
	
	public function loadList($subject_id, $options =array('options' =>0, 'sc' => 'A', 'lc' => ''), $config) {
		$index = $options['options'];
		$sc = $options['sc'];
		$lc = $options['lc'];
		//$index=0, $sc='A', $lc =''
		
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$this->loadSubjects();
		$subject = $this->t3_to_ezb_subjects[$subject_id];

		$ezb = new EZB();
		$journals = $ezb->getFachbereichJournals($subject['ezbnotation'], $index, $sc, $lc);

		foreach(array_keys($journals['navlist']['pages']) as $page) {
			if (is_array($journals['navlist']['pages'][$page])) {
				$journals['navlist']['pages'][$page]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
					'libconnect[subject]' => $subject['uid'],
					'libconnect[index]' => 0,
				    'libconnect[sc]' => $journals['navlist']['pages'][$page]['sc']? $journals['navlist']['pages'][$page]['sc'] : 'A',
					'libconnect[lc]' => $journals['navlist']['pages'][$page]['lc'],
				));
			}
		}
		if(isset($journals['alphabetical_order']['first_fifty'])){
			foreach(array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
				$journals['alphabetical_order']['first_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
						'libconnect[subject]' => $subject['uid'],
						'libconnect[index]' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
					    'libconnect[sc]' => $journals['alphabetical_order']['first_fifty'][$section]['sc']? $journals['alphabetical_order']['first_fifty'][$section]['sc'] : 'A',
						'libconnect[lc]' => $journals['alphabetical_order']['first_fifty'][$section]['lc'],
				));
			}
		}
		foreach(array_keys($journals['alphabetical_order']['journals']) as $journal) {
			$journals['alphabetical_order']['journals'][$journal]['detail_link'] = $cObject->getTypolink_URL(
					intval($config['detailPid']),
					array(
						'libconnect[jourid]' => $journals['alphabetical_order']['journals'][$journal]['jourid'],
					)
			);
		}
		if(isset($journals['alphabetical_order']['next_fifty'])){
			foreach(array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
				$journals['alphabetical_order']['next_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
						'libconnect[subject]' => $subject['uid'],
						'libconnect[index]' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
					    'libconnect[sc]' => $journals['alphabetical_order']['next_fifty'][$section]['sc']? $journals['alphabetical_order']['next_fifty'][$section]['sc'] : 'A',
						'libconnect[lc]' => $journals['alphabetical_order']['next_fifty'][$section]['lc'],
				));
			}
		}

		return $journals;
	}
	
	public function loadDetail($journal_id) {
		$cObject = t3lib_div::makeInstance('tslib_cObj');
		$ezb = new EZB();
		$journal = $ezb->getJournalDetail($journal_id);

		if (! journal ){
			return false;
		}
	
		return $journal; 
	}
}
?>