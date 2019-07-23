<?php
declare(strict_types=1);

/**
 * Class BookingOffer
 */
class BookingOffer
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $condition;

    /** @var string */
    private $state;

    /** @var int */
    private $price;

    /**
     * Booking constructor.
     *
     * @param int    $id
     * @param string $name
     * @param string $condition
     * @param string $state
     * @param int    $price
     */
    public function __construct(
        int $id,
        string $name,
        string $condition,
        string $state,
        int $price
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->condition = $condition;
        $this->state = $state;
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }
}
