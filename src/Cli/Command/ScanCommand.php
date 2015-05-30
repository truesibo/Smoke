<?php

namespace whm\Smoke\Cli\Command;

use Ivory\HttpAdapter\HttpAdapterFactory;
use phmLabs\Base\Www\Uri;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use whm\Smoke\Config\Configuration;
use whm\Smoke\Http\HttpClient;
use whm\Smoke\Scanner\Scanner;

class ScanCommand extends Command
{
    /**
     * Defines what arguments and options are available for the user. Can be listed using
     * Smoke.phar analyse --help.
     */
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('url', InputArgument::REQUIRED, 'the url to start with'),
                new InputOption('parallel_requests', 'p', InputOption::VALUE_OPTIONAL, 'number of parallel requests.', 10),
                new InputOption('num_urls', 'u', InputOption::VALUE_OPTIONAL, 'number of urls to be checled', 20),
                new InputOption('config_file', 'c', InputOption::VALUE_OPTIONAL, 'config file'),
                new InputOption('bootstrap', 'b', InputOption::VALUE_OPTIONAL, 'bootstrap file'),
                new InputOption('foreign', 'f', InputOption::VALUE_NONE, 'include foreign domains'),
            ])
            ->setDescription('analyses a website')
            ->setHelp('The <info>analyse</info> command runs a cache test.')
            ->setName('analyse');
    }

    /**
     * Runs the analysis of the given website with all given parameters.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->initConfiguration(
            $input->getOption('config_file'),
            $input->getOption('foreign'),
            $input->getOption('num_urls'),
            $input->getOption('parallel_requests'),
            new Uri($input->getArgument('url')));

        $output->writeln("\n Smoke " . SMOKE_VERSION . " by Nils Langner\n");
        $output->writeln(' <info>Scanning ' . $config->getStartUri()->toString() . "</info>\n");

        if ($input->getOption('bootstrap')) {
            include $input->getOption('bootstrap');
        }

        $progressBar = new ProgressBar($output, $input->getOption('num_urls'));

        $progressBar->setBarWidth(100);
        $progressBar->setFormat('normal');

        $progressBar->start();

        $scanner = new Scanner($config, new HttpClient(HttpAdapterFactory::guess()), $progressBar);
        $scanResults = $scanner->scan();
        $progressBar->finish();

        $this->renderResults($scanResults, $output, $config);

        return $this->getStatus($scanResults);
    }

    private function renderResults($results, $output, Configuration $config)
    {
        $reporter = $config->getReporter();
        if (method_exists($reporter, 'setOutput')) {
            $reporter->setOutput($output);
        }
        $reporter->render($results);
    }

    private function getStatus($scanResults)
    {
        foreach ($scanResults as $result) {
            if ($result['type'] === Scanner::ERROR) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * Initializes the configuration.
     *
     * @param $configFile
     * @param $loadForeign
     * @param Uri $uri
     *
     * @return Configuration
     */
    private function initConfiguration($configFile, $loadForeign, $num_urls, $parallel_requests, Uri $uri)
    {
        $defaultConfigFile = __DIR__ . '/../../settings/default.yml';
        if ($configFile) {
            if (file_exists($configFile)) {
                $configArray = Yaml::parse(file_get_contents($configFile));
            } else {
                throw new \RuntimeException("Config file was not found ('" . $configFile . "').");
            }
        } else {
            $configArray = array();
        }

        $config = new Configuration($uri, $configArray, Yaml::parse(file_get_contents($defaultConfigFile)));

        if ($loadForeign) {
            $config->enableForeignDomainScan();
        }

        if ($num_urls) {
            $config->setContainerSize($num_urls);
        }

        if ($parallel_requests) {
            $config->setParallelRequestCount($parallel_requests);
        }

        return $config;
    }
}
