<?php
declare(strict_types=1);

require_once 'ProcessCsvTask.php';
require_once 'BookingOffer.php';

const OFFERS_LIMIT_PER_ID = 20;
const TOTAL_OFFERS_LIMIT = 1000;
const THREADS_NUM = 4;

$pool = new Pool(THREADS_NUM);

$csvDir = 'csv';
$files = array_diff(scandir($csvDir), ['..', '.']);

$tasks = [];
foreach ($files as $fileName) {
    $pathToFile = $csvDir . '/' . $fileName;
    $task = new ProcessCsvTask(
        $pathToFile,
        OFFERS_LIMIT_PER_ID,
        TOTAL_OFFERS_LIMIT
    );
    $tasks[] = $task;
    $pool->submit($task);
}

$pool->shutdown();

$offers = [];
foreach ($tasks as $task) {
    $offers[] = $task->getBookingOffers();
}

$cheapestOffers = ProcessCsvTask::getTopCheapestOffers($offers, TOTAL_OFFERS_LIMIT);

$file = fopen('result.csv', 'wb');
foreach ($cheapestOffers as $offer) {
    $row = [
        $offer->getId(),
        $offer->getName(),
        $offer->getCondition(),
        $offer->getState(),
        // Intentionally hardcode currency for overall simplicity
        $offer->getPrice() . 'RUB',
    ];
    fwrite($file, implode(';', $row) . PHP_EOL);
}
fclose($file);

