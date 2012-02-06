<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Dbis',
	array(
		//'Subject' => 'show, list, displayDetail, index,displayTop,displayList,displayMiniForm,displayForm, displayOverview',
		'Dbis' => 'displayDetail, displayOverview, displayList'
		
	),
	// non-cacheable actions
	array(
		'Subject' => '',
		'Dbis' => '',
		'Ezb' => '',
		
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Ezb',
	array(
		//'Subject' => 'show, list, index, displayTop, displayList, displayDetail, displayMiniForm, displayForm',
		//'Dbis' => 'show, list',
		'Ezb' => 'displayDetail, displayList, displayMiniForm, displayForm'
		
	),
	// non-cacheable actions
	array(
		'Subject' => '',
		'Dbis' => '',
		'Ezb' => '',
		
	)
);

?>