<?php
namespace SKYFILLERS\SfFilecollectionGallery\Controller;
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

use SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService;

/**
 * GalleryController
 *
 * @author Jöran Kurschatke <j.kurschatke@skyfillers.com>
 */
class GalleryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * File Collection Service
	 *
	 * @var \SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService
	 */
	protected $fileCollectionService;

	/**
	 * Inject the FileCollectionService
	 *
	 * @param \SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService $fileCollectionService The service
	 *
	 * @return void
	 */
	public function injectFileCollectionService(FileCollectionService $fileCollectionService) {
		$this->fileCollectionService = $fileCollectionService;
	}

	/**
	 * List action
	 *
	 * @param int $offset The offset
	 *
	 * @return void
	 */
	public function listAction($offset = 0) {
		if ($this->settings['fileCollection'] !== '' && $this->settings['fileCollection']) {
			$collectionUids = explode(',', $this->settings['fileCollection']);
			$imageItems = $this->fileCollectionService->getFileObjectsFromCollection($collectionUids);
			$cObj = $this->configurationManager->getContentObject();
			$currentUid = $cObj->data['uid'];
			$columnPosition = $cObj->data['colPos']; //To determine where the gallery is placed (e.g. sidebar)

			//If more than one FileCollection is selected
			if (sizeof($collectionUids) > 1) {
				//if a special gallery is requested
				if ($this->request->hasArgument('galleryUID')) {
					$gallery = array($this->request->getArgument('galleryUID'));
					$imageItems = $this->fileCollectionService->getFileObjectsFromCollection($gallery);
					$showBackToGallerySelectionLink = true;
				} else {
					$this->redirect('nested');
				}
			} else {
				//If only one filecollection is selected
				$imageItems = $this->fileCollectionService->getFileObjectsFromCollection($collectionUids);
			}

			$paginationArray = array(
				'itemsPerPage' => $this->settings['imagesPerPage'],
				'maximumVisiblePages' => $this->settings['numberOfPages'],
				//don't show pagination if there is only one page
				'insertAbove' => (sizeof($imageItems) <= $this->settings['imagesPerPage']) ? false : $this->settings['insertAbove'],
				'insertBelow' => (sizeof($imageItems) <= $this->settings['imagesPerPage']) ? false : $this->settings['insertBelow']
			);

			$this->view->assignMultiple(array(
				'imageItems' => $imageItems,
				'offset' => $offset,
				'paginationConfiguration' => $paginationArray,
				'settings' => $this->settings,
				'currentUid' => $currentUid,
				'columnPosition' => $columnPosition,
				'showBackToGallerySelectionLink' => $showBackToGallerySelectionLink
			));
		}
	}

	/**
	 * Nested action
	 *
	 * @param int $offset The offset
	 *
	 * @return void
	 */
	public function nestedAction($offset = 0) {

		$cObj = $this->configurationManager->getContentObject();
		$currentUid = $cObj->data['uid'];
		$columnPosition = $cObj->data['colPos'];

		$collectionUids = explode(',', $this->settings['fileCollection']);

		//Get Gallery Covers for Gallery selection page
		$imageItems = $this->fileCollectionService->getGalleryCoversFromCollections($collectionUids);

		$paginationArray = array(
			'itemsPerPage' => $this->settings['imagesPerPage'],
			'maximumVisiblePages' => $this->settings['numberOfPages'],
			//don't show pagination if there is only one page
			'insertAbove' => (sizeof($imageItems) <= $this->settings['imagesPerPage']) ? false : $this->settings['insertAbove'],
			'insertBelow' => (sizeof($imageItems) <= $this->settings['imagesPerPage']) ? false : $this->settings['insertBelow']
		);

		$this->view->assignMultiple(array(
			'paginationConfiguration' => $paginationArray,
			'offset' => $offset,
			'imageItems' => $imageItems,
			'settings' => $this->settings,
			'currentUid' => $currentUid,
			'columnPosition' => $columnPosition
		));
	}
}