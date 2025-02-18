<?php

namespace App\Infrastructure\Command;

use App\Infrastructure\CrowdsecService\Bouncer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:crowdsec', description: 'Crowdsec command',hidden: true)]
class CrowdsecCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('service',InputArgument::REQUIRED);
    }
    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input,$output);
        $io->title("[Crowdesc] - Crowdsec Command");

        $settings = new \App\Infrastructure\CrowdsecService\Settings(__DIR__,$_ENV);
        $settings = $settings->settings();
        $bouncer = new Bouncer($settings);

        $argument = $input->getArgument('service');
        if($argument == "refresh-cache"){
            $bouncer->refreshBlocklistCache();
        }
        if($argument == "prune-cache"){
            $bouncer->pruneCache();
        }
        if($argument == "push-usage-metrics"){
            $bouncer->pushUsageMetrics();
        }

        $io->success("[Crowdesc] - Crowdsec command Finish");

        return Command::SUCCESS;
    }

}
