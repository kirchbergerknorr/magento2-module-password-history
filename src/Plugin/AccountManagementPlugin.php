<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\{
    Api\SearchCriteriaBuilderFactory,
    Exception\LocalizedException
};
use Snk\PasswordHistory\Api\UsedPasswordManagementInterface;

class AccountManagementPlugin
{
    /**
     * @var UsedPasswordManagementInterface
     */
    private $passwordManagement;

    public function __construct(UsedPasswordManagementInterface $passwordManagement)
    {
        $this->passwordManagement = $passwordManagement;
    }

    /**
     * @param AccountManagementInterface $subject
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     * @throws LocalizedException
     */
    public function beforeChangePassword(AccountManagementInterface $subject, $email, $currentPassword, $newPassword)
    {
        $this->passwordManagement->validatePassword($email, $newPassword);

        return [$email, $currentPassword, $newPassword];
    }

    /**
     * @param AccountManagementInterface $subject
     * @param bool $result
     * @param string $email
     * @param string $currentPassword
     * @return bool
     */
    public function afterChangePassword(AccountManagementInterface $subject, $result, $email, $currentPassword)
    {
        $this->passwordManagement->saveUsedPassword($email, $currentPassword);

        return $result;
    }
}
