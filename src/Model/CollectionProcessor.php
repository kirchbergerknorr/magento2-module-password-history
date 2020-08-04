<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\PasswordHistory\Model;

use Magento\Framework\{
    Api\SearchCriteria\CollectionProcessor\FilterProcessor,
    Api\SearchCriteria\CollectionProcessor\PaginationProcessor,
    Api\SearchCriteria\CollectionProcessor\SortingProcessor,
    Api\SearchCriteria\CollectionProcessorInterface,
    Api\SearchCriteriaInterface,
    Data\Collection\AbstractDb
};

/**
 * Collection processor class that combines FilterProcessor, PaginationProcessor and SortingProcessor
 * in order to not inject them separately in the repository class
 */
class CollectionProcessor implements CollectionProcessorInterface
{
    /**
     * @var FilterProcessor
     */
    private $filterProcessor;
    /**
     * @var SortingProcessor
     */
    private $sortingProcessor;
    /**
     * @var PaginationProcessor
     */
    private $paginationProcessor;

    public function __construct(
        FilterProcessor $filterProcessor,
        SortingProcessor $sortingProcessor,
        PaginationProcessor $paginationProcessor
    ) {
        $this->filterProcessor = $filterProcessor;
        $this->sortingProcessor = $sortingProcessor;
        $this->paginationProcessor = $paginationProcessor;
    }

    /**
     * @inheritDoc
     */
    public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection)
    {
        $this->filterProcessor->process($searchCriteria, $collection);
        $this->sortingProcessor->process($searchCriteria, $collection);
        $this->paginationProcessor->process($searchCriteria, $collection);
    }
}
