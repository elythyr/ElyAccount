<?php

namespace ElyAccount\Tests\Command;

use ElyAccount\Handler\BasicCommandHandler;
use ElyAccount\Command\Command;
use ElyAccount\Handler\HandlesCommand;

/**
 * Creates a mock manually because PHPUnit can't handle the static call
 * to getCommandHandledType() inside another static method.
 *
 * @see HandlesCommand
 */
class BasicCommandHandlerMock implements HandlesCommand
{
    use BasicCommandHandler;

    private $commandForwarded;

    public function __construct()
    {
        $this->commandForwarded = null;
    }

    public function commandForwarded()
    {
        return $this->commandForwarded;
    }

    protected function doHandle(Command $command)
    {
        $this->commandForwarded = $command;
    }

    public static function getCommandHandledType(): string
    {
        return DummyCommand::class;
    }
}
