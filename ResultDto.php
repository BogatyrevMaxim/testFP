<?php

/**
 * Без геттеров, чтобы нигде не менялось значение
 * Class ResultDto
 */
class ResultDto
{
    /**
     * @var int|null
     */
    private $purse;

    /**
     * @var int|null
     */
    private $code;

    /**
     * @var float|null
     */
    private $sum;

    public function __construct(?int $purse, ?int $code, ?float $sum)
    {
        $this->purse = $purse;
        $this->code = $code;
        $this->sum = $sum;
    }

    /**
     * @return int|null
     */
    public function getPurse(): ?int
    {
        return $this->purse;
    }

    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @return float|null
     */
    public function getSum(): ?float
    {
        return $this->sum;
    }
}