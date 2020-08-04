<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Model;

use Magento\Framework\Model\AbstractModel;
use Snk\PasswordHistory\Api\Data\UsedPasswordInterface;

class UsedPassword extends AbstractModel implements UsedPasswordInterface
{
    protected $_eventPrefix = 'used_customer_password';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\UsedPassword::class);
    }

    /**
     * @inheritDoc
     */
    public function getHash()
    {
        return (string) $this->getData(self::PASSWORD_HASH);
    }

    /**
     * @inheritDoc
     */
    public function setHash($hash)
    {
        $this->setData(self::PASSWORD_HASH, $hash);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }
}
