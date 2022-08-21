<?php

namespace Chess\Pieces;

use Chess\Check;
use Chess\Color;
use Chess\PieceName;
use Chess\Position;

class King extends Piece
{
    private Check $check;

    public function __construct(Color $case, Position $position, PieceName $name)
    {
        parent::__construct($case, $position, $name);
        $this->check = new Check();
    }

    public function getCheck(): Check
    {
        return $this->check;
    }

    public function canCapture(Piece $piece): bool
    {
        return parent::canCapture($piece) && !$piece->isProtected();
    }
}