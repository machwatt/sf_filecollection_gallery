<?php
namespace SKYFILLERS\SfFilecollectionGallery\Tests\Unit\Service;

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
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\File;

/**
 * Class FileCollectionServiceTest
 *
 * @author  Stefano Kowalke <blueduck@gmx.net>
 */
class FileCollectionServiceTest extends UnitTestCase
{
    /**
     *  A backup of registered singleton instances.
     *
     * @var array
     */
    protected $singletonInstances = [];

    /**
     * The FileCollectionService
     *
     * @var \SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService
     */
    protected $subject;

    /**
     * The Abstract
     *
     * @var  \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection
     */
    protected $fileCollectionMock;

    /**
     * The Mock Object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Resource\FileCollectionRepository
     */
    protected $fileCollectionRepositoryMock;

    /**
     * The frontend mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager
     */
    protected $frontendConfigurationManagerMock;

    /**
     * The resource storage
     *
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected $storageMock;

    /**
     * Set up
     *
     * @return void
     */
    public function setUp()
    {
        $this->singletonInstances = \TYPO3\CMS\Core\Utility\GeneralUtility::getSingletonInstances();

        $this->fileCollectionMock = $this->getAccessibleMock(
            '\TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection',
            ['loadContents', 'getItems'],
            [],
            '',
            false
        );

        $this->fileCollectionRepositoryMock = $this->getMock(
            '\TYPO3\CMS\Core\Resource\FileCollectionRepository',
            ['findByUid'],
            [],
            '',
            false
        );
        $this->fileCollectionRepositoryMock->expects($this->any())
            ->method('findByUid')
            ->will($this->returnValue($this->fileCollectionMock));

        $this->frontendConfigurationManagerMock = $this->getMock(
            '\TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager',
            ['getConfiguration'],
            [],
            '',
            false
        );

        $this->storageMock = $this->getMock(
            'TYPO3\CMS\Core\Resource\ResourceStorage',
            [],
            [],
            '',
            false
        );
        $this->storageMock->expects($this->any())
            ->method('getUid')
            ->will($this->returnValue(5));

        $this->subject = GeneralUtility::makeInstance('SKYFILLERS\SfFilecollectionGallery\Service\FileCollectionService');
        $this->subject->injectFileCollectionRepository($this->fileCollectionRepositoryMock);
        $this->subject->injectFrontendConfigurationManager($this->frontendConfigurationManagerMock);
    }

