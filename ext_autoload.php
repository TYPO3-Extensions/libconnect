<?php
$extensionPath = t3lib_extMgm::extPath('libconnect');
$default = array(
	'tx_libconnect_resources_private_lib_dbis' => $extensionPath . 'Resources/Private/Lib/Dbis.php',
    'tx_libconnect_resources_private_lib_ezb' => $extensionPath . 'Resources/Private/Lib/Ezb.php',
    'tx_libconnect_resources_private_lib_zdb' => $extensionPath . 'Resources/Private/Lib/Zdb.php',
    'tx_libconnect_resources_private_lib_xmlpageconnection' => $extensionPath . 'Resources/Private/Lib/Xmlpageconnection.php'
);

return $default;
?>