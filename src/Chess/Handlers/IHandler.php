<?php
declare(strict_types=1);

namespace Chess\Handlers;

use Chess\Board;
use Chess\Notebook\Notebook;
use Chess\Pieces\Piece;
use Chess\Position;
// На самом деле этот интерфейс - ничего не делает И ОН НЕ НУЖОН
Interface IHandler
{
    public function setMovablePositionsChecksDefenses(Piece $piece, Board $board);
}