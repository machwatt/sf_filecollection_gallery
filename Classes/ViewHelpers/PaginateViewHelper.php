<?php
namespace SKYFILLERS\SfFilecollectionGallery\ViewHelpers;
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
 * This widget is a copy of the fluid paginate widget. Now it's possible to
 * use arrays with paginate, not only query results.
 *
 * @author     Paul Beck <pb@teamgeist-medien.de>
 * @author     Armin Ruediger Vieweg <info@professorweb.de>
 * @copyright  2011 Copyright belongs to the respective authors
 */
class PaginateViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper {

	/**
	 * @var \SKYFILLERS\SfFilecollectionGallery\Controller\PaginateController
	 */
	protected $controller;

	/**
	 * Injection of widget controller
	 *
	 * @param \SKYFILLERS\SfFilecollectionGallery\Controller\PaginateController $controller
	 * @return void
	 */
	public function injectController(\SKYFILLERS\SfFilecollectionGallery\Controller\PaginateController $controller) {
		$this->controller = $controller;
	}

	/**
	 * The render method of widget
	 *
	 * @param mixed $objects \TYPO3\CMS\ExtBase\Persistence\QueryResultInterface,
	 *        \TYPO3\CMS\ExtBase\Persistence\ObjectStorage object or array
	 * @param string $as
	 * @param array $configuration
	 * @return string
	 */
	public function render($objects, $as, array $configuration = array('itemsPerPage' => 10,
		'insertAbove' => FALSE, 'insertBelow' => TRUE)) {
		return $this->initiateSubRequest();
	}
}