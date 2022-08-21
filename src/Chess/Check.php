<?php

namespace Chess;

use Chess\Pieces\Piece;

class Check
{
    private DefenseStatus $checkStatus = DefenseStatus::Off;
    private array $enemyList = [];
    private ?array $CheckMoves = [];

    public function setCheckMoves(?array $moves): void
    {
        $this->CheckMoves = $moves;
    }

    public function getCheckMoves(): ?array
    {
        return $this->CheckMoves;
    }

    public function setCheckOn(Piece $piece): void
    {
        $this->checkStatus = DefenseStatus::On;
        $this->enemyList[] = $piece;
    }

    public function setCheckOff(): void
    {
        $this->checkStatus = DefenseStatus::Off;
        $this->enemyList = [];
    }

    public function getCheckStatus(): DefenseStatus
    {
        return $this->checkStatus;
    }

    public function getEnemyList(): array
    {
        return $this->enemyList;
    }
}