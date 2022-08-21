<?php
declare(strict_types=1);

namespace Chess\Notebook;

use Chess\Color;
use Chess\Notebook\NotationNotes;
use Chess\PieceName;
use Chess\Pieces\Piece;
use Chess\Position;

class Notation
{
    public function __construct(
        private int $numberMove,
        private Position $from,
        private Position $to,
        private NotationNotes $moveStatus,
        private Piece $piece,
    ){
    }

    public function getMoveNumber(): int
    {
        return $this->numberMove;
    }

    public function getColor(): Color
    {
        return $this->piece->getColor();
    }

    public function getPiece(): Piece
    {
        return $this->piece;
    }

    public function getPieceName(): PieceName
    {
        return $this->piece->getName();
    }

    public function getFromPosition(): Position
    {
        return $this->from;
    }

    public function getToPosition(): Position
    {
        return $this->to;
    }

    public function getMoveStatus(): NotationNotes
    {
        return $this->moveStatus;
    }
}