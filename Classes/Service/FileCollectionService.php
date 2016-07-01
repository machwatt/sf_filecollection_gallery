<?php
namespace SKYFILLERS\SfFilecollectionGallery\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;
use TYPO3\CMS\Core\Resource\FileReference;

/**
 * FileCollectionService
 *
 * @author Jöran Kurschatke <j.kurschatke@skyfillers.com>
 */
class FileCollectionService
{

    /**
     * Collection Repository
     *
     * @var \TYPO3\CMS\Core\Resource\FileCollectionRepository
     */
    protected $fileCollectionRepository;

    /**
     * The Frontend Configuration
     *
     * @var \TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager
     */
    protected $frontendConfigurationManager;

    /**
     * Inject the fileCollection repository
     *
     * @param \TYPO3\CMS\Core\Resource\FileCollectionRepository $fileCollectionRepository
     *
     * @return void
     */
    public function injectFileCollectionRepository(FileCollectionRepository $fileCollectionRepository)
    {
        $this->fileCollectionRepository = $fileCollectionRepository;
    }

    /**
     * Inject the Frontend Configuration Manager.
     *
     * @param \TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager $frontendConfigurationManager
     *
     * @return void
     */
    public function injectFrontendConfigurationManager(FrontendConfigurationManager $frontendConfigurationManager)
    {
        $this->frontendConfigurationManager = $frontendConfigurationManager;
    }

    /**
     * Returns an array of file objects for the given UIDs of fileCollections
     *
     * @param array $collectionUids The uids
     *
     * @return array
     */
    public function getFileObjectsFromCollection(array $collectionUids)
    {
        $imageItems = array();
        foreach ($collectionUids as $collectionUid) {
            $collection = $this->fileCollectionRepository->findByUid($collectionUid);
            $collection->loadContents();
            foreach ($collection->getItems() as $item) {
                $collectionProperties = [
                    'uid' => $collection->getUid(),
                    'title' => $collection->getTitle(),
                    'description' => $collection->getDescription()
                ];
                if (get_class($item) === 'TYPO3\CMS\Core\Resource\FileReference') {
                    $file = $this->getFileObjectFromFileReference($item);
                    $file->updateProperties(['collection' => $collectionProperties]);
                    array_push($imageItems, $file);
                } else {
                    $item->updateProperties(['collection' => $collectionProperties]);
                    array_push($imageItems, $item);
                }
            }
        }
        return $this->sortFileObjects($imageItems);
    }

    /**
     * Returns an array of gallery covers for the given UIDs of fileCollections
     *
     * @param $collectionUids
     * @return array
     */
    public function getGalleryCoversFromCollections($collectionUids)
    {
        $imageItems = array();
        foreach ($collectionUids as $collectionUid) {
            $collection = $this->fileCollectionRepository->findByUid($collectionUid);
            $collection->loadContents();
            $galleryCover = array();
            foreach ($collection->getItems() as $item) {
                if (get_class($item) === 'TYPO3\CMS\Core\Resource\FileReference') {
                    array_push($galleryCover, $this->getFileObjectFromFileReference($item));
                } else {
                    array_push($galleryCover, $item);
                }
            }
            $galleryCover = $this->sortFileObjects($galleryCover);

            $galleryCover[0]->galleryUID = $collectionUid;
            $galleryCover[0]->galleryTitle = $collection->getTitle();
            $galleryCover[0]->galleryDescription = $collection->getDescription();
            $galleryCover[0]->gallerySize = sizeof($galleryCover);

            array_push($imageItems, $galleryCover[0]);
        }
        return $this->sortFileObjects($imageItems);
    }

