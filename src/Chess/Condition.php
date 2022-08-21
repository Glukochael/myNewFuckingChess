<?php
declare(strict_types=1);

namespace Chess;

Enum Condition
{
    case Attack;
    case Move;
    case PassiveAttack;
    case EnPassant;
    case ShortCastling;
    case LongCastling;
}