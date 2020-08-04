<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Test\Unit\Model;

use Magento\Framework\{
    Api\SearchCriteriaBuilder,
    Api\SearchCriteriaInterface,
    Api\SearchResultsInterface,
    Api\SortOrder,
    Api\SortOrderBuilder,
    App\Config\ScopeConfigInterface,
    Encryption\EncryptorInterface,
    Exception\LocalizedException,
    TestFramework\Unit\Helper\ObjectManager
};
use Magento\Customer\{
    Api\CustomerRepositoryInterface,
    Api\Data\CustomerInterface
};
use PHPUnit\Framework\TestCase;
use Snk\PasswordHistory\{
    Api\Data\UsedPasswordInterface,
    Api\UsedPasswordRepositoryInterface,
    Helper\Config,
    Model\UsedPasswordManagement
};

class UsedPasswordManagementTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $configHelper;

    /**
     * @var UsedPasswordManagement
     */
    private $usedPasswordManagement;
    /**
     * @var EncryptorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encryptor;
    /**
     * @var object
     */
    private $usedPasswordManagementEmptyRepo;
    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepository;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UsedPasswordRepositoryInterface
     */
    private $passwordRepository;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->configHelper = $this->createMock(Config::class);
        $this->configHelper->method('getMessage')->willReturn('Exception message');

        $searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->method('create')
            ->willReturn($this->createMock(SearchCriteriaInterface::class));

        $searchCriteriaBuilderFactory = $this->getFactoryMock(
            '\Magento\Framework\Api\SearchCriteriaBuilderFactory',
            $searchCriteriaBuilder
        );

        $sortOrderBuilder = $this->createMock(SortOrderBuilder::class);
        $sortOrderBuilder->method('setDescendingDirection')->willReturnSelf();
        $sortOrderBuilder->method('setAscendingDirection')->willReturnSelf();
        $sortOrderBuilder->method('setField')->willReturnSelf();
        $sortOrderBuilder->method('create')->willReturn(SortOrder::class);

        $sortOrderBuilderFactory = $this->getFactoryMock(
            '\Magento\Framework\Api\SortOrderBuilderFactory',
            $sortOrderBuilder
        );

        $searchResultList = $this->createMock(SearchResultsInterface::class);
        $searchResultList->method('getItems')->willReturn(
            [
                $this->createMock(UsedPasswordInterface::class),
                $this->createMock(UsedPasswordInterface::class),
                $this->createMock(UsedPasswordInterface::class),
            ]
        );
        $this->passwordRepository = $this->createMock(UsedPasswordRepositoryInterface::class);
        $this->passwordRepository->method('getList')->willReturn($searchResultList);
        $this->passwordRepository->method('getNew')->willReturn($this->createMock(UsedPasswordInterface::class));

        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->customerRepository->method('get')->willReturn($this->createMock(CustomerInterface::class));

        $this->encryptor = $this->createMock(EncryptorInterface::class);

        $this->usedPasswordManagement = $this->objectManager->getObject(UsedPasswordManagement::class, [
            'passwordRepository'      => $this->passwordRepository,
            'sortOrderBuilderFactory' => $sortOrderBuilderFactory,
            'customerRepository'      => $this->customerRepository,
            'criteriaBuilderFactory'  => $searchCriteriaBuilderFactory,
            'encryptor'               => $this->encryptor,
            'config'                  => $this->configHelper
        ]);

        $emptySearchResultList = $this->createMock(SearchResultsInterface::class);
        $emptySearchResultList->method('getItems')->willReturn([]);
        $emptyPasswordRepository = $this->createMock(UsedPasswordRepositoryInterface::class);
        $emptyPasswordRepository->method('getList')->willReturn($emptySearchResultList);

        $this->usedPasswordManagementEmptyRepo = $this->objectManager->getObject(UsedPasswordManagement::class, [
            'passwordRepository'      => $emptyPasswordRepository,
            'sortOrderBuilderFactory' => $sortOrderBuilderFactory,
            'customerRepository'      => $this->customerRepository,
            'criteriaBuilderFactory'  => $searchCriteriaBuilderFactory,
            'encryptor'               => $this->encryptor,
            'config'                  => $this->configHelper
        ]);
    }

    /**
     * @return void
     */
    public function testValidatePasswordNoException()
    {
        $this->configHelper->method('isEnabled')->willReturn(true);
        $this->encryptor->method('validateHash')->withAnyParameters()->willReturn(false);
        $this->encryptor->expects($this->atLeastOnce())->method('validateHash');
        $this->assertEquals(
            true,
            $this->usedPasswordManagement->validatePassword('some@email.com', 'password123')
        );
    }

    /**
     * @return void
     */
    public function testValidatePasswordNoSavedPasswords()
    {
        $this->configHelper->method('isEnabled')->willReturn(true);
        $this->encryptor->expects($this->never())->method('validateHash');
        $this->assertEquals(
            true,
            $this->usedPasswordManagementEmptyRepo->validatePassword('some@email.com', 'password123')
        );
    }

    /**
     * @return void
     */
    public function testValidatePasswordException()
    {
        $this->configHelper->method('isEnabled')->willReturn(true);
        $this->encryptor->method('validateHash')->withAnyParameters()->willReturn(true);
        $this->encryptor->expects($this->atLeastOnce())->method('validateHash');

        $this->expectException(LocalizedException::class);

        $this->usedPasswordManagement->validatePassword('some@email.com', 'password123');
    }

    public function testValidatePasswordDisabled()
    {
        $this->configHelper->method('isEnabled')->willReturn(false);
        $this->encryptor->expects($this->never())->method('validateHash');
        $this->assertEquals(
            true,
            $this->usedPasswordManagement->validatePassword('some@email.com', 'password123')
        );
    }

    public function testSaveUsedPassword()
    {
        $this->passwordRepository->expects($this->once())->method('save');
        $this->usedPasswordManagement->saveUsedPassword('some@email.com', 'password123');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFactoryMock($className, $returnObject)
    {
        $factoryMock = $this->getMockBuilder($className)
             ->disableOriginalConstructor()
             ->setMethods(['create'])
             ->getMock();
        $factoryMock->method('create')->willReturn($returnObject);

        return $factoryMock;
    }
}
