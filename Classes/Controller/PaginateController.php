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

/**
 * This view helper uses the technology of paginate widget but works with arrays
 * and the assigned objects don't need the QueryResultInterface.
 *
 * @author Paul Beck <pb@teamgeist-medien.de>
 * @author Armin Ruediger Vieweg <info@professorweb.de>
 * @author Benjamin Schulte <benjamin.schulte@diemedialen.de>
 * @author Jöran Kurschatke <j.kurschatke@skyfillers.com>
 */
class PaginateController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController
{
    /**
     * Configuration Array
     *
     * @var array
     */
    protected $configuration = ['itemsPerPage' => 5, 'insertAbove' => false, 'insertBelow' => true,
        'maximumVisiblePages' => 7];

    /**
     * All objects
     *
     * @var array
     */
    protected $objects;

    /**
     * Current Page
     *
     * @var int
     */
    protected $currentPage = 1;

    /**
     * Number of pages
     *
     * @var int
     */
    protected $numberOfPages = 1;

    /**
     * Items per pages
     *
     * @var int
     */
    protected $itemsPerPage = 0;

    /**
     * Initialize Action of the widget controller
     *
     * @todo Replace deprecated method
     * @return void
     */
    public function initializeAction()
    {
        $this->objects = $this->widgetConfiguration['objects'];
        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $this->configuration,
            $this->widgetConfiguration['configuration'],
            true
        );
    }

    /**
     * Returns the items per page
     *
     * @return int the items per page
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Sets the items per page
     *
     * @param int $itemsPerPage The items per page
     *
     * @return void
     */
    public function setItemsPerPage($itemsPerPage)
    {
        if (empty($itemsPerPage)) {
            $this->itemsPerPage = (integer)$this->configuration['itemsPerPage'];
        } else {
            $this->itemsPerPage = $itemsPerPage;
        }
    }

    /**
     * Returns the number of pages
     *
     * @return int
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * Sets the number of pages
     *
     * @param int $numberOfPages The number of pages
     *
     * @return void
     */
    public function setNumberOfPages($numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;
    }

    /**
     * Returns the current page
     *
     * @return int the current page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Sets the current page and limits it between 1 and $this->numberOfPages.
     *
     * @param int $currentPage The current page
     *
     * @return void
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        } elseif ($this->currentPage > $this->numberOfPages) {
            $this->currentPage = $this->numberOfPages;
        }
    }

    /**
     * Returns all visible objects within a range,
     * depending on itemsPerPage and the currentPage.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResult|array $objects The list of objects
     *
     * @return array<mixed> the list of visible objects
     */
    public function getVisibleObjects($objects)
    {
        $i = 0;
        $modifiedObjects = [];
        $indexMin = $this->itemsPerPage * ($this->currentPage - 1);
        $indexMax = $this->itemsPerPage * $this->currentPage - 1;

        foreach ($objects as $object) {
            if ($i >= $indexMin && $i <= $indexMax) {
                $modifiedObjects[] = $object;
            }
            $i++;
        }

        return $modifiedObjects;
    }

    /**
     * Index action of the widget controller
     *
     * @param int $currentPage The current page
     * @param int $itemsPerPage The items per page
     *
     * @return void
     */
    public function indexAction($currentPage = 1, $itemsPerPage = 0)
    {
        $this->setItemsPerPage($itemsPerPage);
        $this->setNumberOfPages(intval(ceil(count($this->objects) / (integer)$this->itemsPerPage)));
        $this->setCurrentPage((integer)$currentPage);

        $this->view->assign('contentArguments', [
            $this->widgetConfiguration['as'] => $this->getVisibleObjects($this->objects)
        ]);
        $this->view->assign('configuration', $this->configuration);
        if ($this->numberOfPages >= 2) {
            $this->view->assign('pagination', $this->buildPagination());
        }
        $this->view->assign('itemsPerPage', $this->itemsPerPage);
    }

    /**
     * Returns an array with the keys "pages", "current",
     * "numberOfPages", "nextPage" & "previousPage"
     *
     * @return array
     */
    protected function buildPagination()
    {
        $sidePageCount = intval($this->configuration['maximumVisiblePages']) >> 1;

        $firstPage = max($this->currentPage - $sidePageCount, 1);
        $lastPage = min($firstPage + $sidePageCount * 2, $this->numberOfPages);
        $firstPage = max($lastPage - $sidePageCount * 2, 1);

        $pages = [];
        foreach (range($firstPage, $lastPage) as $index) {
            $pages[] = [
                'number' => $index,
                'isCurrent' => ($index === $this->currentPage)
            ];
        }

        $pagination = [
            'pages' => $pages,
            'current' => $this->currentPage,
            'numberOfPages' => $this->numberOfPages,
            'itemsPerPage' => $this->itemsPerPage,
            'firstPage' => 1,
            'lastPage' => $this->numberOfPages,
            'isFirstPage' => ($this->currentPage == 1),
            'isLastPage' => ($this->currentPage == $this->numberOfPages)
        ];

        return $this->addPreviousAndNextPage($pagination);
    }

    /**
     * Adds the nextPage and the previousPage to the pagination array
     *
     * @param array $pagination The pagination array which get previous and
     *        next page
     *
     * @return array the pagination array which contains some meta data and
     *         another array which are the pages
     */
    protected function addPreviousAndNextPage($pagination)
    {
        if ($this->currentPage < $this->numberOfPages) {
            $pagination['nextPage'] = $this->currentPage + 1;
        }
        if ($this->currentPage > 1) {
            $pagination['previousPage'] = $this->currentPage - 1;
        }

        return $pagination;
    }
}
