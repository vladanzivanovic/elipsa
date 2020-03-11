<?php

declare(strict_types=1);

namespace App\Model;

class DataTableColumnModel
{
    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $searchable;

    /**
     * @var bool
     */
    private $orderable;

    /**
     * @var string
     */
    private $searchValue;

    /**
     * @var string
     */
    private $searchRegex;

    public function __construct(
        string $data,
        string $name,
        bool $searchable = true,
        bool $orderable = true,
        string $searchValue = '',
        string  $searchRegex = ''
    ) {
        $this->data = $data;
        $this->name = $name;
        $this->searchable = $searchable;
        $this->orderable = $orderable;
        $this->searchValue = $searchValue;
        $this->searchRegex = $searchRegex;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    /**
     * @return string
     */
    public function getSearchValue(): string
    {
        return $this->searchValue;
    }

    /**
     * @return string
     */
    public function getSearchRegex(): string
    {
        return $this->searchRegex;
    }
}