<?php

namespace Chess;

class GraphicSystem
{
    public function draw(Board $board): void
    {
//        system('clear');
//        $aliasesPiecesMap = [
//            "Pawn" => "p",
//            "King" => "k",
//            "Bishop" => "b",
//            "Queen" => "q",
//            "Knight" => "n",
//            "Rook" => "r",
//        ];
//        $line = [];
//        for($width = 7; $width >= 0; $width--) {
//            $line[] = ".";
//        }
//        for($height = 7; $height >= 0; $height--) {
//            $lines[] = $line;
//        }
//        foreach($board->getPieces() as $key => $piece) {
//            $name = $piece->getName()->name;
//            $position = $piece->getPosition();
//            if ($piece->getColor() === Color::White) {
//                $skin = strtoupper($aliasesPiecesMap[$name]);
//            } else {
//                $skin = $aliasesPiecesMap[$name];
//            }
//            $lines[$position->getYCoordinate()][$position->getXCoordinate()] = $skin;
//        }
//        $paintedBoard = "";
//        foreach($lines as $line) {
//            foreach($line as $symb) {
//                $paintedBoard .= $symb;
//                $paintedBoard .= " ";
//            }
//            $paintedBoard .= "\n";
//        }
//        echo $paintedBoard;
    }
}