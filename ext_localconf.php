<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'SKYFILLERS.' . $_EXTKEY,
	'Pifilecollectiongallery',
	array(
		'Gallery' => 'list, nested, nestedFromFolder, listFromFolder',
	),
	// non-cacheable actions
	array(
		'Gallery' => '',

	)
);

// Use hook from http://www.derhansen.de/2014/06/typo3-how-to-prevent-empty-flexform.html
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['SKYFILLERS.' . $_EXTKEY] = 'SKYFILLERS\SfFilecollectionGallery\Hooks\DataHandlerHooks';