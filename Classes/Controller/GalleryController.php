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
	 * @param \SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService $fileCollectionService
	 *
	 * @return void
	 */
	public function injectFileCollectionService(FileCollectionService $fileCollectionService) {
		$this->fileCollectionService = $fileCollectionService;
	}

	/**
	 * List action
	 *
	 * @param int $offset
	 * @return void
	 */
	public function listAction($offset = 0) {
		if($this->settings['fileCollection'] !== "" && $this->settings['fileCollection']) {
			$collectionUids = explode(',', $this->settings['fileCollection']);
			$imageItems = $this->fileCollectionService->getFileObjectsFromCollection($collectionUids);
			$cObj = $this->configurationManager->getContentObject();
			$currentUid = $cObj->data['uid'];

			$paginationArray = array(
				'itemsPerPage' => $this->settings['imagesPerPage'],
				'maximumVisiblePages' => $this->settings['numberOfPages'],
				'insertAbove' => $this->settings['insertAbove'],
				'insertBelow' => $this->settings['insertBelow']
			);
			$this->view->assignMultiple(array(
				'imageItems' => $imageItems,
				'offset' => $offset,
				'paginationConfiguration' => $paginationArray,
				'settings' => $this->settings,
				'currentUid' => $currentUid
			));
		}
	}

}