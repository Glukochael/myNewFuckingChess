<?php

namespace Chess;

use Chess\Pieces\Piece;

class Mobility
{
    private MobilityStatus $status = MobilityStatus::Yes;
    private Piece $piece;

    public function setPieceInMobility(Piece $piece): void
    {
        $this->piece = $piece;
    }

    public function setMobilityOn(): void
    {
        $this->status = MobilityStatus::Yes;
    }

    public function setMobilityOff(): void
    {
        $this->status = MobilityStatus::No;
    }

    public function getStatus(): MobilityStatus
    {
        return $this->status;
    }

    public function getPiece(): Piece
    {
        return $this->piece;
    }
}