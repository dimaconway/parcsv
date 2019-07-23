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

    /** @var Threaded */
    private $result;

    /**
     * ProcessCsvTask constructor.
     *
     * @param string $pathToFile
     */
    public function __construct(string $pathToFile)
    {
        $this->pathToFile = $pathToFile;
        $this->result = new Threaded();
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $bookingOffers = [];

        $file = fopen($this->pathToFile, 'rb');

        while (($row = fgets($file)) !== false) {
            $fields = explode(';', $row);

            $id = (int)$fields[0];

            $bookingOffers[$id][] = new BookingOffer(
                $id,
                $fields[1],
                $fields[2],
                $fields[3],
                (int)$fields[4],
            );
        }

        fclose($file);

        /** @noinspection UnnecessaryCastingInspection */
        $this->result[self::BOOKING_OFFERS] = (array)$bookingOffers;
    }

    /**
     * @return array
     */
    public function getBookingOffers(): array
    {
        return $this->result[self::BOOKING_OFFERS];
    }
}
