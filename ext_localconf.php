<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'sf_filecollection_gallery',
        'Pifilecollectiongallery',
        [
            SKYFILLERS\SfFilecollectionGallery\Controller\GalleryController::class => 'list',
        ],
        // non-cacheable actions
        [
            SKYFILLERS\SfFilecollectionGallery\Controller\GalleryController::class => '',

        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'sf_filecollection_gallery',
        'Pifilecollectiongallerynested',
        [
            SKYFILLERS\SfFilecollectionGallery\Controller\GalleryController::class => 'list, nested',
        ],
        // non-cacheable actions
        [
            SKYFILLERS\SfFilecollectionGallery\Controller\GalleryController::class => '',

        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'sf_filecollection_gallery',
        'Pifilecollectiongalleryfolder',
        [
            SKYFILLERS\SfFilecollectionGallery\Controller\GalleryController::class => 'nestedFromFolder, listFromFolder',
        ],
        // non-cacheable actions
        [
            SKYFILLERS\SfFilecollectionGallery\Controller\GalleryController::class => '',

        ]
    );

    // Use hook from http://www.derhansen.de/2014/06/typo3-how-to-prevent-empty-flexform.html
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['SKYFILLERS.sf_filecollection_gallery'] = 'SKYFILLERS\SfFilecollectionGallery\Hooks\DataHandlerHooks';
});
