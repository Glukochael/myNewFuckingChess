<?php
declare(strict_types=1);

namespace Chess\Handlers;

use Chess\Board;
use Chess\Condition;
use Chess\PieceName;
use Chess\Pieces\Piece;
use Chess\Pieces\King;
use Chess\Position;

abstract class HandlerOfAnyPositions implements IHandler
{
    /**
     * @var Position[]
     */
    private array $directions = [];

    public function __construct()
    {
        $this->directions = $this->getDirections();
    }

    public function getAttackPositions(Piece $piece, Board $board): array
    {
        //Может быть этот метод нужно не разбить, но как минимум переписать с учётом того, что доска сама ведёт проверку по параметрам
        $movePositions = [];
        foreach ($this->directions as $move) {
            for ($i = 1;; $i++) {
                $possibleMove = $move->multiplicationPositions($i)->additionPositions($piece->getPosition());
                if (!$board->isRightCoordinates($possibleMove)) {
                    break;
                }
                $barrier = $this->hasBarrier($piece, $possibleMove, $board);
                if ($barrier === null) {
                    $movePositions[] = $possibleMove;
                } elseif ($barrier === 1) {
                    $movePositions[] = $possibleMove;
                    if ($this->isEnemyKingAfter($piece, $move, $possibleMove, $board)) {
                        $board->getPieceByPosition($possibleMove)->setMobilityOff($piece);
                    }
                    break;
                } elseif ($barrier === -1) {
                    $board->getPieceByPosition($possibleMove)->getCheck()->setCheckOn($piece);
                    $blockMove = $possibleMove->additionPositions($move);
                    if ($board->isRightCoordinates($blockMove)) {
                        $blockMove->setCondition(Condition::PassiveAttack);
                        $movePositions[] = $blockMove;
                        // блять, я не понимаю, зачем я это делал... кажется понял,
                        $pieceByBlockMove = $board->getPieceByPosition($blockMove);
                        if ($pieceByBlockMove !== null && $pieceByBlockMove->getColor() === $piece->getColor()) {
                            $pieceByBlockMove->setDefenseOn();
                        }
                    }
                    break;
                } elseif ($barrier === 0) {
                    $board->getPieceByPosition($possibleMove)->setDefenseOn();
                    break;
                }
            }
        }
        return $movePositions;
    }

    public function setMovablePositionsChecksDefenses(Piece $piece, Board $board): void
    {
        $movePositions = $this->getAttackPositions($piece, $board);
        if (method_exists($piece, 'isPossibleCastling')) {
            $castling = $piece->isPossibleCastling($piece, $board);
            if ($castling !== null) {
                $movePositions[] = $castling;
            }
        }
        $piece->setAttackMoves($movePositions);
    }

    private function hasBarrier(Piece $piece, Position $position, Board $board): ?int
    {
        $pieceByPosition = $board->getPieceByPosition($position);
        if ($pieceByPosition !== null) {
            if ($pieceByPosition->getColor() !== $piece->getColor()) {
                if ($pieceByPosition instanceof King) {
                    return -1;
                }
                return 1;
            }
            else {
                return 0;
            }
        }
        return null;
    }

    private function isEnemyKingAfter(Piece $piece, Position $shift, Position $move, Board $board): bool
    {
        $changeablePosition = $move->additionPositions($shift);
        while ($board->isRightCoordinates($changeablePosition)) {
            $pieceByPosition = $board->getPieceByPosition($changeablePosition);
            if ($pieceByPosition) {
                return $pieceByPosition->getName() === PieceName::King && $pieceByPosition->getColor() !== $piece->getColor();
            }
            $changeablePosition = $changeablePosition->additionPositions($shift);
            print_r($changeablePosition);
            print_r($shift);
        }
        return false;
    }

    abstract protected function getDirections();
}