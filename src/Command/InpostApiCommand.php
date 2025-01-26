<?php

declare(strict_types=1);

namespace App\Command;

use App\Mapper\DataMapperInterface;
use App\Model\Resource;
use App\Service\InpostApiClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

#[AsCommand(
    name: 'fetch-api-data',
    description: 'Fetch inpost API data'
)]
class InpostApiCommand extends Command
{
    // bin/console fetch-api-data points Kozy

    public function __construct(
        private readonly DataMapperInterface $apiMapper,
        private readonly InpostApiClient $inpostApiClient,

    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('resource', InputArgument::REQUIRED, 'Resource type');
        $this->addArgument('city', InputArgument::OPTIONAL, 'City name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $data = $this->inpostApiClient->fetchData(
                $input->getArgument('resource'),
                $input->getArgument('city')
            );
            $resource = $this->apiMapper->deserialize($data, Resource::class, 'json');
            dump($resource);
        } catch (ClientExceptionInterface $exception) {
            $output->writeln('<error>Error fetching data: ' . $exception->getMessage() . '</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
