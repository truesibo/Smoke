<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 19.05.15
 * Time: 08:45
 */

namespace whm\CacheWatch\Cli\Command;


use phmLabs\Base\Www\Uri;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;
use whm\CacheWatch\Scanner\Scanner;

class ScanCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('url', InputArgument::REQUIRED, 'the url to start with'),
                new InputOption('parallel_requests', '-p', InputOption::VALUE_OPTIONAL, 'number of parallel requests.', 10),
                new InputOption('num_urls', '', InputOption::VALUE_OPTIONAL, 'number of urls to be checled', 20),
                new InputOption('config_file', '', InputOption::VALUE_OPTIONAL, 'config file'),
            ))
            ->setDescription('analyses a website')
            ->setHelp('The <info>analyse</info> command runs a cache test.')
            ->setName('analyse');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $output->writeln("\n <info>Scanning $url</info>\n");

        if ($input->hasOption('config_file')) {
            $configArray = Yaml::parse(file_get_contents($input->getOption('config_file')));
        } else {
            $configArray = array("whitelist" => null, 'blacklist' => null);
        }

        if ($configArray["whitelist"] == null) {
            $configArray["whitelist"] = array("^^");
        }
        if ($configArray["blacklist"] == null) {
            $configArray["blacklist"] = array();
        }

        $scanner = new Scanner(new Uri($url),
            $output,
            $configArray["whitelist"],
            $configArray["blacklist"],
            $input->getOption('num_urls'),
            $input->getOption('parallel_requests'));

        $scanResult = $scanner->scan();

        $output->writeln("");
        $output->writeln("");

        $output->writeln(" <comment>Passed tests:</comment> \n");

        foreach ($scanResult as $url => $result) {
            if( $result["type"] == Scanner::PASSED ) {
                $output->writeln("   <info>" . $url . " </info>". $result["message"]);
            }
        }

        $output->writeln("\n <comment>Failed tests:</comment> \n");

        foreach ($scanResult as $url => $result) {
            if( $result["type"] == Scanner::ERROR ) {
                $output->writeln("   <error>" . $url . " </error>  " . $result["message"]);
            }
        }

        $output->writeln("");
    }
}