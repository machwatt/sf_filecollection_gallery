<?php
namespace SKYFILLERS\SfFilecollectionGallery\Service;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Jöran Kurschatke <j.kurschatke@skyfillers.com>, Skyfillers GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * FileCollectionService
 */
class FileCollectionService
{

    /**
     * Collection Repository
     *
     * @var \TYPO3\CMS\Core\Resource\FileCollectionRepository
     * @inject
     */
    protected $collectionRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager
     * @inject
     */
    protected $beConfigManager;

    /**
     * Returns an array of file objects for the given UIDs of fileCollections
     *
     * @param $collectionUids
     * @return array
     */
    public function getFileObjectsFromCollection($collectionUids)
    {
        $imageItems = array();
        foreach ($collectionUids as $collectionUid) {
            $collection = $this->collectionRepository->findByUid($collectionUid);
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
     * Sorts the Result Array according to the Flexform Settings
     * @param array $imageItems
     * @return array
     */
    protected function sortFileObjects($imageItems) {
        $lowercase = array_map(function($n) { return strtolower($n->getName()); }, $imageItems);
        if ($this->beConfigManager->getConfiguration()['settings']['order'] === 'desc') {
            array_multisort($lowercase, SORT_DESC, SORT_STRING, $imageItems);
        } else {
            array_multisort($lowercase, SORT_ASC, SORT_STRING, $imageItems);
        }
        return $imageItems;
    }

	/**
	 * Returns an FileObject from a given FileReference
	 *
	 * @param \TYPO3\CMS\Core\Resource\FileReference $item
	 * @return \TYPO3\CMS\Core\Resource\File
	 */
	protected function getFileObjectFromFileReference($item) {
		/** @var \TYPO3\CMS\Core\Resource\File $returnItem */
		$returnItem = $item->getOriginalFile();
		$returnItem->updateProperties($item->getProperties());
		return $returnItem;
	}

}