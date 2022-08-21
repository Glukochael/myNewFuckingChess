<?php
declare(strict_types=1);

namespace Chess\Handlers;

use Chess\Position;

class QueenHandler extends HandlerOfAnyPositions implements IHandler
{
    protected function getDirections(): array
    {
        return [
            new Position(1, 0),
            new Position(-1, 0),
            new Position(0, 1),
            new Position(0, 1),
            new Position(1, 1),
            new Position(1, -1),
            new Position(-1, 1),
            new Position(-1, -1),
        ];
    }
}