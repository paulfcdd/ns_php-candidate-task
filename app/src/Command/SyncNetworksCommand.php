<?php

namespace App\Command;

use App\Service\CityBike\NetworkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync-networks',
    description: 'Add a short description for your command',
)]
class SyncNetworksCommand extends Command
{
    public function __construct(
        private readonly NetworkService $cityBikeNetworkService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->cityBikeNetworkService->syncNetworks();
            $io->success('Data was synced');
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
