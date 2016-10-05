<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander\Console;

use League\Uri\Schemes\Http;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExpandCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('url-expander:expand')
            ->addArgument(
                'short-url',
                InputArgument::REQUIRED,
                'The short URL to expand.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(
            (string) RedirectBasedUrlExpander::createWithGuzzleClient()->expand(
                Http::createFromString(
                    $input->getArgument('short-url')
                )
            )
        );
    }
}
