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
    private $search;

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
    public function getGeneralSearch()
    {
        return $this->search;
    }

    public function getColumnSearchValue(string $columnName): ?DataTableColumnModel
    {
        $searchedColumn = $this->columns->filter(function (DataTableColumnModel $column) use ($columnName) {
            return $column->getName() === $columnName && null !== $column->getSearchValue();
        });

        return $searchedColumn->isEmpty() ? null : $searchedColumn->first();
    }

    private function setColumns(array $columns): void
    {
        $this->columns = new ArrayCollection();

        foreach ($columns as $column) {
            $searchValue = $column['search']['value'];

            $this->columns->add(new DataTableColumnModel(
                $column['data'],
                $column['name'],
                $column['searchable'] == true,
                $column['orderable'] == true,
                $searchValue !== '' ? $searchValue : null,
                $column['search']['regex']
            ));
        }
    }

    private function setOrderColumn(int $orderColumn): void
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
