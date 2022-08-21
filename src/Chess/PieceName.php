<?php
declare(strict_types=1);

namespace Chess;

Enum PieceName: int
{
    case Pawn = 1;
    case Knight = 2;
    case Bishop = 3;
    case Queen = 4;
    case Rook = 5;
    case King = 6;
}