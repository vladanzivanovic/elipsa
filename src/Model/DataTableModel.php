<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;

class DataTableModel
{
    private int $draw;

    private int $offset;

    private int $limit;

    private ArrayCollection $columns;

    private string $orderColumn;

    private string $orderDirection;

    /**
     * @var array|string|null
     */
    private $search = null;

    public function __construct(
        int $draw,
        int $offset,
        int $limit,
        array $columns,
        int $orderColumn,
        string $orderDirection,
        string $search = null
    ) {
        $this->draw = $draw;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->setColumns($columns);
        $this->setOrderColumn($orderColumn);
        $this->orderDirection = $orderDirection;
        $this->search = $this->setSearch($search);
    }

    public function getDraw(): int
    {
        return $this->draw;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getColumns(): ArrayCollection
    {
        return $this->columns;
    }

    public function getOrderColumn(): string
    {
        return $this->orderColumn;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * @return array|string|null
     */
    public function getSearch()
    {
        return $this->search;
    }

    private function setColumns(array $columns): void
    {
        $this->columns = new ArrayCollection();

        foreach ($columns as $column) {
            $this->columns->add(new DataTableColumnModel(
                $column['data'],
                $column['name'],
                $column['searchable'] == true,
                $column['orderable'] == true,
                $column['search']['value'],
                $column['search']['regex']
            ));
        }
    }

    private function setOrderColumn(int $orderColumn)
    {
        /** @var DataTableColumnModel $dataTableColumn */
        $dataTableColumn = $this->columns->get($orderColumn);

        $this->orderColumn = $dataTableColumn->getName();
    }

    /**
     * @return array|string|null
     */
    private function setSearch(?string $search)
    {
        $decodedSearch = json_decode($search, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $search;
        }

        return $decodedSearch;
    }
}
