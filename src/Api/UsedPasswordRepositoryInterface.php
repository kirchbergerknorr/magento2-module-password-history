<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */
namespace Snk\PasswordHistory\Api;

use Magento\Framework\{
    Api\SearchCriteriaInterface,
    Api\SearchResultsInterface,
    Exception\CouldNotDeleteException,
    Exception\CouldNotSaveException,
    Exception\NoSuchEntityException
};
use Snk\PasswordHistory\Api\Data\UsedPasswordInterface;

interface UsedPasswordRepositoryInterface
{
    /**
     * @param UsedPasswordInterface $usedPassword
     * @return UsedPasswordInterface
     * @throws CouldNotSaveException
     */
    public function save($usedPassword);

    /**
     * @param int $id
     * @return UsedPasswordInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param UsedPasswordInterface $entity
     * @throws CouldNotDeleteException
     */
    public function delete($entity);

    /**
     * @return UsedPasswordInterface
     */
    public function getNew();
}
