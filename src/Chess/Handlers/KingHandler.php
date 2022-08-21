<?php
declare(strict_types=1);

namespace Chess\Handlers;

use Chess\Board;
use Chess\Color;
use Chess\Condition;
use Chess\DefenseStatus;
use Chess\PieceName;
use Chess\Pieces\Piece;
use Chess\Position;

class KingHandler implements IHandler
{
    // Аналогично - вынести как минимум отдельно метод, который возвращает абстрактные ходы
    public function setMovablePositionsChecksDefenses(Piece $piece, Board $board): void
    {
        $kingMoves = $this->getMovablePositions();
        $otherKing = $board->getKingByColor($piece->getColor() === Color::White ? Color::Black : Color::White);
        $otherKingMoves = [];
        foreach ($kingMoves as $index => $kingMove) {
            $otherKingMoves[] = $kingMove->additionPositions($otherKing->getPosition());
            $kingMoves[$index] = $kingMove->additionPositions($piece->getPosition());
        }
        $pieces = array_filter($board->getPieces(), static function(Piece $otherPiece) use ($piece): bool {
            return !($otherPiece->getName() === $piece->getName() || $otherPiece->getColor() === $piece->getColor());
        });
        foreach ($pieces as $otherPiece) {
            $moves = $otherPiece->getAttackMoves();
            foreach ($moves as $move) {
                $kingMoves = array_filter($kingMoves, static function (Position $kingMove) use ($move, $board): bool {
                    $condition = $move->getCondition();
                    return $board->isRightCoordinates($kingMove) && !($kingMove->equals($move) &&
                            ($condition === Condition::Attack || $condition === Condition::PassiveAttack));
                });
            }
        }
        $kingMoves = array_filter($kingMoves, static function (Position $position) use ($otherKingMoves): bool {
            $check = true;
            foreach ($otherKingMoves as $move) {
                if ($position->equals($move)) {
                    $check = false;
                }
            }
            return $check;
        });
        $kingMoves = array_filter($kingMoves, static function (Position $kingMove) use ($board, $piece): bool {
            $otherPiece = $board->getPieceByPosition($kingMove);
            return !$otherPiece || $piece->canCapture($otherPiece);
        });
        $kingMoves = array_values($kingMoves);
        $castlingMoves = $this->isPossibleCastling($piece, $board);
        if (count($castlingMoves) > 0) {
            foreach($castlingMoves as $castlingMove) {
                $kingMoves[] = $castlingMove;
            }
        }
        $piece->setAttackMoves($kingMoves);
    }

    private function isPossibleCastling(Piece $piece, Board $board): array
    {
        $rooks = array_filter($board->getPiecesByName(PieceName::Rook), static function(Piece $rook) use ($piece): bool {
            return $rook->getColor() === $piece->getColor();
        });
        $castlingMoves = [];
        if (count($rooks) > 0) {
            foreach ($rooks as $rook) {
                $rookMoves = $rook->getAttackMoves();
                foreach ($rookMoves as $rookMove) {
                    if ($rookMove->getCondition() === Condition::ShortCastling) {
                        $castlingMoves[] = new Position(6, $piece->getPosition()->getYCoordinate(), Condition::ShortCastling);
                    }
                    if ($rookMove->getCondition() === Condition::LongCastling) {
                        $castlingMoves[] = new Position(2, $piece->getPosition()->getYCoordinate(), Condition::LongCastling);
                    }
                }
            }
        }
        return $castlingMoves;
    }

    private function getMovablePositions(): array
    {
        return [
                new Position(1, 1),
                new Position(1, 0),
                new Position(1, -1),
                new Position(0, -1),
                new Position(-1, -1),
                new Position(-1, 0),
                new Position(-1, 1),
                new Position(0, 1),
        ];
    }
}