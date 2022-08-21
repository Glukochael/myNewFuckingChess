<?php
declare(strict_types=1);

namespace Chess\Notebook;

use Chess\Color;
use Chess\PieceName;
use Chess\Pieces\Piece;
use Chess\Position;

class Notebook
{
    /**
     * @var Notation[]
     */
    private array $notations = [];
    private int $counter = 0;

    public function addNotation(Piece $piece, Position $from, Position $to, NotationNotes $moveStatus): void
    {
        $this->notations[] = new Notation($this->counter, $from, $to, $moveStatus, $piece);
        $this->counter++;
    }

    public function getLastNotation(): ?Notation
    {
        if (count($this->notations) === 0) {
            return null;
        }
        return end($this->notations);
    }

    public function getNotations(): array
    {
        return $this->notations;
    }

    public function getNotationHistoryByPiece(Piece $piece): ?array
    {
        $history = array_filter($this->notations, static function(Notation $notation) use ($piece): bool {
            return $piece === $notation->getPiece();
        });
        if (count($history) > 0) {
            return $history;
        }
        return null;
    }
}