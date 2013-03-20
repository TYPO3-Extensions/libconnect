<?php

/**
 * Doku: http://www.bibliothek.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * Doku: http://rzblx1.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * @author niklas guenther
 * @author Torsten Witt
 *
 */

require_once(t3lib_extMgm::extPath('libconnect') . 'Resources/Private/Lib/class_XMLPageConnection.php');

class EZB {

    // document search meta infos
    private $title;
    private $author_firstname;
    private $author_lastname;
    private $genre; // journal / article
    private $isbn;
    private $issn;
    private $eissn;
    private $date; // YYYY-MM-DD YYYY-MM YYYY
    
    // general config
    private $overview_requst_url = 'http://rzblx1.uni-regensburg.de/ezeit/fl.phtml?xmloutput=1&';
    private $detailview_request_url = 'http://rzblx1.uni-regensburg.de/ezeit/detail.phtml?xmloutput=1&';
    private $search_url = 'http://rzblx1.uni-regensburg.de/ezeit/search.phtml?xmloutput=1&';
    //private $journal_link_url = "http://rzblx1.uni-regensburg.de/ezeit/warpto.phtml?bibid=SUBHH&colors=7&lang=de&jour_id=";
    private $search_result_page = "http://rzblx1.uni-regensburg.de/ezeit/searchres.phtml?&xmloutput=1&";
    //private $search_result_page = "http://rzblx1.uni-regensburg.de/ezeit/searchres.phtml?&xmloutput=1&bibid=SUBHH&colors=7&lang=de&";
    //private $search_result_page = "http://ezb.uni-regensburg.de/searchres.phtml?xmloutput=1&bibid=SUBHH&colors=7&lang=de";
    private $participants_url = "http://rzblx1.uni-regensburg.de/ezeit/where.phtml?";
    private $participants_xml_url = "http://rzblx1.uni-regensburg.de/ezeit/where.phtml?&xmloutput=1&";
    //private $contact_url = "http://rzblx1.uni-regensburg.de/ezeit/kontakt.phtml?&xmloutput=1&";
    

    private $lang = 'de';
    private $colors = 7;
    
    // Fachbereich Journals
    public $notation;
    public $sc;
    public $lc;
    public $sindex;
    
    // typoscript Konfigurationsvariablen
    private $bibID;
    
    // XML Daten
    private $XMLPageConnection;

    /**
     * Konstruktor
     *
     */
    public function __construct() {
	
		$this->XMLPageConnection = new XMLPageConnection();
		EZB::setBibID();
    }
    
    /**
     * Funktion setzt die EZB Bibliothek ID Klassenvariable
     *
     */
    private function setBibID() {
		$this->bibID = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
    }
    
    /**
     * Fachbereiche laden
     *
     * @return array()
     */
    public function getFachbereiche() {
		
		$fachbereiche = array();
		$url = "{$this->overview_requst_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&";
		$xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

		foreach ($xml_request->ezb_subject_list->subject AS $key => $value) {
			$fachbereiche[(string) $value['notation'][0]] = array('title' => (string) $value[0], 'journalcount' => (int) $value['journalcount'], 'id' => (string) $value['notation'][0], 'notation' => (string) $value['notation'][0]);
		}

		return $fachbereiche;
    }

    /**
     * Alle Journals eines Fachbereichs laden
     *
     * @param string $jounal
     * @param string $letter
     * @param string $lc
     * @param $sindex int
     *
     * @return array()
     */
    public function getFachbereichJournals($jounal, $sindex = 0, $sc = 'A', $lc = '') {
		
		$journals = array();
		$url = "{$this->overview_requst_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&notation={$jounal}&sc={$sc}&lc={$lc}&sindex={$sindex}&";
		$xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

		if ($xml_request->page_vars) {
			$this->notation = (string) $xml_request->page_vars->notation->attributes()->value;
			$this->sc = (string) $xml_request->page_vars->sc->attributes()->value;
			$this->lc = (string) $xml_request->page_vars->lc->attributes()->value;
			$this->sindex = (string) $xml_request->page_vars->sindex->attributes()->value;
		}

		if ($xml_request->ezb_alphabetical_list) {

			$journals['subject'] = (string) $xml_request->ezb_alphabetical_list->subject;
			$journals['navlist']['current_page'] = (string) $xml_request->ezb_alphabetical_list->navlist->current_page;
			$journals['navlist']['current_title'] = (string) $xml_request->ezb_alphabetical_list->current_title;

			foreach ($xml_request->ezb_alphabetical_list->navlist->other_pages AS $key2 => $value2) {
				foreach ($value2->attributes() AS $key3 => $value3) {
					$journals['navlist']['pages'][(string) $value2[0]][(string) $key3] = (string) $value3;
				}
				// set title
				$journals['navlist']['pages'][(string) $value2[0]]['title'] = (string) $value2[0];
			}
		}
		$journals['navlist']['pages'][$journals['navlist']['current_page']] = $journals['navlist']['current_page'];
		ksort($journals['navlist']['pages']);

		foreach ($xml_request->ezb_alphabetical_list->alphabetical_order->journals->journal AS $key => $value) {
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['title'] = (string) $value->title;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['jourid'] = (int) $value->attributes()->jourid;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color_code'] = (int) $value->journal_color->attributes()->color_code;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color'] = (string) $value->journal_color->attributes()->color;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['detail_link'] = '';
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
		}
		$i = 0;

