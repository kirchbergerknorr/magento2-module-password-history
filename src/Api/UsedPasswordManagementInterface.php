<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */
namespace Snk\PasswordHistory\Api;

use Magento\Framework\Exception\LocalizedException;

interface UsedPasswordManagementInterface
{
    /**
     * @param string $email
     * @param string $password
     * @return bool
     * @throws LocalizedException
     */
    public function validatePassword($email, $password);

    /**
     * @param string $email
     * @param string $password
     */
    public function saveUsedPassword($email, $password);
}
