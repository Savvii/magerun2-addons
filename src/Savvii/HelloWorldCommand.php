<?php

namespace Savvii;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends AbstractMagentoCommand
{
    protected function configure()
    {
        $this
            ->setName('savvii:helloworld')
            ->setDescription('Echo helloworld');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);
        if (!$this->initMagento()) {
            return 0;
        }

        return $output->writeln('Hello World!');
    }

}
