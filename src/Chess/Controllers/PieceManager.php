<?php
declare(strict_types=1);

namespace Chess\Controllers;

use Chess\Board;
use Chess\Check;
use Chess\Condition;
use Chess\DefenseStatus;
use Chess\Handlers\BishopHandler;
use Chess\Handlers\IHandler;
use Chess\Handlers\KingHandler;
use Chess\Handlers\KnightHandler;
use Chess\Handlers\PawnHandler;
use Chess\Handlers\QueenHandler;
use Chess\Handlers\RookHandler;
use Chess\MobilityStatus;
use Chess\PieceName;
use Chess\Pieces\King;
use Chess\Pieces\Piece;
use Chess\Position;
use Exception;

class PieceManager
{
    /**
     * @var IHandler[]
     */
    private array $handlers;

    public function __construct()
    {
        $this->handlers = [
            'Pawn' => new PawnHandler(),
            'Knight' => new KnightHandler(),
            'Bishop' => new BishopHandler(),
            'Rook' => new RookHandler(),
            'Queen' => new QueenHandler(),
            'King' => new KingHandler(),
        ];
    }

    /**
     * @throws Exception
     */
    public function setPiecesDefenseAndMoves(Board $board): void
    {
        $board->sortPieces();
        $board->setDefenceOff();
        $board->setCheckOff();
        $board->setMobilityOn();
        foreach ($board->getPieces() as $piece) {
            if ($handler = $this->handlers[$piece->getName()->name] ?? null) {
                $handler->setMovablePositionsChecksDefenses($piece, $board);
            }
        }
        $allPieces = $board->getPieces();
        array_walk($allPieces, function (Piece $piece) {
           if ($piece->getMobility()->getStatus() === MobilityStatus::No) {
               $piece->setAttackMoves(array_filter($piece->getAttackMoves(), static function (Position $move) use ($piece): bool {
                   $attackPiecePosition = $piece->getMobility()->getPiece()->getPosition();
                   return $move->equals($attackPiecePosition);
               }));
           }
        });
        $pieces = [];
        $checks = [];
        $kings = array_values($board->getPiecesByName(PieceName::King));
        foreach ($kings as $king) {
            $checks[] = $king->getCheck();
            $pieces[] = $board->getPiecesByColor($king->getColor());
        }
        foreach ($pieces as $index => $piecesByColor) {
            foreach ($piecesByColor as $piece) {
                if ($this->isCheck($kings[$index])) {
                    $piece->setAttackMoves($this->getCheckMoves($checks[$index], $piece, $board));
                }
            }
        }
        foreach ($pieces as $index => $piecesByColor) {
            $this->isEndGame($piecesByColor, $checks[$index]);
        }
    }

    public function getCheckMoves(Check $check, Piece $piece, Board $board): array
    {
        if ($piece->getName() === PieceName::King) {
            return $piece->getAttackMoves();
        }
        $checkMoves = $check->getCheckMoves();
        if ($checkMoves === null) {
            return [];
        } elseif (count($checkMoves) > 0) {
            return array_values(array_filter($piece->getAttackMoves(), static function (Position $move) use ($checkMoves): bool {
                foreach ($checkMoves as $checkMove) {
                    if ($move->equals($checkMove)) {
                        return true;
                    }
                }
                return false;
            }));
        }
        // Всё могло сломаться нахой
        return $this->formCheckMoves($check, $piece, $board);
    }

    public function formCheckMoves(Check $check, Piece $piece, Board $board): array
    {
        $attackPieces = $check->getEnemyList();
        $pieceMoves = $piece->getAttackMoves();
        $possibleMovesPieces = [];
        foreach ($attackPieces as $index => $attackPiece) {
            $attackPiecePosition = $attackPiece->getPosition();
            $attackPieceName = $attackPiece->getName();
            if ($attackPieceName === PieceName::Knight || $attackPieceName === PieceName::Pawn) {
                $possibleMovesPieces[] = [$attackPiecePosition];
            } else {
                $possibleMovesPieces[] = [];
                $king = $board->getKingByColor($piece->getColor());
                $shiftPosition = $king->getPosition()->getPositionShiftToOtherPosition($attackPiecePosition);
                for ($comparedPosition = $king->getPosition()->additionPositions($shiftPosition);
                     !$comparedPosition->equals($attackPiecePosition->additionPositions($shiftPosition));
                     $comparedPosition = $comparedPosition->additionPositions($shiftPosition)) {
                    $possibleMovesPieces[$index][] = $comparedPosition;
                }
            }
        }
        $comparedPositions = $possibleMovesPieces[0];
        if (count($possibleMovesPieces) > 1) {
            foreach ($possibleMovesPieces as $possibleMove) {
                if (count($comparedPositions) > 0) {
                    $comparedPositions = array_filter($possibleMove, static function (Position $move) use ($comparedPositions): bool {
                        foreach ($comparedPositions as $position) {
                            if ($position) {
                                if ($position->equals($move)) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    });
                }
            }
        }
        $validationMoves = array_filter($comparedPositions, static function (Position $position) use ($pieceMoves): bool {
            foreach ($pieceMoves as $move) {
                if ($move) {
                    if ($move->equals($position)) {
                        return true;
                    }
                }
            }
            return false;
        });
        if (count($validationMoves) === 0) {
            $check->setCheckMoves(null);
        } else {
            $check->setCheckMoves($validationMoves);
        }
        return $validationMoves;
    }

    public function isCheck(King $king): bool
    {
        return $king->getCheck()->getCheckStatus() === DefenseStatus::On;
    }

    /**
     * не забыть спросить у Вовы
     * @throws Exception
     */
    public function isEndGame(array $piecesByColor, Check $check): void
    {
        $movablePieces = array_filter($piecesByColor, static function (Piece $piece): bool {
            $moves = array_filter($piece->getAttackMoves(), static function (Position $position): bool {
                return $position->getCondition() !== Condition::PassiveAttack;
            });
            if ($moves) {
                return true;
            }
            return false;
        });
        if (count($movablePieces) === 0) {
            if ($check->getCheckStatus() === DefenseStatus::On) {
                throw new Exception("Checkmate");

            } else {
                throw new Exception("Draw");
            }
        }
    }
}