<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

// hook to parse the whole content
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:secure_files/Classes/Hooks/ParseContent.php:&tx_SecureFiles_Hooks_ParseContent->parse';

// hook right after a was authenticated
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] = 'EXT:secure_files/Classes/Hooks/Login.php:&tx_SecureFiles_Hooks_Login->loggedIn';

// scheduler tasks

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_SecureFiles_Scheduler_CleanupTask'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:secure_files/Resources/Private/Language/locallang_db.xml:tx_securefiles_scheduler_cleanup.title',
	'description'      => 'LLL:EXT:secure_files/Resources/Private/Language/locallang_db.xml:tx_securefiles_scheduler_cleanup.desc',
	'additionalFields' => 'tx_SecureFiles_Scheduler_CleanupAdditionalFieldsProvider',
);
?>
