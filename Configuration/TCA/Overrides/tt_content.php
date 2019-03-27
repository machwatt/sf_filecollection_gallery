<?php
defined('TYPO3_MODE') or die();

/**
 * Register the plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'sf_filecollection_gallery',
    'Pifilecollectiongallery',
    'FileCollection Gallery'
);

/**
 * Add flexform
 */
$pluginSignature = 'sffilecollectiongallery_pifilecollectiongallery';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:sf_filecollection_gallery/Configuration/FlexForms/Flexform_plugin.xml'
);

/**
 * Add static TypoScript
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'sf_filecollection_gallery',
    'Configuration/TypoScript',
    'FileCollection Gallery'
);

/**
 * Remove unused fields
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key,recursive,pages';
