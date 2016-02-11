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
class FileCollectionService {

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
	public function injectFileCollectionRepository(FileCollectionRepository $fileCollectionRepository) {
		$this->fileCollectionRepository = $fileCollectionRepository;
	}

	/**
	 * Inject the Frontend Configuration Manager.
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager $frontendConfigurationManager
	 *
	 * @return void
	 */
	public function injectFrontendConfigurationManager(FrontendConfigurationManager $frontendConfigurationManager) {
		$this->frontendConfigurationManager = $frontendConfigurationManager;
	}

	/**
	 * Returns an array of file objects for the given UIDs of fileCollections
	 *
	 * @param array $collectionUids The uids
	 *
	 * @return array
	 */
	public function getFileObjectsFromCollection(array $collectionUids) {
		$imageItems = array();
		foreach ($collectionUids as $collectionUid) {
			$collection = $this->fileCollectionRepository->findByUid($collectionUid);
			$collection->loadContents();
			foreach ($collection->getItems() as $item) {
				if (get_class($item) === 'TYPO3\CMS\Core\Resource\FileReference') {
					array_push($imageItems, $this->getFileObjectFromFileReference($item));
				} else {
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
	public function getGalleryCoversFromCollections($collectionUids) {
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
	 * Returns the array including pagination settings
	 *
	 * @param array $settings The current settings
	 * @return array
	 */
	public function buildPaginationArray($settings) {
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
											  $currentUid, $columnPosition, $showBackToGallerySelectionLink) {
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
	 * Sorts the Result Array according to the Flexform Settings
	 *
	 * @param array $imageItems The image items
	 *
	 * @return array
	 */
	protected function sortFileObjects($imageItems) {
		$lowercase = array_map(function($n) {
			return strtolower($n->getName());
		}, $imageItems);
		$configuration = $this->frontendConfigurationManager->getConfiguration();
		if ($configuration['settings']['order'] === 'desc') {
			array_multisort($lowercase, SORT_DESC, SORT_STRING, $imageItems);
		} else {
			array_multisort($lowercase, SORT_ASC, SORT_STRING, $imageItems);
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
	protected function getFileObjectFromFileReference(FileReference $item) {
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
