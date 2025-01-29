<?php

namespace App\Command\Command;

use App\Command\Services\Helper;
use App\Connector\Qbo\QboConnector;
use App\Linker\Services\QboService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Services\QuickBooksEntitiesUpdateService;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConnectorsQuickbooksFetchCommand extends Command
{
	use LockableTrait;

	private QboConnector $connector;
	private QboService $qboService;
	private ParameterBagInterface $parameterBag;
	private QuickBooksEntitiesUpdateService|ConnectorsQuickbooksFetchCommand $quickBooksEntitiesUpdateService;

	/**
	 * QuickBooksEntitiesUpdateService constructor.
	 */
	public function __construct(QuickBooksEntitiesUpdateService $quickBooksEntitiesUpdateSrv)
	{
		parent::__construct();
		$this->quickBooksEntitiesUpdateService = $quickBooksEntitiesUpdateSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('connectors:quickbooks:fetch')
			->setDescription('Qbo: Send the request to qbo to update the entities information.')
			->addOption(
				'entity',
				'en',
				InputOption::VALUE_OPTIONAL,
				'Name of the entity to be updated. One of Account, Item, Vendor, Invoice, InvoiceItems, Payment, Bill, Vendor, BillPayment'
			)
			->addOption(
				'id',
				'id',
				InputOption::VALUE_OPTIONAL,
				'Id of the specific entity to be updated.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		Helper::applyFormatting($output);
		$outputStyle = new OutputFormatterStyle('yellow');
		$output->getFormatter()->setStyle('entname', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green');
		$output->getFormatter()->setStyle('entval', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green', null, ['bold']);
		$output->getFormatter()->setStyle('header', $outputStyle);
		$totalToProcess = $this->quickBooksEntitiesUpdateService->collect($input, $output);
		[$success, $failed] = $this->quickBooksEntitiesUpdateService->process($output);
		$output->writeln(sprintf('<entname>Processed Success %d Failed %d from Total: %d</entname>', $success, $failed, $totalToProcess));

		return (0 === $failed) ? Command::SUCCESS : Command::FAILURE;
	}
}
