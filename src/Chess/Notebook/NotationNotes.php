<?php
declare(strict_types=1);

namespace Chess\Notebook;

Enum NotationNotes
{
    case Move;
    case Capture;
    case EnPassant;
    case ShortCastling;
    case LongCastling;
    case Check;
    case Checkmate;
    case Draw;
}