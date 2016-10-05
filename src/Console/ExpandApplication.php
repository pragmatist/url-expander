<?php

declare (strict_types = 1);

namespace Pragmatist\UrlExpander\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

final class ExpandApplication extends Application
{
    public function __construct()
    {
        parent::__construct('pragmatist/url-expander', '@package_version@');
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();
        return $inputDefinition;
    }

    protected function getCommandName(InputInterface $input)
    {
        return 'url-expander:expand';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new ExpandCommand();
        return $defaultCommands;
    }
}
