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
use SKYFILLERS\SfFilecollectionGallery\Service\FolderService;
use TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;

/**
 * GalleryController
 *
 * @author Jöran Kurschatke <info@joerankurschatke.de>
 */
class GalleryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * File Collection Service
     *
     * @var \SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService
     */
    protected $fileCollectionService;

    /**
     * Collection Repository
     *
     * @var \TYPO3\CMS\Core\Resource\FileCollectionRepository
     */
    protected $fileCollectionRepository;

    /**
     * Folder Service
     *
     * @var \SKYFILLERS\SfFilecollectionGallery\Service\FolderService
     */
    protected $folderService;

    /**
     * Inject the FileCollectionService
     *
     * @param \SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService $fileCollectionService The service
     *
     * @return void
     */
    public function injectFileCollectionService(FileCollectionService $fileCollectionService)
    {
        $this->fileCollectionService = $fileCollectionService;
    }

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
     * Inject the FolderService
     *
     * @param \SKYFILLERS\SfFilecollectionGallery\Service\FolderService $folderService The service
     *
     * @return void
     */
    public function injectFolderService(FolderService $folderService)
    {
        $this->folderService = $folderService;
    }

    /**
     * Initializes the view before invoking an action method.
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     * @return void
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        $view->assign('contentObjectData', $this->configurationManager->getContentObject()->data);
    }

    /**
     * List action
     *
     * @param int $offset The offset
     *
     * @return void
     */
    public function listAction($offset = 0)
    {
        if ($this->settings['fileCollection'] !== '' && $this->settings['fileCollection']) {
            $collectionUids = explode(',', $this->settings['fileCollection']);
            $cObj = $this->configurationManager->getContentObject();
            $currentUid = $cObj->data['uid'];
            $columnPosition = $cObj->data['colPos'];
            /** @var AbstractFileCollection $collection */
            $collection = null;

            $showBackToGallerySelectionLink = false;
            //if a special gallery is requested
            if ($this->request->hasArgument('galleryUID')) {
                $gallery = [$this->request->getArgument('galleryUID')];
                $imageItems = $this->fileCollectionService->getFileObjectsFromCollection($gallery);
                $collection = $this->fileCollectionRepository->findByUid($this->request->getArgument('galleryUID'));
                $showBackToGallerySelectionLink = true;
            } else {
                $imageItems = $this->fileCollectionService->getFileObjectsFromCollection($collectionUids);
            }

            if ($collection === null && count($collectionUids) === 1) {
                $collection = $this->fileCollectionRepository->findByUid($collectionUids[0]);
            }

            if ($collection !== null) {
                $collection->loadContents();
                $this->view->assign('galleryListName', $collection->getTitle());
            }

            $this->view->assignMultiple($this->fileCollectionService->buildArrayForAssignToView(
                $imageItems,
                $offset,
                $this->fileCollectionService->buildPaginationArray($this->settings),
                $this->settings,
                $currentUid,
                $columnPosition,
                $showBackToGallerySelectionLink
            ));
        }
    }

    /**
     * List from folder action
     *
     * @param int $offset The offset
     *
     * @return void
     */
    public function listFromFolderAction($offset = 0)
    {
        if ($this->settings['fileCollection'] !== '' && $this->settings['fileCollection']) {
            $cObj = $this->configurationManager->getContentObject();
            $currentUid = $cObj->data['uid'];
            $columnPosition = $cObj->data['colPos'];

            $showBackToGallerySelectionLink = false;
            $imageItems = [];
            //if a special gallery is requested
            if ($this->request->hasArgument('galleryFolder') && $this->request->hasArgument('galleryUID')) {
                $galleryFolderHash = $this->request->getArgument('galleryFolder');
                $galleryUid = [$this->request->getArgument('galleryUID')];
                $imageItems = $this->fileCollectionService->getGalleryItemsByFolderHash($galleryUid, $galleryFolderHash);
                $showBackToGallerySelectionLink = true;
            }

            if ($imageItems) {
                $this->view->assign('galleryFolderName', $this->folderService->getFolderByFile($imageItems[0])->getName());
            }

            $this->view->assignMultiple($this->fileCollectionService->buildArrayForAssignToView(
                $imageItems,
                $offset,
                $this->fileCollectionService->buildPaginationArray($this->settings),
                $this->settings,
                $currentUid,
                $columnPosition,
                $showBackToGallerySelectionLink
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
    public function nestedAction($offset = 0)
    {
        if ($this->settings['fileCollection'] !== '' && $this->settings['fileCollection']) {
            $cObj = $this->configurationManager->getContentObject();
            $currentUid = $cObj->data['uid'];
            $columnPosition = $cObj->data['colPos'];

            $collectionUids = explode(',', $this->settings['fileCollection']);

            $imageItems = $this->fileCollectionService->getGalleryCoversFromCollections($collectionUids);

            $this->view->assignMultiple($this->fileCollectionService->buildArrayForAssignToView(
                $imageItems,
                $offset,
                $this->fileCollectionService->buildPaginationArrayForNested($this->settings),
                $this->settings,
                $currentUid,
                $columnPosition,
                false
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
    public function nestedFromFolderAction($offset = 0)
    {
        if ($this->settings['fileCollection'] !== '' && $this->settings['fileCollection']) {
            $cObj = $this->configurationManager->getContentObject();
            $currentUid = $cObj->data['uid'];
            $columnPosition = $cObj->data['colPos'];

            $collectionUids = explode(',', $this->settings['fileCollection']);

            $imageItems = $this->fileCollectionService->getGalleryCoversFromNestedFoldersCollection($collectionUids);

            $this->view->assignMultiple($this->fileCollectionService->buildArrayForAssignToView(
                $imageItems,
                $offset,
                $this->fileCollectionService->buildPaginationArrayForNested($this->settings),
                $this->settings,
                $currentUid,
                $columnPosition,
                false
            ));
        }
    }
}
