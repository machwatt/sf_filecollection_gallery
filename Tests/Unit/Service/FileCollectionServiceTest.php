<?php
namespace SKYFILLERS\SfFilecollectionGallery\Tests\Unit\Service;
/**
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
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\File;

/**
 * Class FileCollectionServiceTest
 * 
 * @package 
 * @author  Stefano Kowalke <blueduck@gmx.net>
 */
class FileCollectionServiceTest extends UnitTestCase {

	/** @var array A backup of registered singleton instances */
	protected $singletonInstances = array();

	/** @var \SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService */
	protected $subject;

	/** @var  \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection */
	protected $fileCollectionMock;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Resource\FileCollectionRepository */
	protected $fileCollectionRepositoryMock;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager */
	protected $frontendConfigurationManagerMock;

	/** @var \TYPO3\CMS\Core\Resource\ResourceStorage */
	protected $storageMock;

	/**
	 * Set up
	 */
	public function setUp() {
		$this->singletonInstances = \TYPO3\CMS\Core\Utility\GeneralUtility::getSingletonInstances();

		$this->fileCollectionMock = $this->getAccessibleMock(
			'\TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection',
			array('loadContents', 'getItems'),
			array(),
			'',
			FALSE
		);
		$this->fileCollectionMock->expects($this->atLeastOnce())->method('loadContents');


		$this->fileCollectionRepositoryMock = $this->getMock(
			'\TYPO3\CMS\Core\Resource\FileCollectionRepository',
			array('findByUid'),
			array(),
			'',
			FALSE
		);
		$this->fileCollectionRepositoryMock->expects($this->any())
			->method('findByUid')
			->will($this->returnValue($this->fileCollectionMock));

		$this->frontendConfigurationManagerMock = $this->getMock(
			'\TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager',
			array('getConfiguration'),
			array(),
			'',
			FALSE
		);

		$this->storageMock = $this->getMock(
			'TYPO3\CMS\Core\Resource\ResourceStorage',
			array(),
			array(),
			'',
			FALSE
		);
		$this->storageMock->expects($this->any())
			->method('getUid')
			->will($this->returnValue(5));

		$this->subject = GeneralUtility::makeInstance('\SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService');
		$this->subject->injectFileCollectionRepository($this->fileCollectionRepositoryMock);
		$this->subject->injectFrontendConfigurationManager($this->frontendConfigurationManagerMock);
	}

	/**
	 * Shut down
	 */
	public function tearDown() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::resetSingletonInstances($this->singletonInstances);
		unset($this->fileCollectionRepositoryMock);
		unset($this->frontendConfigurationManagerMock);
		unset($this->subject);
		parent::tearDown();
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface|\TYPO3\CMS\Core\Resource\FileReference
	 */
	protected function getFileFixture($sorting = 'desc') {
		$fileNames = array(
			'desc' => array('d', 'c', 'b', 'a'),
			'asc' => array('a', 'b', 'c', 'd'),
			'misc' => array('d', 'b', 'a', 'c')
		);

		$result = array();

		switch ($sorting) {
			case 'desc':
				foreach ($fileNames['desc'] as $name) {
					$result[] = new \TYPO3\CMS\Core\Resource\File(
						array('identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'),
						$this->storageMock
					);
				}
				break;
			case 'asc':
				foreach ($fileNames['asc'] as $name) {
					$result[] = new \TYPO3\CMS\Core\Resource\File(
						array('identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'),
						$this->storageMock
					);
				}
				break;
			case 'mixed':
				foreach ($fileNames['misc'] as $name) {
					$result[] = new \TYPO3\CMS\Core\Resource\File(
						array('identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'),
						$this->storageMock
					);
				}
				break;
			default:
				foreach ($fileNames['asc'] as $name) {
					$result[] = new \TYPO3\CMS\Core\Resource\File(
						array('identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'),
						$this->storageMock
					);
				}
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function collectionUidsDataProvider() {
		return array(
			'oneUid' => array(array(1), 4),
			'twoUids' => array(array(1,2), 8),
			'threeUids' => array(array(1,3, 5), 12),
		);
	}

	/**
	 * @param array $collectionUids
	 * @param int $expectedTimes
	 *
	 * @test
	 * @dataProvider collectionUidsDataProvider
	 */
	public function getFileObjectFromCollectionWithUids(array $collectionUids, $expectedTimes) {
		$this->fileCollectionMock->expects($this->any())->method('getItems')->will($this->returnValue($this->getFileFixture()));

		$imageItems = $this->subject->getFileObjectsFromCollection($collectionUids);
		$this->assertEquals($expectedTimes, count($imageItems));
	}

	/**
	 * @return array
	 */
	public function sortingDataProviderAsc() {
		$fileList = array('a.pdf', 'b.pdf', 'c.pdf', 'd.pdf');

		return array(
			'asc' => array('asc', $fileList),
			'desc' => array('desc', $fileList),
			'mixed' => array('mixed', $fileList)
		);
	}

	/**
	 * @return array
	 */
	public function sortingDataProviderDesc() {
		$fileList = array('d.pdf', 'c.pdf', 'b.pdf', 'a.pdf');

		return array(
			'asc' => array('asc', $fileList),
			'desc' => array('desc', $fileList),
			'mixed' => array('mixed', $fileList)
		);
	}

	/**
	 * @test
	 *
	 * @param string $sorting
	 * @param string $expectedSortingOrderOfFiles
	 *
	 * @dataProvider sortingDataProviderAsc
	 */
	public function getFileObjectFromCollectionAsc($sorting, $expectedSortingOrderOfFiles){
		$this->fileCollectionMock->expects($this->any())
			->method('getItems')
			->will($this->returnValue($this->getFileFixture($sorting)));

		$this->frontendConfigurationManagerMock->expects($this->once())
			->method('getConfiguration')
			->will($this->returnValue(array('settings' => array('order' => 'asc'))));

		$imageItems = $this->subject->getFileObjectsFromCollection(array(1));
		$itemCount = count($imageItems);

		for ($i = 0; $i < $itemCount; $i++) {
			$this->assertEquals($expectedSortingOrderOfFiles[$i], $imageItems[$i]->getName());
		}
	}

	/**
	 * @test
	 *
	 * @param string $sorting
	 * @param string $fileName
	 *
	 * @dataProvider sortingDataProviderDesc
	 */
	public function getFileObjectFromCollectionDesc($sorting, $fileName){
		$this->fileCollectionMock->expects($this->any())
			->method('getItems')
			->will($this->returnValue($this->getFileFixture($sorting)));

		$this->frontendConfigurationManagerMock->expects($this->once())
			->method('getConfiguration')
			->will($this->returnValue(array('settings' => array('order' => 'desc'))));

		$imageItems = $this->subject->getFileObjectsFromCollection(array(1));
		$itemCount = count($imageItems);

		for ($i = 0; $i < $itemCount; $i++) {
			$this->assertEquals($fileName[$i], $imageItems[$i]->getName());
		}
	}
}

