<?php
declare(strict_types=1);

namespace Chess\Handlers;

use Chess\Board;
use Chess\Color;
use Chess\Condition;
use Chess\PieceName;
use Chess\Pieces\King;
use Chess\Pieces\Piece;
use Chess\Position;

class PawnHandler implements IHandler
{
    public function setMovablePositionsChecksDefenses(Piece $piece, Board $board): void
    {
        $barrier = $this->hasBarrier($piece, $board);
        $positions = $this->getMovablePositions();
        array_walk($positions, function (Position &$position) use ($piece) {
            $position = $position->multiplicationPositions($piece->getColor()->value);
            $position = $position->additionPositions($piece->getPosition());
        });
        $positions = array_filter($positions, static function(Position $position) use ($board): bool {
            return $board->isRightCoordinates($position);
        });
        if ($barrier === 1) {
            unset($positions['oneSquare']);
            unset($positions['twoSquare']);
        } elseif ($barrier > 0 && !$this->isStartPosition($piece)) {
            unset($positions['twoSquare']);
        }
        foreach ($positions as $key => $position) {
            if (str_contains($key, 'Attack')) {
                $otherPiece = $board->getPieceByPosition($position);
                if ($otherPiece === null) {
                    $position->setCondition(Condition::PassiveAttack);
                } else {
                    if ($otherPiece instanceof King) {
                        $otherPiece->getCheck()->setCheckOn($piece);
                    }
                }
            }
        }
        $positions = array_values($positions);
        $enPassant = $this->isEnPassant($piece, $board);
        if ($enPassant !== null) {
            $positions[] = $enPassant;
        }
        $piece->setAttackMoves($positions);
    }

    private function isStartPosition(Piece $piece): bool
    {
        $yCoordinate = ($piece->getColor() === Color::White) ? 6 : 1;
        $y = $piece->getPosition()->getYCoordinate();
        if ($y === $yCoordinate) {
            return true;
        }
        return false;
    }

    private function hasBarrier(Piece $piece, Board $board): int
    {
        $oneSquareMove = new Position($piece->getPosition()->getXCoordinate(), $piece->getPosition()->getYCoordinate()
            + $piece->getColor()->value);
        $twoSquareMove = new Position($piece->getPosition()->getXCoordinate(), $piece->getPosition()->getYCoordinate()
            + ($piece->getColor()->value * 2));
        if ($board->getPieceByPosition($oneSquareMove) !== null) {
            return 1;
        }
        if ($board->getPieceByPosition($twoSquareMove) !== null) {
            return 2;
        }
        return 0;
    }

    private function isEnPassant(Piece $piece, Board $board): ?Position
    {
        $piecePosition = $piece->getPosition();
        $enPassantPosition = new Position($piecePosition->getXCoordinate(),
            ($piece->getColor() === Color::White) ? 3 : 4);
        $lastNote = $board->getNotebook()->getLastNotation();
        if ($lastNote === null) {
            return null;
        }
        $toPositionMove = $lastNote->getToPosition();
        if ($piecePosition->equals($enPassantPosition)
            && ($toPositionMove->substractionPositions($lastNote->getFromPosition())->equals(new Position(0, 2))
                || $toPositionMove->substractionPositions($lastNote->getFromPosition())->equals(new Position(0, -2)))
            && $lastNote->getPieceName() === PieceName::Pawn) {
            $leftEnPassantPiece = $enPassantPosition->substractionPositions(new Position(1, 0));
            $rightEnPassantPiece = $enPassantPosition->additionPositions(new Position(1, 0));
            if (($toPositionMove->equals($leftEnPassantPiece) || $toPositionMove->equals($rightEnPassantPiece))
                && $board->getPieceByPosition($toPositionMove) !== null) {
                return new Position($toPositionMove->getXCoordinate(), ($piece->getColor() === Color::White) ?
                2 : 5, Condition::EnPassant);
            }
        }
        return null;
    }

    private function getMovablePositions(): array
    {
        return [
            'oneSquare' => new Position(0, 1, Condition::Move),
            'twoSquare' => new Position(0, 2, Condition::Move),
            'leftAttack' => new Position(1, 1),
            'rightAttack' => new Position(-1, 1),
        ];
    }
}