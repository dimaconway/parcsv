<?php
declare(strict_types=1);

require_once 'ProcessCsvTask.php';
require_once 'BookingOffer.php';

const THREADS_NUM = 4;

$pool = new Pool(THREADS_NUM);

$csvDir = 'csv';
$files = array_diff(scandir($csvDir), ['..', '.']);

$tasks = [];
foreach ($files as $fileName) {
    $task = new ProcessCsvTask($csvDir . '/' . $fileName);
    $tasks[] = $task;
    $pool->submit($task);
}

$pool->shutdown();

foreach ($tasks as $task) {
    var_export($task->getBookingOffers());
}
