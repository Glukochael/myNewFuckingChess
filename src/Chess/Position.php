<?php
declare(strict_types=1);

namespace Chess;

class Position
{
    private int $x;
    private int $y;
    private Condition $condition;

    public function __construct(int $x, int $y, Condition $condition = Condition::Attack)
    {
        $this->x = $x;
        $this->y = $y;
        $this->condition = $condition;
    }

    public function additionPositions(Position $position): Position
    {
        return new Position($this->x + $position->x, $this->y + $position->y, $this->condition);
    }

    public function multiplicationPositions(int $multiplier): Position
    {
        return new Position($this->x * $multiplier, $this->y * $multiplier, $this->condition);
    }

    public function setCondition(Condition $condition): void
    {
        $this->condition = $condition;
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function getXCoordinate(): int
    {
        return $this->x;
    }

    public function getYCoordinate(): int
    {
        return $this->y;
    }

    public function equals(Position $position): bool
    {
        if ($this->x === $position->x and $this->y === $position->y) {
            return true;
        }
        return false;
    }

    public function substractionPositions(Position $position): Position
    {
        return new Position(($this->x - $position->x), ($this->y - $position->y));
    }

    public function getPositionShiftToOtherPosition(Position $position): Position
    {
        return new Position(
            $position->x - $this->x === 0 ? 0 : ($position->x - $this->x) / abs($position->x - $this->x),
            $position->y - $this->y === 0 ? 0 : ($position->y - $this->y) / abs($position->y - $this->y)
        );
    }
}