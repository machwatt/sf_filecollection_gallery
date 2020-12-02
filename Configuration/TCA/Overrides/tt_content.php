<?php
defined('TYPO3_MODE') or die();

/**
 * Register the plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'sf_filecollection_gallery',
    'Pifilecollectiongallery',
    'FileCollection Gallery (List View)'
);

/**
 * Register the plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'sf_filecollection_gallery',
    'Pifilecollectiongallerynested',
    'FileCollection Gallery (Nested View)'
);

/**
 * Register the plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'sf_filecollection_gallery',
    'Pifilecollectiongalleryfolder',
    'FileCollection Gallery (Nested Folders)'
);

/**
 * Add flexform
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['sffilecollectiongallery_pifilecollectiongallery'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'sffilecollectiongallery_pifilecollectiongallery',
    'FILE:EXT:sf_filecollection_gallery/Configuration/FlexForms/Flexform_plugin.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['sffilecollectiongallery_pifilecollectiongallerynested'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'sffilecollectiongallery_pifilecollectiongallerynested',
    'FILE:EXT:sf_filecollection_gallery/Configuration/FlexForms/Flexform_plugin.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['sffilecollectiongallery_pifilecollectiongalleryfolder'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'sffilecollectiongallery_pifilecollectiongalleryfolder',
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
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['sffilecollectiongallery_pifilecollectiongallery'] = 'layout,select_key,recursive,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['sffilecollectiongallery_pifilecollectiongallerynested'] = 'layout,select_key,recursive,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['sffilecollectiongallery_pifilecollectiongalleryfolder'] = 'layout,select_key,recursive,pages';
