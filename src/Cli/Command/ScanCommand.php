<?php

namespace whm\Smoke\Cli\Command;

use phmLabs\Base\Www\Uri;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use whm\Smoke\Config\Configuration;
use whm\Smoke\Scanner\Scanner;

class ScanCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('url', InputArgument::REQUIRED, 'the url to start with'),
                new InputOption('parallel_requests', 'p', InputOption::VALUE_OPTIONAL, 'number of parallel requests.', 10),
                new InputOption('num_urls', 'u', InputOption::VALUE_OPTIONAL, 'number of urls to be checled', 20),
                new InputOption('config_file', 'c', InputOption::VALUE_OPTIONAL, 'config file'),
                new InputOption('bootstrap', 'b', InputOption::VALUE_OPTIONAL, 'bootstrap file'),
                new InputOption('foreign', 'f', InputOption::VALUE_OPTIONAL, 'include foreign domains', false),
            ])
            ->setDescription('analyses a website')
            ->setHelp('The <info>analyse</info> command runs a cache test.')
            ->setName('analyse');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $output->writeln("\n Smoke " . SMOKE_VERSION . " by Nils Langner\n");
        $output->writeln(" <info>Scanning $url</info>\n");

        if ($input->getOption('config_file')) {
            $configArray = Yaml::parse(file_get_contents($input->getOption('config_file')));
        } else {
            $configArray = [];
        }

        if ($input->getOption('bootstrap')) {
            include $input->getOption('bootstrap');
        }

        $config = new Configuration(new Uri ($url), $configArray);

        if ($input->getOption('foreign') === "true") {
            $config->enableForeignDomainScan();
        }

        $scanner = new Scanner($config->getStartUri(),
            $output,
            $config,
            $input->getOption('num_urls'),
            $input->getOption('parallel_requests'));

        $scanResults = $scanner->scan();
        $this->renderResults($scanResults, $output);
    }

    private function renderResults($results, OutputInterface $output)
    {
        // @todo create reporter classes

        $output->writeln("\n\n <comment>Passed tests:</comment> \n");

        foreach ($results as $url => $result) {
            if ($result['type'] === Scanner::PASSED) {
                $output->writeln('   <info> ' . $url . ' </info> all tests passed');
            }
        }

        $output->writeln("\n <comment>Failed tests:</comment> \n");

        foreach ($results as $url => $result) {
            if ($result['type'] === Scanner::ERROR) {
                $output->writeln('   <error> ' . $url . ' </error> coming from ' . $result['parent']);
                foreach ($result['messages'] as $message) {
                    $output->writeln('    - ' . $message);
                }
                $output->writeln('');
            }
        }

        $output->writeln('');
    }
}