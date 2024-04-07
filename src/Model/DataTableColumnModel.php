<?php

declare(strict_types=1);

namespace App\Model;

class DataTableColumnModel
{
    private string $data;

    private string $name;

    private bool $searchable;

    private bool $orderable;

    private ?string $searchValue = null;

    private string $searchRegex;

    public function __construct(
        string $data,
        string $name,
        bool $searchable = true,
        bool $orderable = true,
        string $searchValue = null,
        string  $searchRegex = ''
    ) {
        $this->data = $data;
        $this->name = $name;
        $this->searchable = $searchable;
        $this->orderable = $orderable;
        $this->searchValue = $searchValue;
        $this->searchRegex = $searchRegex;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    public function getSearchValue(): ?string
    {
        return $this->searchValue;
    }

    public function getSearchRegex(): string
    {
        return $this->searchRegex;
    }
}