		foreach ($xml_request->ezb_alphabetical_list->next_fifty AS $key => $value) {
			$journals['alphabetical_order']['next_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$journals['alphabetical_order']['next_fifty'][$i]['lc'] = (string) $value->attributes()->lc;
			$journals['alphabetical_order']['next_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$journals['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string) $value->next_fifty_titles;
			$i++;
		}

		$i = 0;

		foreach ($xml_request->ezb_alphabetical_list->first_fifty AS $key => $value) {
			$journals['alphabetical_order']['first_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$journals['alphabetical_order']['first_fifty'][$i]['lc'] = (string) $value->attributes()->lc;
			$journals['alphabetical_order']['first_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$journals['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string) $value->first_fifty_titles;
			$i++;
		}

		return $journals;
    }

    /**
     * Details zu einem Journal laden
     *
     * @param journalId int
     *
     * @return string
     */
    public function getJournalDetail($journalId) {
	
		$journal = array();
		$url = "{$this->detailview_request_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&jour_id={$journalId}";
		$xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

		if (!is_object($xml_request->ezb_detail_about_journal->journal)) {
			return false;
		}

		$journal['id'] = (int) $xml_request->ezb_detail_about_journal->journal->attributes()->jourid;
		$journal['title'] = (string) $xml_request->ezb_detail_about_journal->journal->title;
		$journal['color'] = (string) $xml_request->ezb_detail_about_journal->journal->journal_color->attributes()->color;
		$journal['color_code'] = (int) $xml_request->ezb_detail_about_journal->journal->journal_color->attributes()->color_code;
		$journal['publisher'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->publisher;
		$journal['ZDB_number'] = (string) @$xml_request->ezb_detail_about_journal->journal->detail->ZDB_number;
		$journal['ZDB_number_link'] = (string) @$xml_request->ezb_detail_about_journal->journal->detail->ZDB_number->attributes()->url;
		$journal['subjects'] = array();
		if (isset($xml_request->ezb_detail_about_journal->journal->detail->subjects->subject)) {
			foreach ($xml_request->ezb_detail_about_journal->journal->detail->subjects->subject as $subject) {
				$journal['subjects'][] = (string) $subject;
			}
		}
		$journal['subjects_join'] = join(', ', $journal['subjects']);
		$journal['pissns'] = array();
		if (isset($xml_request->ezb_detail_about_journal->journal->detail->P_ISSNs->P_ISSN)) {
			foreach ($xml_request->ezb_detail_about_journal->journal->detail->P_ISSNs->P_ISSN as $pissn) {
				$journal['pissns'][] = (string) $pissn;
			}
		}
		$journal['pissns_join'] = join(', ', $journal['pissns']);
		$journal['eissns'] = array();
		if (isset($xml_request->ezb_detail_about_journal->journal->detail->E_ISSNs->E_ISSN)) {
			foreach ($xml_request->ezb_detail_about_journal->journal->detail->E_ISSNs->E_ISSN as $eissn) {
				$journal['eissns'][] = (string) $eissn;
			}
		}
		$journal['eissns_join'] = join(', ', $journal['eissns']);
		$journal['keywords'] = array();
		if (isset($xml_request->ezb_detail_about_journal->journal->detail->keywords->keyword)) {
			foreach ($xml_request->ezb_detail_about_journal->journal->detail->keywords->keyword as $keyword) {
				$journal['keywords'][] = (string) $keyword;
			}
		}
		$journal['keywords_join'] = join(', ', $journal['keywords']);
		$journal['fulltext'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->fulltext;

		if (isset($xml_request->ezb_detail_about_journal->journal->detail->fulltext)) {
			$i = 1;
			$warpto = urlencode((string) $xml_request->ezb_detail_about_journal->journal->detail->fulltext->attributes()->url);
			$journal['fulltext_link'] = 'http%3A%2F%2Frzblx1.uni-regensburg.de%2Fezeit%2Fwarpto.phtml?bibid=' . $this->bibID . '&colors=' . $this->colors . '&lang=' . $this->lang . '&jour_id=' . $journalId . '&url=' . $warpto;
			//$journal['fulltext_link'] = str_replace('http%3A%2F%2F', 'http%3A%2F%2Frzblx1.uni-regensburg.de%2Fezeit%2Fwarpto.phtml?bibid='.$bibid.'&colors='.$this->colors.'&lang='.$this->lang.'&jour_id='.$journalId.'&url=http%3A%2F%2F', $warpto, $i);
		}

		$journal['homepages'] = array();
		if (isset($xml_request->ezb_detail_about_journal->journal->detail->homepages->homepage)) {
			foreach ($xml_request->ezb_detail_about_journal->journal->detail->homepages->homepage as $homepage) {
				$journal['homepages'][] = (string) $homepage;
			}
		}
		$journal['first_fulltext'] = array(
			'volume' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_volume,
			'issue' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_issue,
			'date' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_date
		);
		if ($xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue) {
			$journal['last_fulltext'] = array(
			'volume' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_volume,
			'issue' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_issue,
			'date' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_date
			);
		}
		$jounral['moving_wall'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->moving_wall;
		$journal['appearence'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->appearence;
		$journal['costs'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->costs;
		$journal['remarks'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->remarks;

		// generate link to institutions having access to this journal
		$participants_xml_request = $this->XMLPageConnection->getDataFromXMLPage("{$this->participants_xml_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&jour_id={$journalId}");
		if (isset($participants_xml_request->ezb_where_journal_at_partners->partner_selection->institutions->institution)) {
		    if ($participants_xml_request->ezb_where_journal_at_partners->partner_selection->institutions->institution->count() > 0) {
                $journal['participants'] = "{$this->participants_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&jour_id={$journalId}";
            }
		}
		
		// periods

		$color_map = array(
			'green' => 1,
			'yellow' => 2,
			'red' => 4,
			'yellow_red' => 6
		);
		$journal['periods'] = array();
		if (isset($xml_request->ezb_detail_about_journal->journal->periods->period)) {
			foreach ($xml_request->ezb_detail_about_journal->journal->periods->period as $period) {
				$i = 1;
				$warpto = "";
				if (@$period->warpto_link->attributes()->url) {
					$warpto = urlencode((string) $period->warpto_link->attributes()->url);
				}
				$journal['periods'][] = array(
					'label' => (string) $period->label,
					'color' => (string) @$period->journal_color->attributes()->color,
					'color_code' => $color_map[(string) @$period->journal_color->attributes()->color],
					//'link' => (string) $period->warpto_link->attributes()->url //alt und fehlerhaft
					'link' => 'http%3A%2F%2Frzblx1.uni-regensburg.de%2Fezeit%2Fwarpto.phtml?bibid=' . $this->bibID . '&colors=' . $this->colors . '&lang=' . $this->lang . '&jour_id=' . $journalId . '&url=' . $warpto,
					//'link' => str_replace('http%3A%2F%2F', 'http%3A%2F%2Frzblx1.uni-regensburg.de%2Fezeit%2Fwarpto.phtml?bibid='.$bibid.'&colors='.$this->colors.'&lang='.$this->lang.'&jour_id='.$journalId.'&url=http%3A%2F%2F', $warpto, $i
					'readme' => (string) @$period->readme_link->attributes()->url
				);
			}
		}

		return $journal;
    }

    /**
     * Detailsuche Formular ausgeben
     *
     * @return array
     */
    public function detailSearchFormFields() {
	
		$xml_such_form = $this->XMLPageConnection->getDataFromXMLPage((string) $this->search_url);

		foreach ($xml_such_form->ezb_search->option_list AS $key => $value) {
			foreach ($value->option AS $key2 => $value2) {
				$form[(string) $value->attributes()->name][(string) $value2->attributes()->value] = (string) $value2;
			}
		}

		// fehlenden Eintrag ergaenzen
		$form['selected_colors'][2] = 'im Campus-Netz zugänglich';

		// schlagwort und issn tauschen...
		$form['jq_type'] = array(
			'KT' => 'Titelwort(e)',
			'KS' => 'Titelanfang',
			'IS' => 'ISSN',
			'PU' => 'Verlag',
			'KW' => 'Schlagwort(e)',
			'ID' => 'Eingabedatum',
			'LC' => 'Letzte Änderung',
			'ZD' => 'ZDB-Nummer',
		);

		return $form;
    }

    /**
     * Suchurl erzeugen
     *
     * @param term string
     * @param searchVars array
     *
     * @return string
     */
    private function createSearchUrl($term, $searchVars/* , $lett = 'k' */) {
	
		$searchUrl = $this->search_result_page . 'bibid=' . $this->bibID . '&colors=' . $this->colors . '&lang=' . $this->lang;
		
		// urlencode termi
		$term = rawurlencode(utf8_decode($term));
		
		//Bei Suche mittels Sidebar
		if (strlen($term)) {
			$searchUrl .= "&jq_type1=KT&jq_term1={$term}";
		}

		if (!$searchVars['sc']) {
			$searchVars['sc'] = 'A';
		}

		foreach ($searchVars as $var => $values) {

			if (!is_array($values)) {
				$searchUrl .= '&' . $var . '=' . urlencode(utf8_decode($values));
			} else {
				foreach ($values as $value) {
					$searchUrl .= '&' . $var . '[]=' . urlencode(utf8_decode($value));
				}
			}
		}

		return $searchUrl;
    }

    /**
     * Suche durchführen
     *
     * @param string Such string
     *
     * @return array
     */
    public function search($term, $searchVars = array()) {

		$searchUrl = str_replace(" ", "", $this->createSearchUrl($term, $searchVars));
		$xml_request = $this->XMLPageConnection->getDataFromXMLPage($searchUrl);

		if (!$xml_request) {
			return false;
		}
		$i = 0;
		$result = array('page_vars');
		foreach ($xml_request->page_vars->children() AS $key => $value) {
			$result = array('page_vars' => array($key => (string) $value->attributes()->value));
			//$result['page_vars'][$key] = (string) $value->attributes()->value;
		}

		foreach ($xml_request->page_vars->children() AS $key => $value) {
			$result['page_vars'][$key] = (string) $value->attributes()->value;
		}

		$result['page_vars']['search_count'] = (int) $xml_request->ezb_alphabetical_list_searchresult->search_count;

		if (isset($xml_request->ezb_alphabetical_list_searchresult->navlist->other_pages)) {
			foreach ($xml_request->ezb_alphabetical_list_searchresult->navlist->other_pages AS $key2 => $value2) {
				foreach ($value2->attributes() AS $key3 => $value3) {
					$result['navlist']['pages'][(string) $value3] = array(
						'id' => (string) $value3,
						'title' => (string) $value2
					);
				}
			}
		}
		$current_page = (string) $xml_request->ezb_alphabetical_list_searchresult->navlist->current_page;

		if ($current_page) {
			$result['navlist']['pages'][$current_page] = $current_page;
		}
		if (is_array($result['navlist']['pages'])) {
			ksort($result['navlist']['pages']);
		}

		if ($xml_request->ezb_alphabetical_list_searchresult->current_title) {
			$result['alphabetical_order']['current_title'] = (string) $xml_request->ezb_alphabetical_list_searchresult->current_title;
		}

		if (isset($xml_request->ezb_alphabetical_list_searchresult->alphabetical_order->journals->journal)) {
			foreach ($xml_request->ezb_alphabetical_list_searchresult->alphabetical_order->journals->journal AS $key => $value) {
				$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['title'] = (string) $value->title;
				$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['jourid'] = (int) $value->attributes()->jourid;
				$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color_code'] = (int) $value->journal_color->attributes()->color_code;
				$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color'] = (string) $value->journal_color->attributes()->color;
				$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['detail_link'] = '';
				$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
			}
		}
		$i = 0;
		foreach ($xml_request->ezb_alphabetical_list_searchresult->next_fifty AS $key => $value) {
			$result['alphabetical_order']['next_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$result['alphabetical_order']['next_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$result['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string) $value->next_fifty_titles;
			$i++;
		}

		$i = 0;
		foreach ($xml_request->ezb_alphabetical_list_searchresult->first_fifty AS $key => $value) {
			$result['alphabetical_order']['first_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$result['alphabetical_order']['first_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$result['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string) $value->first_fifty_titles;
			$i++;
		}

		return $result;
    }
}
?>