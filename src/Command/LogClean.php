<?php

namespace Pixel\Module\Sucuri\Command;

use Exception;
use Pixel\Module\Sucuri\Model\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LogClean extends Command
{
    private Api $api;

    private TranslatorInterface $translator;

    public function __construct(
        Api $api,
        TranslatorInterface $translator,
        string $name = null
    ) {
        $this->api = $api;
        $this->translator = $translator;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('sucuri:log-clean')
            ->setDescription('Clean Sucuri logs')
            ->addArgument('retention', InputArgument::OPTIONAL, 'Log retention (days)', 60);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $retention = (int)$input->getArgument('retention');
            if ($retention) {
                $total = $this->api->cleanLog($retention);
                $output->writeln(
                    $this->translator->trans('%s logs purged for a %s-day retention period', [$total, $retention], 'Modules.Pixelsucuri.Admin')
                );
            }
        } catch (Exception $exception) {
            $output->writeln(
                $this->translator->trans('Unable to clean the logs: %s', [$exception->getMessage()], 'Modules.Pixelsucuri.Admin')
            );
        }
    }
}
