<?php

namespace App\Command;

use App\Service\CityBike\NetworkService;
use App\Service\CityBike\SyncNetworkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:sync-networks',
    description: 'Add a short description for your command',
)]
class SyncNetworksCommand extends Command
{
    public function __construct(
        private readonly SyncNetworkService $syncNetworkService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->syncNetworkService->syncNetworks();
            $io->success('Data was synced');
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        } catch (TransportExceptionInterface $e) {
            $io->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
