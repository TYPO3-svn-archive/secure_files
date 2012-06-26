<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}




t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Secured files using mod_rewrite');


t3lib_extMgm::addLLrefForTCAdescr('tx_securefiles_domain_model_public', 'EXT:secure_files/Resources/Private/Language/locallang_csh_tx_securefiles_domain_model_public.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_securefiles_domain_model_public');
$TCA['tx_securefiles_domain_model_public'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:secure_files/Resources/Private/Language/locallang_db.xml:tx_securefiles_domain_model_public',
		'label' => 'folder',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Public.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_securefiles_domain_model_public.gif'
	),
);

?>