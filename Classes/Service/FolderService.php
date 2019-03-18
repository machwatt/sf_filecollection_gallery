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

/**
 * FolderService
 *
 * @author Jöran Kurschatke <info@joerankurschatke.de>
 */
class FolderService
{

    /**
     *
     * @return \TYPO3\CMS\Core\Resource\Folder|\TYPO3\CMS\Core\Resource\InaccessibleFolder
     *
     * @param \TYPO3\CMS\Core\Resource\File $file
     */
    public function getFolderByFile($file)
    {
        /** @var \TYPO3\CMS\Core\Resource\ResourceStorage $storage */
        $storage = $file->getStorage();
        $folderIdentifier = $storage->getFolderIdentifierFromFileIdentifier($file->getIdentifier());
        return $storage->getFolder($folderIdentifier);
    }
}
