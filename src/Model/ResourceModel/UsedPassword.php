<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */
namespace Snk\PasswordHistory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Snk\PasswordHistory\Api\Data\UsedPasswordInterface;

class UsedPassword extends AbstractDb
{
    const TABLE_NAME = 'customer_used_password';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, UsedPasswordInterface::ID);
    }
}
