<?php

namespace Chess\Parsers;

use Chess\Board;
use Chess\Color;
use Chess\PieceName;
use Chess\Pieces\King;
use Chess\Pieces\Piece;
use Chess\Position;

class FENParser
{
    public function setFENPositions(string $fen, Board $board): void
    {
        $board->setPieces([]);
        $aliasesPiecesMap = [
            "P" => PieceName::Pawn,
            "K" => PieceName::King,
            "B" => PieceName::Bishop,
            "Q" => PieceName::Queen,
            "N" => PieceName::Knight,
            "R" => PieceName::Rook,
        ];
        if (strpos($fen, " ")) {
            $fen = substr_replace($fen, '', strpos($fen, " "));
        }
        $lines = [];
        foreach (explode("/", $fen) as $line) {
            $lines[] = str_split($line);
        }
        foreach ($lines as $y => $line) {
            $shift = 0;
            foreach ($line as $x => $ch) {
                if (is_numeric($ch)) {
                    $shift += (int)$ch + 1;
                    continue;
                }
                if (strtoupper($ch) === "K") {
                    $board->addPiece(new King(strtoupper($ch) === $ch ? Color::White : Color::Black,
                        new Position($x + $shift, $y), $aliasesPiecesMap[strtoupper($ch)]));
                } else {
                    $board->addPiece(new Piece(strtoupper($ch) === $ch ? Color::White : Color::Black,
                        new Position($x + $shift, $y), $aliasesPiecesMap[strtoupper($ch)]));
                }
            }
        }
    }

    public function setStartPositions(Board $board): void
    {
        $this->setFENPositions("rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR", $board);
    }
}