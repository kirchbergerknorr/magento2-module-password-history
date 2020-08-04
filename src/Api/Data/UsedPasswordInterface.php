<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Api\Data;

interface UsedPasswordInterface
{
    const ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const PASSWORD_HASH = 'password_hash';
    const CREATED_AT = 'created_at';

    /**
     * @return int|string
     */
    public function getEntityId();

    /**
     * @param int|string $id
     * @return void
     */
    public function setEntityId($id);

    /**
     * @return string
     */
    public function getHash();

    /**
     * @param string $hash
     * @return void
     */
    public function setHash($hash);

    /**
     * @param int|string $customerId
     * @return void
     */
    public function setCustomerId($customerId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);
}
