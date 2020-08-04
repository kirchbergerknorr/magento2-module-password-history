<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const DEFAULT_MESSAGE = 'Please choose a password that you haven\'t used before.';
    const DEFAULT_HISTORY_SIZE = 10;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->config->getValue('customer/password/history_enabled', ScopeInterface::SCOPE_WEBSITES);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $message = $this->config->getValue('customer/password/history_message', ScopeInterface::SCOPE_STORES);

        return trim($message) ?: self::DEFAULT_MESSAGE;
    }

    /**
     * @return int
     */
    public function getHistorySize()
    {
        $historySize = (int) $this->config->getValue('customer/password/history_size', ScopeInterface::SCOPE_WEBSITES);

        return $historySize ?: self::DEFAULT_HISTORY_SIZE;
    }
}
