<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Cli;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Creator\MassInvoicesCreatorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius-invoicing:generate-invoices',
    description: 'Generates invoices for orders placed before InvoicingPlugin installation',
)]
final class GenerateInvoicesCommand extends Command
{
    public function __construct(
        private readonly MassInvoicesCreatorInterface $massInvoicesCreator,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array $orders */
        $orders = $this->orderRepository
            ->createListQueryBuilder()
            ->andWhere('o.number IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->massInvoicesCreator->__invoke($orders);

        $output->writeln('Invoices generated successfully');

        return Command::SUCCESS;
    }
}
