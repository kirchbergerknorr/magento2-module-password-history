<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */
namespace Snk\PasswordHistory\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\{
    Api\SearchCriteriaBuilderFactory,
    Api\SearchCriteriaBuilder,
    Api\SortOrderBuilderFactory,
    Api\SortOrderBuilder,
    Encryption\EncryptorInterface,
    Exception\CouldNotDeleteException,
    Exception\CouldNotSaveException,
    Exception\LocalizedException,
    Exception\NoSuchEntityException
};
use Snk\PasswordHistory\{
    Api\Data\UsedPasswordInterface,
    Api\UsedPasswordManagementInterface,
    Api\UsedPasswordRepositoryInterface,
    Helper\Config
};

class UsedPasswordManagement implements UsedPasswordManagementInterface
{
    /**
     * @var UsedPasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $criteriaBuilderFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SortOrderBuilderFactory
     */
    private $sortOrderBuilderFactory;

    public function __construct(
        UsedPasswordRepositoryInterface $passwordRepository,
        SearchCriteriaBuilderFactory $criteriaBuilderFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory,
        CustomerRepositoryInterface $customerRepository,
        EncryptorInterface $encryptor,
        Config $config
    ) {
        $this->passwordRepository = $passwordRepository;
        $this->criteriaBuilderFactory = $criteriaBuilderFactory;
        $this->customerRepository = $customerRepository;
        $this->encryptor = $encryptor;
        $this->config = $config;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
    }

    /**
     * Check if the password is in the saved list of used passwords for the customer
     * Returns true if password is not on the list and throws and exception if it is
     *
     * @param string $email
     * @param string $password
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validatePassword($email, $password)
    {
        if ($this->config->isEnabled()) {
            $customer = $this->customerRepository->get($email);

            /** @var SearchCriteriaBuilder $criteriaBuilder */
            $criteriaBuilder = $this->criteriaBuilderFactory->create();
            $criteriaBuilder->addFilter(UsedPasswordInterface::CUSTOMER_ID, $customer->getId());

            /** @var UsedPasswordInterface[] $usedPasswords */
            $usedPasswords = $this->passwordRepository->getList($criteriaBuilder->create())->getItems();
            foreach ($usedPasswords as $usedPassword) {
                if ($this->encryptor->validateHash($password, $usedPassword->getHash())) {
                    throw new LocalizedException(__($this->config->getMessage()));
                }
            }
        }

        return true;
    }

    /**
     * Save password (as hash) to the list of used passwords for customer
     *
     * @param string $email
     * @param string $password
     * @return void
     * @throws LocalizedException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveUsedPassword($email, $password)
    {
        $customer = $this->customerRepository->get($email);

        $usedPassword = $this->passwordRepository->getNew();
        $usedPassword->setCustomerId($customer->getId());
        $usedPassword->setHash($this->encryptor->getHash($password, true));

        $this->passwordRepository->save($usedPassword);

        $this->cleanUpOldPasswords($customer->getId());
    }

    /**
     * Remove all used passwords for customer except for configured number of last ones
     *
     * @param int $customerId
     * @return void
     * @throws CouldNotDeleteException
     */
    private function cleanUpOldPasswords($customerId)
    {
        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->sortOrderBuilderFactory->create();

        $sortOrder = $sortOrderBuilder
            ->setField(UsedPasswordInterface::CREATED_AT)
            ->setDescendingDirection()
            ->create();

        /** @var SearchCriteriaBuilder $criteriaBuilderToKeep */
        $criteriaBuilderToKeep = $this->criteriaBuilderFactory->create();
        $criteriaBuilderToKeep->addFilter(UsedPasswordInterface::CUSTOMER_ID, $customerId);

        $criteriaBuilderAll = clone $criteriaBuilderToKeep;

        /** @var UsedPasswordInterface[] $passwordsAll */
        $passwordsAll = $this->passwordRepository->getList($criteriaBuilderAll->create())->getItems();

        $criteriaBuilderToKeep->addSortOrder($sortOrder);
        $criteriaBuilderToKeep->setPageSize($this->config->getHistorySize());
        $passwordsToKeep = $this->passwordRepository->getList($criteriaBuilderToKeep->create())->getItems();

        /** @var UsedPasswordInterface $password */
        foreach (array_diff(array_keys($passwordsAll), array_keys($passwordsToKeep)) as $passwordId) {
            $this->passwordRepository->delete($passwordsAll[$passwordId]);
        }
    }
}
