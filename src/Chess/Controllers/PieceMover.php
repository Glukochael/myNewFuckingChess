<?php
declare(strict_types=1);

namespace Chess\Controllers;

use Chess\Board;
use Chess\Color;
use Chess\Condition;
use Chess\Notebook\NotationNotes;
use Chess\PieceName;
use Chess\Pieces\King;
use Chess\Pieces\Piece;
use Chess\Position;

class PieceMover
{
    public function move(Position $from, Position $to, Board $board): void
    {
        $piece = $board->getPieceByPosition($from);
        if (isset($piece)) {
            $possibleMoves = $piece->getAttackMoves();
            $otherPiece = $board->getPieceByPosition($to);
            foreach ($possibleMoves as $move) {
                if ($move->equals($to)) {
                    if ($move->getCondition() === Condition::PassiveAttack) {
                        continue;
                    }
                    if (isset($otherPiece) && $move->getCondition() === Condition::Attack) {
                        $this->doDidDoneAttack($from, $move, $piece, $otherPiece, $board);
                    } elseif ($move->getCondition() === Condition::EnPassant) {
                        $this->doDidDoneEnPassant($from, $move, $piece, $board);
                    } elseif ($piece instanceof King && ($move->getCondition() === Condition::ShortCastling
                        || $move->getCondition() === Condition::LongCastling)) {
                        $this->doDidDoneCastling($from, $move, $piece, $board);
                    } elseif ($move->getCondition() !== Condition::LongCastling ||
                        $move->getCondition() !== Condition::ShortCastling) {
                        $piece->setPosition($to);
                        $this->formNotation($from, $to, $piece, NotationNotes::Move, $board);
                    }
                }
            }
        }
    }

    private function doDidDoneEnPassant(Position $from, Position $to, Piece $piece, Board $board): void
    {
        $enPassantPosition = new Position($to->getXCoordinate(), ($piece->getColor() === Color::White) ? 3 : 4);
        $enPassantPiece = $board->getPieceByPosition($enPassantPosition);
        $board->deletePiece($enPassantPiece);
        $piece->setPosition($to);
        $this->formNotation($from, $to, $piece, NotationNotes::EnPassant, $board);
    }

    private function doDidDoneAttack(Position $from, Position $to, Piece $piece, Piece $otherPiece, Board $board): void
    {
        $board->deletePiece($otherPiece);
        $piece->setPosition($to);
        $this->formNotation($from, $to, $piece, NotationNotes::Capture, $board);
    }

    private function doDidDoneCastling(Position $from, Position $to, Piece $piece, Board $board): void
    {
        $condition = $to->getCondition();
        if ($piece->getName() === PieceName::King) {
            $castlingPartners = array_filter($board->getPiecesByName(PieceName::Rook),
                static function (Piece $rook) use ($piece, $condition): bool {
                    if ($rook->getColor() !== $piece->getColor()) {
                        return false;
                    }
                    $moves = $rook->getMovesByCondition($condition);
                    foreach ($moves as $move) {
                        if ($move->getCondition() === $condition) {
                            return true;
                        }
                    }
                    return false;
                });
            $castlingPartner = end($castlingPartners);
            $positionPartnerCastling = $castlingPartner->getMovesByCondition($condition)[0];
            $castlingPartner->setPosition($positionPartnerCastling);
            $piece->setPosition($to);
            $this->formNotation($from, $to, $piece, NotationNotes::ShortCastling, $board);
        }
    }

    private function formNotation(Position $from, Position $to, Piece $piece, NotationNotes $notation, Board $board): void
    {
        $board->getNotebook()->addNotation($piece, $from, $to, $notation);
    }
}