<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\ImportProductsExcel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportProducts extends Command
{
    protected static $defaultName = 'app:import:excel-products';

    private \App\Services\ImportProductsExcel $importProductsExcel;

    public function __construct(
        ImportProductsExcel $importProductsExcel
    ) {
        parent::__construct(null);
        $this->importProductsExcel = $importProductsExcel;
    }

    protected function configure(): void
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importProductsExcel->doImport();

        return 0;
    }
}