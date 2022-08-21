<?php
declare(strict_types=1);

namespace Chess\Handlers;

use Chess\Board;
use Chess\Condition;
use Chess\PieceName;
use Chess\Pieces\Piece;
use Chess\Position;

class RookHandler extends HandlerOfAnyPositions implements IHandler
{
    public function setMovablePositionsChecksDefenses(Piece $piece, Board $board): void
    {
        parent::setMovablePositionsChecksDefenses($piece, $board);
        $moves = $piece->getAttackMoves();
        $castilng = $this->isPossibleCastling($piece, $board);
        if ($castilng !== null) {
            $moves[] = $castilng;
        }
        $piece->setAttackMoves($moves);
    }

    protected function getDirections(): array
    {
        return [
            new Position(1, 0),
            new Position(-1, 0),
            new Position(0, 1),
            new Position(0, -1),
        ];
    }

    private function isPossibleCastling(Piece $piece, Board $board): ?Position
    {
        $kings = array_filter($board->getPiecesByName(PieceName::King), static function (Piece $someKing) use ($piece): bool {
            return $piece->getColor() === $someKing->getColor();
        });
        if (count($kings) === 0) {
            return null;
        }
        $king = end($kings);
        if ($board->getNotebook()->getNotationHistoryByPiece($piece) !== null ||
            $board->getNotebook()->getNotationHistoryByPiece($king) !== null) {
            return null;
        }
        $piecePosition = $piece->getPosition();
        $kingPosition = $king->getPosition();
        if ($piecePosition->getXCoordinate() === 0) {
            for ($i = 1; $i < $kingPosition->getXCoordinate(); $i++) {
                if ($board->getPieceByPosition(new Position($i, $piecePosition->getYCoordinate())) !== null) {
                    return null;
                }
            }
            $newRookPosition = new Position(3, $piecePosition->getYCoordinate(), Condition::LongCastling);
            $newKingPosition = new Position(2, $piecePosition->getYCoordinate(), Condition::LongCastling);
        } else {
            for ($i = 5; $i < $piecePosition->getXCoordinate(); $i++) {
                if ($board->getPieceByPosition(new Position($i, $piecePosition->getYCoordinate())) !== null) {
                    return null;
                }
            }
            $newRookPosition = new Position(5, $piecePosition->getYCoordinate(), Condition::ShortCastling);
            $newKingPosition = new Position(6, $piecePosition->getYCoordinate(), Condition::ShortCastling);
        }
        $pieces = array_filter($board->getPieces(), static function(Piece $otherPiece) use ($piece): bool {
            return $otherPiece->getColor() !== $piece->getColor();
        });
        foreach ($pieces as $otherPiece) {
            if ($otherPiece->getName() === PieceName::Rook) {
                $attackMoves = $this->getAttackPositions($otherPiece, $board);
            } else {
                $attackMoves = $otherPiece->getAttackMoves();
            }
            foreach ($attackMoves as $attackMove) {
                if ($attackMove->equals($newKingPosition)
                    || $attackMove->equals($kingPosition)
                    || $attackMove->equals($newRookPosition)) {
                    return null;
                }
            }
        }
        return $newRookPosition;
    }
}