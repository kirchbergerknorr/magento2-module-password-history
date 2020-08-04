<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Model\ResourceModel\UsedPassword;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Snk\PasswordHistory\{
    Model\UsedPassword,
    Model\ResourceModel\UsedPassword as UsedPasswordResource
};

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(UsedPassword::class, UsedPasswordResource::class);
    }
}
