<?php

namespace Chess\Handlers;

use Chess\Board;
use Chess\PieceName;
use Chess\Pieces\King;
use Chess\Pieces\Piece;
use Chess\Position;

class KnightHandler implements IHandler
{
    public function setMovablePositionsChecksDefenses(Piece $piece, Board $board): void
    {
        $piecePosition = $piece->getPosition();
        $movePositions = $this->getMovablePositions();
        array_walk($movePositions, function (Position &$position) use ($piecePosition) {
             $position = $position->additionPositions($piecePosition);
        });
        $possiblePositions = [];
        foreach ($movePositions as $position) {
            $otherPiece = $board->getPieceByPosition($position);
            if (isset($otherPiece)) {
                if ($piece->canCapture($otherPiece)) {
                    $possiblePositions[] = $position;
                    if ($otherPiece instanceof King) {
                        $otherPiece->getCheck()->setCheckOn($piece);
                    }
                } else {
                    $board->$otherPiece->setDefenseOn();
                }
            } else {
                $possiblePositions[] = $position;
            }
        }
        $piece->setAttackMoves($possiblePositions);
    }

    private function getMovablePositions(): array
    {
        return [
            new Position(1, 2),
            new Position(2, 1),
            new Position(2, -1),
            new Position(1, -2),
            new Position(-1, -2),
            new Position(-2, -1),
            new Position(-2, 1),
            new Position(-1, 2),
        ];
    }
}