<?php
declare(strict_types=1);

namespace Chess\Pieces;

use Chess\Mobility;
use Chess\MobilityStatus;
use Chess\Check;
use Chess\Condition;
use Chess\DefenseStatus;
use Chess\PieceName;
use Chess\Position;
use Chess\Color;

class Piece
{
    protected PieceName $name;
    protected Color $color;
    protected Position $position;
    protected DefenseStatus $defense = DefenseStatus::Off;
    protected Mobility $mobility;
    /**
     * @var Position[]
     */
    protected array $moves = [];

    public function __construct(Color $case, Position $position, PieceName $name)
    {
        $this->color = $case;
        $this->position = $position;
        $this->name = $name;
        $this->mobility = new Mobility();
    }

    public function getName(): PieceName
    {
        return $this->name;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function getAttackMoves(): array
    {
        return $this->moves;
    }

    public function getMovesByCondition(Condition $condition): array
    {
        return array_filter($this->getAttackMoves(), static function (Position $position) use ($condition) {
            return $condition === $position->getCondition();
        });
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getDefenseStatus(): DefenseStatus
    {
        return $this->defense;
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    public function setAttackMoves(array $moves): void
    {
        $this->moves = $moves;
    }

    public function setDefenseOn(): void
    {
        $this->defense = DefenseStatus::On;
    }

    public function setDefenseOff(): void
    {
        $this->defense = DefenseStatus::Off;
    }

    public function  getId(): string
    {
        return spl_object_hash($this);
    }

    public function equal(Piece $piece): bool
    {
        return $this->getId() === $piece->getId();
    }

    public function isProtected(): bool
    {
        return $this->defense === DefenseStatus::On;
    }

    public function canCapture(Piece $piece): bool
    {
        return $this->color !== $piece->getColor();
    }

    public function getMobility(): Mobility
    {
        return $this->mobility;
    }

    public function setMobilityOn(): void
    {
        $this->mobility->setMobilityOn();
    }

    public function setMobilityOff(Piece $piece): void
    {
        $this->mobility->setMobilityOff();
        $this->mobility->setPieceInMobility($piece);
    }
}