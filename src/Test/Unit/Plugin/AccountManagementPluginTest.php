<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Test\Unit\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Customer\Api\AccountManagementInterface;
use PHPUnit\Framework\TestCase;
use Snk\PasswordHistory\{
    Api\UsedPasswordManagementInterface,
    Plugin\AccountManagementPlugin
};

class AccountManagementPluginTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UsedPasswordManagementInterface
     */
    private $usedPasswordManagement;

    /**
     * @var object
     */
    private $accountManagementPlugin;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->usedPasswordManagement = $this->createMock(UsedPasswordManagementInterface::class);

        $this->accountManagementPlugin = $this->objectManager->getObject(AccountManagementPlugin::class, [
            'passwordManagement' => $this->usedPasswordManagement
        ]);
    }

    /**
     * @return void
     */
    public function testBeforeChangePassword()
    {
        $params = ['some@email.com', 'old_password', 'new_password'];
        $result = $this->accountManagementPlugin->beforeChangePassword(
            $this->createMock(AccountManagementInterface::class),
            ...$params
        );

        $this->assertEquals($params, $result);
    }

    /**
     * @return void
     */
    public function testAfterChangePassword()
    {
        $params = [true, 'some@email.com', 'old_password', 'new_password'];
        $result = $this->accountManagementPlugin->afterChangePassword(
            $this->createMock(AccountManagementInterface::class),
            ...$params
        );

        $this->assertEquals(true, $result);
    }
}
