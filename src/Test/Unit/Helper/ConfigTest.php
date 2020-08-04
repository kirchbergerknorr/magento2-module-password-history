<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Test\Unit\Helper;

use Magento\Framework\{
    App\Config\ScopeConfigInterface,
    TestFramework\Unit\Helper\ObjectManager
};
use PHPUnit\Framework\TestCase;
use Snk\PasswordHistory\Helper\Config;

class ConfigTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var object
     */
    private $configHelper;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $this->configHelper = $this->objectManager->getObject(Config::class, [
            'config'     => $this->scopeConfig
        ]);
    }

    /**
     * @dataProvider configProvider
     * @return void
     */
    public function testIsEnabled($value)
    {
        $this->scopeConfig->method('getValue')->willReturn($value);
        $this->assertInternalType('bool', $this->configHelper->isEnabled());
    }

    /**
     * @dataProvider configMessageProvider
     * @return void
     */
    public function testGetMessage($configValue, $expectedValue)
    {
        $this->scopeConfig->method('getValue')->willReturn($configValue);
        $this->assertInternalType('string', $this->configHelper->getMessage());
        $this->assertEquals($expectedValue, $this->configHelper->getMessage());
    }

    /**
     * @dataProvider configHistorySizeProvider
     * @return void
     */
    public function testGetHistorySize($configValue, $expectedResult)
    {
        $this->scopeConfig->method('getValue')->willReturn($configValue);
        $this->assertInternalType('int', $this->configHelper->getHistorySize());
        $this->assertEquals($expectedResult, $this->configHelper->getHistorySize());
    }

    /**
     * @return array
     */
    public function configHistorySizeProvider()
    {
        return [
            [false, Config::DEFAULT_HISTORY_SIZE],
            [true, 1],
            [null, Config::DEFAULT_HISTORY_SIZE],
            ['somestring', Config::DEFAULT_HISTORY_SIZE],
            ['25', 25],
            ['0', Config::DEFAULT_HISTORY_SIZE]
        ];
    }

    /**
     * @return array
     */
    public function configMessageProvider()
    {
        return [
            [false, Config::DEFAULT_MESSAGE],
            [null, Config::DEFAULT_MESSAGE],
            ['some string', 'some string'],
            ['  ', Config::DEFAULT_MESSAGE]
        ];
    }

    /**
     * @return array
     */
    public function configProvider()
    {
        return [
            [false],
            [null],
            [true],
            ['1'],
            ['0'],
            ['some string'],
            ['  ']
        ];
    }
}