    /**
     * Returns an array of gallery covers for the given UIDs of fileCollections
     * Use if you have recursive folder collection.
     *
     * @param $collectionUids
     * @return array
     */
    public function getGalleryCoversFromNestedFoldersCollection($collectionUids)
    {
        $configuration = $this->frontendConfigurationManager->getConfiguration();
        $imageItems = array();

        // Load all images from collection
        foreach ($collectionUids as $collectionUid) {
            $collection = $this->fileCollectionRepository->findByUid($collectionUid);
            $collection->loadContents();
            $galleryCover = array();

            // Load all image and sort them by folder_hash
            foreach ($collection->getItems() as $item) {
                if (get_class($item) === 'TYPO3\CMS\Core\Resource\FileReference') {
                    array_push($galleryCover, $this->getFileObjectFromFileReference($item));
                } else {
                    array_push($galleryCover, $item);
                }
            }
            $this->sortFileObjectsByFolderHash($galleryCover, SORT_ASC);

            // Split all images in separate arrays
            $folderHashedGalleryCovers = array();
            $oldFolderHash = '';
            foreach ($galleryCover as $item) {
                $getFolderPath = explode(strrchr($item->getIdentifier(), "/"), $item->getIdentifier());
                $itemIdentificatorWithoutFilename = $getFolderPath[0];
                if ($item->getProperty('folder_hash') !== $oldFolderHash) {
                    $oldFolderHash = $item->getProperty('folder_hash');
                    $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename] = array();
                    array_push($folderHashedGalleryCovers[$itemIdentificatorWithoutFilename], $item);
                } else {
                    array_push($folderHashedGalleryCovers[$itemIdentificatorWithoutFilename], $item);
                }
            }

            // Sort the array by key => $itemIdentificatorWithoutFilename
            if ($configuration['settings']['orderNestedFolder'] == "asc") {
                ksort($folderHashedGalleryCovers);
            } else {
                krsort($folderHashedGalleryCovers);
            }

            // Sort all subarrays depending on the current settings
            foreach ($folderHashedGalleryCovers as $itemIdentificatorWithoutFilename => $folderHashedGalleryCoversArray) {
                $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename] = $this->sortFileObjects($folderHashedGalleryCoversArray);
                $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename][0]->galleryFolder = $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename][0]->getProperty('folder_hash');

                $getFolderPath = explode("/", $itemIdentificatorWithoutFilename);
                $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename][0]->galleryFolderName = end($getFolderPath);
                $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename][0]->galleryUID = $collectionUid;
                $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename][0]->gallerySize = sizeof($folderHashedGalleryCovers[$itemIdentificatorWithoutFilename]);
                array_push($imageItems, $folderHashedGalleryCovers[$itemIdentificatorWithoutFilename][0]);
            }
        }
        return $imageItems;
    }

    /**
     * Returns an array of gallery covers for the given UIDs of fileCollections
     * Use if you have recursive folder collection.
     *
     * @param $collectionUids
     * @param $galleryFolder
     * @return array
     */
    public function getGalleryItemsByFolderHash($collectionUids, $galleryFolderHash)
    {
        $imageItems = array();

        // Load all images from collection
        foreach ($collectionUids as $collectionUid) {
            $collection = $this->fileCollectionRepository->findByUid($collectionUid);
            $collection->loadContents();
            $allItems = array();

            // Load all image and sort them by folder_hash
            foreach ($collection->getItems() as $item) {
                if($item->getProperty('folder_hash') === $galleryFolderHash) {
                    if (get_class($item) === 'TYPO3\CMS\Core\Resource\FileReference') {
                        array_push($allItems, $this->getFileObjectFromFileReference($item));
                    } else {
                        array_push($allItems, $item);
                    }
                }
            }
            $imageItems = $this->sortFileObjects($allItems);
        }
        return $imageItems;
    }

    /**
     * Returns the array including pagination settings
     *
     * @param array $settings The current settings
     * @return array
     */
    public function buildPaginationArray($settings)
    {
        $paginationArray = array();
        if (!empty($settings)) {
            $paginationArray = array(
                'itemsPerPage' => $settings['imagesPerPage'],
                'maximumVisiblePages' => $settings['numberOfPages'],
                'insertAbove' => $settings['insertAbove'],
                'insertBelow' => $settings['insertBelow']
            );
        }
        return $paginationArray;
    }

    /**
     * Returns the array including pagination settings for nested views
     *
     * @param array $settings The current settings
     * @return array
     */
    public function buildPaginationArrayForNested($settings)
    {
        $paginationArray = array();
        if (!empty($settings)) {
            $paginationArray = array(
                'itemsPerPage' => $settings['nestedImagesPerPage'],
                'maximumVisiblePages' => $settings['nestedNumberOfPages'],
                'insertAbove' => $settings['nestedInsertAbove'],
                'insertBelow' => $settings['nestedInsertBelow']
            );
        }
        return $paginationArray;
    }

    /**
     * Returns the array for assign to view in controller
     *
     * @param array $imageItems The imageItems to show
     * @param int $offset The offset in gallery
     * @param array $paginationConfiguration The pagination config
     * @param array $settings The settings array
     * @param int $currentUid The current uid
     * @param int $columnPosition The column position
     * @param bool $showBackToGallerySelectionLink If back link should be shown
     *
     * @return array
     */
    public function buildArrayForAssignToView($imageItems, $offset, $paginationConfiguration, $settings,
                                              $currentUid, $columnPosition, $showBackToGallerySelectionLink)
    {
        $assign = array(
            'imageItems' => $imageItems,
            'offset' => $offset,
            'paginationConfiguration' => $paginationConfiguration,
            'settings' => $settings,
            'currentUid' => $currentUid,
            'columnPosition' => $columnPosition,
            'showBackToGallerySelectionLink' => $showBackToGallerySelectionLink
        );
        return $assign;
    }

    /**
     * @param array $items The items to sort
     * @param $direction the direction. SORT_DESC or SORT_ASC
     * @return bool
     */
    protected function sortFileObjectsByName(&$items, $direction)
    {
        $lowercaseNames = array_map(function ($n) {
            return strtolower($n->getName());
        }, $items);

        array_multisort($lowercaseNames, $direction, SORT_STRING, $items);
    }

    /**
     * @param array $items The items to sort
     * @param $direction the direction. SORT_DESC or SORT_ASC
     * @return bool
     */
    protected function sortFileObjectsByDate(&$items, $direction)
    {
        $dates = array_map(function ($n) {
            return strtolower($n->getCreationTime());
        }, $items);

        array_multisort($dates, $direction, SORT_NUMERIC, $items);
    }

    /**
     * @param array $items The items to sort
     * @param $direction the direction. SORT_DESC or SORT_ASC
     * @return bool
     */
    protected function sortFileObjectsByFolderHash(&$items, $direction)
    {
        $folderhashes = array_map(function ($n) {
            return strtolower($n->getProperty('folder_hash'));
        }, $items);

        array_multisort($folderhashes, $direction, SORT_NUMERIC, $items);
    }

    /**
     * Sorts the Result Array according to the Flexform Settings
     *
     * @param array $imageItems The image items
     *
     * @return array
     */
    protected function sortFileObjects($imageItems)
    {
        $configuration = $this->frontendConfigurationManager->getConfiguration();
        switch ($configuration['settings']['order']) {
            case 'desc':
                $this->sortFileObjectsByName($imageItems, SORT_DESC);
                break;
            case 'date-desc':
                $this->sortFileObjectsByDate($imageItems, SORT_DESC);
                break;
            case 'date-asc':
                $this->sortFileObjectsByDate($imageItems, SORT_ASC);
                break;
            case 'manual':
                // Do not sort. This could be default, but could be breaking, since default was ASC before.
                break;
            default:
                $this->sortFileObjectsByName($imageItems, SORT_ASC);
                break;
        }
        return $imageItems;
    }

    /**
     * Returns an FileObject from a given FileReference
     *
     * @param \TYPO3\CMS\Core\Resource\FileReference $item The item
     *
     * @return \TYPO3\CMS\Core\Resource\File
     */
    protected function getFileObjectFromFileReference(FileReference $item)
    {
        /**
         * The item to return
         *
         * @var \TYPO3\CMS\Core\Resource\File $returnItem
         */
        $returnItem = $item->getOriginalFile();
        $returnItem->updateProperties($item->getProperties());
        return $returnItem;
    }
}
