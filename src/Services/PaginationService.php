<?php
namespace App\Services;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaginationService
{
    protected int $offset;
    protected int $currentPage;
    protected int $totalPage;

    protected QueryBuilder $query;

    private int $limit;

    public function pagination(QueryBuilder $queryBuilder, int $currentPage, int $limit): array
    {
        $dataArray = [];
        $this->query = $queryBuilder;
        $this->currentPage = $currentPage;
        $this->limit = $limit;
        $this->calculateOffset();

        $dataArray['data'] = $this->getPaginationData();
        $totalRows = $this->totalRows();

        $dataArray['pagination']['totalPages'] = $this->totalPage;
        $dataArray['pagination']['totalRows'] = $totalRows;
        $dataArray['pagination']['prevPage'] = $this->getPrevPage();
        $dataArray['pagination']['nextPage'] = $this->getNextPage();

        $dataArray['pagination']['disableFirst'] = ($this->currentPage == 1);
        $dataArray['pagination']['disableLast'] = ($this->currentPage >= $this->totalPage);
        $dataArray['pagination']['currentPage'] = $this->currentPage;

        return $dataArray;
    }

    public function calculateOffset(): PaginationService
    {
        $this->offset = ($this->currentPage <= 1) ? 0 : $this->limit*($this->currentPage - 1);

        return $this;
    }

    public function setNumberOfData($no): void
    {
        $this->limit = $no;
    }

    public function getTotalPageNumber($rows): void
    {
        $this->totalPage = (int) ceil((int)$rows/$this->limit);
    }

    private function getPaginationData(): array
    {
        return $this->query
            ->setFirstResult($this->offset)
            ->setMaxResults($this->limit)
            ->getQuery()
            ->getResult();
    }

    private function getPrevPage(): int
    {
        $prevPage = $this->currentPage;
        --$prevPage;

        if ($this->currentPage <= 1) {
            $prevPage = 0;
        }

        if ($this->currentPage > $this->totalPage) {
            $prevPage = $this->totalPage;
        }

        return $prevPage;
    }

    private function getNextPage(): int
    {
        $nextPage = $this->currentPage;
        ++$nextPage;
        if ($this->currentPage >= $this->totalPage) {
            $nextPage = $this->totalPage;
        }

        return $nextPage;
    }

    private function totalRows(): int
    {
        $alias = current($this->query->getDQLPart('from'))->getAlias();

        $totalRowsQuery = $this->query
            ->select("COUNT(DISTINCT $alias.id ) as totalRows")
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery();

        $totalRowsQuery = $this->filterParams($totalRowsQuery);

        $counter = (int) array_sum(array_column($totalRowsQuery->getScalarResult(), 'totalRows'));

        $this->getTotalPageNumber($counter);

        return $counter;
    }

    private function filterParams(Query $query): Query
    {
        $params = $query->getParameters();
        $queryDql = $query->getDQL();

        $query->setParameters([]);

        foreach ($params->toArray() as $param) {
            /** @var Parameter $param */
            if (false !== strpos($queryDql, ':'.$param->getName())) {
                $query->setParameter($param->getName(), $param->getValue());
            }
        }

        return $query;
    }
}
