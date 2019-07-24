<?php
declare(strict_types=1);

/**
 * Class ProcessCsvTask
 */
class ProcessCsvTask extends Threaded
{
    private const BOOKING_OFFERS = 'booking_offers';

    /** @var string */
    private $pathToFile;

    /** @var int */
    private $offersLimitPerId;

    /** @var int */
    private $totalOffersLimit;

    /** @var Threaded */
    private $result;

    /**
     * ProcessCsvTask constructor.
     *
     * @param string $pathToFile
     * @param int    $offersLimitPerId
     * @param int    $totalOffersLimit
     */
    public function __construct(
        string $pathToFile,
        int $offersLimitPerId,
        int $totalOffersLimit
    )
    {
        $this->pathToFile = $pathToFile;
        $this->offersLimitPerId = $offersLimitPerId;
        $this->totalOffersLimit = $totalOffersLimit;
        $this->result = new Threaded();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $bookingOffers = [];

        $this->readBookingOffersFromFile($bookingOffers);
        $this->sortAndTruncateBookingOffers($bookingOffers);

        $cheapestOffers = self::getTopCheapestOffers($bookingOffers, $this->totalOffersLimit);

        /** @noinspection UnnecessaryCastingInspection */
        $this->result[self::BOOKING_OFFERS] = (array)$cheapestOffers;
    }

    /**
     * @param array $bookingOffers
     */
    private function readBookingOffersFromFile(array &$bookingOffers): void
    {
        $file = fopen($this->pathToFile, 'rb');

        while (($row = fgets($file)) !== false) {
            $fields = explode(';', $row);

            $id = (int)$fields[0];

            $bookingOffers[$id][] = new BookingOffer(
                $id,
                $fields[1],
                $fields[2],
                $fields[3],
                // Intentionally omit currency for overall simplicity
                (int)$fields[4],
            );
        }

        fclose($file);
    }

    /**
     * @param array $bookingOffers [id => BookingOffer[]]
     */
    private function sortAndTruncateBookingOffers(array &$bookingOffers): void
    {
        foreach ($bookingOffers as $id => &$offers) {
            usort($offers, static function (BookingOffer $a, BookingOffer $b) {
                return self::compareBookingOffers($a, $b);
            });

            $offers = array_splice($offers, 0, $this->offersLimitPerId);
        }
    }

    /**
     * @param BookingOffer $a
     * @param BookingOffer $b
     *
     * @return int
     */
    private static function compareBookingOffers(BookingOffer $a, BookingOffer $b): int
    {
        return [$a->getPrice(), $a->getId()] <=> [$b->getPrice(), $b->getId()];
    }

    /**
     * @param array $bookingOffers [id => BookingOffer[]]
     * @param int   $totalOffersLimit
     *
     * @return BookingOffer[]
     */
    public static function getTopCheapestOffers(array &$bookingOffers, int $totalOffersLimit): array
    {
        $cheapest = array_merge(...$bookingOffers);

        usort($cheapest, static function (BookingOffer $a, BookingOffer $b) {
            return self::compareBookingOffers($a, $b);
        });

        $cheapest = array_splice($cheapest, 0, $totalOffersLimit);

        return $cheapest;
    }

    /**
     * @return array
     */
    public function getBookingOffers(): array
    {
        return $this->result[self::BOOKING_OFFERS];
    }
}
