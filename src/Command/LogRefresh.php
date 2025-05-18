<?php

namespace Pixel\Module\Sucuri\Command;

use Exception;
use Pixel\Module\Sucuri\Model\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LogRefresh extends Command
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
        $this->setName('sucuri:log-refresh')->setDescription('Refresh Sucuri logs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $total = $this->api->refreshLog();
            $output->writeln(
                $this->translator->trans('%s log(s) added', [$total], 'Modules.Pixelsucuri.Admin')
            );
        } catch (Exception $exception) {
            $output->writeln(
                $this->translator->trans('Unable to refresh the logs: %s', [$exception->getMessage()], 'Modules.Pixelsucuri.Admin')
            );
        }
    }
}
