<?php
declare(strict_types=1);

namespace Chess\Handlers;

use Chess\Position;

class BishopHandler extends HandlerOfAnyPositions implements IHandler
{
    protected function getDirections(): array
    {
        return [
            new Position(1, 1),
            new Position(1, -1),
            new Position(-1, 1),
            new Position(-1, -1),
        ];
    }
}