<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
    $_EXTKEY,
    'Dbis',
    array(
        'Dbis' => 'displayDetail, displayList, displayMiniForm, displayForm, displayNew'
    ),
    // non-cacheable actions
    array(
        'Dbis' => 'displayDetail, displayList, displayMiniForm, displayForm, displayNew'
    )
);

Tx_Extbase_Utility_Extension::configurePlugin(
    $_EXTKEY,
    'Ezb',
    array(
        'Ezb' => 'displayDetail, displayList, displayMiniForm, displayForm, displayNew'
    ),
    // non-cacheable actions
    array(
        'Ezb' => 'displayDetail, displayList, displayMiniForm, displayForm, displayNew'
    )
);
?>