    /**
     * Shut down
     *
     * @return void
     */
    public function tearDown()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::resetSingletonInstances($this->singletonInstances);
        unset($this->fileCollectionRepositoryMock);
        unset($this->frontendConfigurationManagerMock);
        unset($this->subject);
        parent::tearDown();
    }

    /**
     * Creature fixtures
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface|\TYPO3\CMS\Core\Resource\FileReference
     */
    protected function getFileFixture($sorting = 'manual')
    {
        $fileNames = [
            'desc' => ['d', 'c', 'b', 'a'],
            'asc' => ['a', 'b', 'c', 'd'],
            'misc' => ['d', 'b', 'a', 'c'],
            'manual' => ['a', 'c', 'd', 'b']
        ];

        $result = [];

        switch ($sorting) {
            case 'desc':
                foreach ($fileNames['desc'] as $name) {
                    $result[] = new \TYPO3\CMS\Core\Resource\File(
                        ['identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'],
                        $this->storageMock
                    );
                }
                break;
            case 'asc':
                foreach ($fileNames['asc'] as $name) {
                    $result[] = new \TYPO3\CMS\Core\Resource\File(
                        ['identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'],
                        $this->storageMock
                    );
                }
                break;
            case 'mixed':
                foreach ($fileNames['misc'] as $name) {
                    $result[] = new \TYPO3\CMS\Core\Resource\File(
                        ['identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'],
                        $this->storageMock
                    );
                }
                break;
            case 'manual':
                foreach ($fileNames['manual'] as $name) {
                    $result[] = new \TYPO3\CMS\Core\Resource\File(
                        ['identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'],
                        $this->storageMock
                    );
                }
                break;
            default:
                foreach ($fileNames['asc'] as $name) {
                    $result[] = new \TYPO3\CMS\Core\Resource\File(
                        ['identifier' => '/tmp/' . $name . '.pdf', 'name' => $name . '.pdf'],
                        $this->storageMock
                    );
                }
        }

        return $result;
    }

    /**
     * Dataprovider for collection uids
     *
     * @return array
     */
    public function collectionUidsDataProvider()
    {
        return [
            'oneUid' => [[1], 4],
            'twoUids' => [[1, 2], 8],
            'threeUids' => [[1, 3, 5], 12],
        ];
    }

    /**
     * Gets the FileObject from collection with uids.
     *
     * @param array $collectionUids The uids
     * @param int $expectedTimes The expected
     *
     * @test
     * @dataProvider collectionUidsDataProvider
     *
     * @return void
     */
    public function getFileObjectFromCollectionWithUids(array $collectionUids, $expectedTimes)
    {
        $this->fileCollectionMock->expects($this->atLeastOnce())->method('loadContents');
        $this->fileCollectionMock->expects($this->any())->method('getItems')->will($this->returnValue($this->getFileFixture()));

        $imageItems = $this->subject->getFileObjectsFromCollection($collectionUids);
        $this->assertEquals($expectedTimes, count($imageItems));
    }

    /**
     * Sorting data provider.
     *
     * @return array
     */
    public function sortingDataProviderAsc()
    {
        $fileList = ['a.pdf', 'b.pdf', 'c.pdf', 'd.pdf'];

        return [
            'asc' => ['asc', $fileList],
            'desc' => ['desc', $fileList],
            'mixed' => ['mixed', $fileList],
            'manual' => ['manual', $fileList]
        ];
    }

    /**
     * Dataprovider for desc
     *
     * @return array
     */
    public function sortingDataProviderDesc()
    {
        $fileList = ['d.pdf', 'c.pdf', 'b.pdf', 'a.pdf'];

        return [
            'asc' => ['asc', $fileList],
            'desc' => ['desc', $fileList],
            'mixed' => ['mixed', $fileList],
            'manual' => ['manual', $fileList]
        ];
    }

    /**
     * Get FileObject from collection ascending
     *
     * @param string $sorting Sorting direction
     * @param string $expectedSortingOrderOfFiles Expected order
     *
     * @test
     * @dataProvider sortingDataProviderAsc
     *
     * @return void
     */
    public function getFileObjectFromCollectionAsc($sorting, $expectedSortingOrderOfFiles)
    {
        $this->fileCollectionMock->expects($this->atLeastOnce())->method('loadContents');
        $this->fileCollectionMock->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($this->getFileFixture($sorting)));

        $this->frontendConfigurationManagerMock->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue(['settings' => ['order' => 'asc']]));

        $imageItems = $this->subject->getFileObjectsFromCollection([1]);
        $itemCount = count($imageItems);

        for ($i = 0; $i < $itemCount; $i++) {
            $this->assertEquals($expectedSortingOrderOfFiles[$i], $imageItems[$i]->getName());
        }
    }

    /**
     * Get FileObject from collection descending
     *
     * @param string $sorting Sorting direction
     * @param string $fileName The filename
     *
     * @test
     * @dataProvider sortingDataProviderDesc
     *
     * @return void
     */
    public function getFileObjectFromCollectionDesc($sorting, $fileName)
    {
        $this->fileCollectionMock->expects($this->atLeastOnce())->method('loadContents');
        $this->fileCollectionMock->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($this->getFileFixture($sorting)));

        $this->frontendConfigurationManagerMock->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue(['settings' => ['order' => 'desc']]));

        $imageItems = $this->subject->getFileObjectsFromCollection([1]);
        $itemCount = count($imageItems);

        for ($i = 0; $i < $itemCount; $i++) {
            $this->assertEquals($fileName[$i], $imageItems[$i]->getName());
        }
    }

    /**
     * Dataprovider for collection covers
     *
     * @return array
     */
    public function collectionCoversDataProvider()
    {
        return [
            'zeroUid' => [[], 0],
            'oneUid' => [[1], 1],
            'twoUids' => [[1, 2], 2],
            'threeUids' => [[1, 3, 5], 3],
        ];
    }

    /**
     * Get gallery covers from filecollection
     *
     * @param array $collectionUids The uids
     * @param int $expectedAmountCovers The expected
     *
     * @test
     * @dataProvider collectionCoversDataProvider
     *
     * @return void
     */
    public function getGalleryCoversFromCollections($collectionUids, $expectedAmountCovers)
    {
        $this->fileCollectionMock->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($this->getFileFixture()));
        $imageItems = $this->subject->getGalleryCoversFromCollections($collectionUids);
        $this->assertEquals($expectedAmountCovers, count($imageItems));
    }
}
