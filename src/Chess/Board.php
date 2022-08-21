<?php
declare(strict_types=1);

namespace Chess;
use Chess\Notebook\Notebook;
use Chess\Pieces\King;
use Chess\Pieces\Piece;

class Board
{
    /**
     * @var Piece[]
     */
    private array $pieces = [];
    private Notebook $notebook;

    public function __construct()
    {
        $this->notebook = new Notebook();
    }

    public function addPiece(Piece $piece): void
    {
        if (!$this->getPieceByPosition($piece->getPosition())) {
            $this->pieces[] = $piece;
        }
    }

    public function deletePiece(Piece $deletingPiece): void
    {
        $this->pieces = array_values(array_filter($this->pieces, static function (Piece $piece) use ($deletingPiece): bool {
           return !$piece->equal($deletingPiece);
        }));
    }

    public function getNotebook(): Notebook
    {
        return $this->notebook;
    }

    public function getPieces(): array
    {
        return $this->pieces;
    }

    public function getPiecesByName(PieceName $name): array
    {
        return array_filter($this->pieces, static function (Piece $piece) Use ($name) {
            return $piece->getName() === $name;
        });
    }

    public function getPiecesByColor(Color $color): array
    {
        return array_values(array_filter($this->pieces, static function (Piece $piece) use ($color): bool {
            return $piece->getColor() === $color;
        }));
    }

    public function getKingByColor(Color $color): Piece
    {
        $kings = array_values(array_filter($this->getPiecesByName(PieceName::King), static function (Piece $king) use ($color): bool {
            return $king->getColor() === $color;
        }));
        return end($kings);
    }

    public function getPieceByPosition(Position $position): ?Piece
    {
        foreach ($this->pieces as $piece) {
            if ($piece->getPosition()->equals($position)) {
                return $piece;
            }
        }
        return null;
    }

    public function sortPieces(): void
    {
        usort($this->pieces, function (Piece $a, Piece $b) {
            return $a->getName()->value <=> $b->getName()->value;
        });
    }

    public function setDefenceOff(): void
    {
        array_walk($this->pieces, function (Piece $piece) {
           $piece->setDefenseOff();
        });
    }

    public function setCheckOff(): void
    {
        $kings = $this->getPiecesByName(PieceName::King);
        array_walk($kings, function (King $king) {
            $king->getCheck()->setCheckOff();
        });
    }

    public function setMobilityOn(): void
    {
        array_walk($this->pieces, function (Piece $piece) {
            $piece->setMobilityOn();
        });
    }

    public function isRightCoordinates(Position $position): bool
    {
        $x = $position->getXCoordinate();
        $y = $position->getYCoordinate();
        return ($x >= 0 && $x < 8) && ($y >= 0 && $y < 8);
    }

    public function setPieces(array $pieces): void
    {
        $this->pieces = $pieces;
    }

    public function __toString(): string
    {
        system('clear');
        $aliasesPiecesMap = [
            "Pawn" => "p",
            "King" => "k",
            "Bishop" => "b",
            "Queen" => "q",
            "Knight" => "n",
            "Rook" => "r",
        ];
        $width = 8;
        $height = 8;
        $output = "";
        for ($i = 0; $i < $width; $i++) {
            $output .= abs($height - $i) . " ";
            for ($j = 0; $j < $height; $j++) {
                $_ = ".";
                if ($piece = $this->getPieceByPosition(new Position($j, $i))) {
                    $alias = $aliasesPiecesMap[$piece->getName()->name];
                    $_ = $piece->getColor() === Color::White
                        ? strtoupper($alias)
                        : $alias;
                }
                $output .= $_ . " ";
            }
            $output .= "\n";
        }
        return "\n" . $output . "  a b c d e f g h \n\n";
//        $line = [];
//        for($width = 7; $width >= 0; $width--) {
//            $line[] = ".";
//        }
//        for($height = 7; $height >= 0; $height--) {
//            $lines[] = $line;
//        }
//        foreach($this->pieces as $piece) {
//            $name = $piece->getName()->name;
//            $position = $piece->getPosition();
//            $alias = $aliasesPiecesMap[$name];
//            $lines[$position->getYCoordinate()][$position->getXCoordinate()] = $piece->getColor() === Color::White
//                ? strtoupper($alias)
//                : $alias;
//        }
//
//        return "\n" . implode("\n", array_map(static function (array $chars) {
//            return implode(" ", $chars);
//        }, $lines)) . "\n\n";
    }
}