<?php
declare(strict_types=1);

const FILES_NUM = 10;
const ROWS_NUM = 10000;


/**
 * @param int $fileCounter
 * @param int $rowCounter
 *
 * @return array
 * @throws Exception
 */
function generateRow(int $fileCounter, int $rowCounter): array
{
    static $id = 0;
    static $rowsWithCurrentIdCounter = 0;
    static $condition;

    if ($rowCounter === 0) {
        $id = 0;
        $rowsWithCurrentIdCounter = 0;
    }

    if ($rowsWithCurrentIdCounter === 0) {
        ++$id;
        $rowsWithCurrentIdCounter = random_int(5, 40);
        $condition = $rowsWithCurrentIdCounter . ' rows with this ID';
    } else {
        --$rowsWithCurrentIdCounter;
    }

    $price = random_int(10000, 200000);

    return [
        $fileCounter * ROWS_NUM * 100 + $id,
        'Name',
        $condition,
        'state',
        $price . 'RUB',
    ];
}


$csvDir = 'csv';

if (is_dir($csvDir)) {
    echo sprintf('Please, remove directory "%s".', $csvDir) . PHP_EOL;
}

if (!is_dir($csvDir) && !mkdir($csvDir) && !is_dir($csvDir)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $csvDir));
}

for ($fileCounter = 1; $fileCounter <= FILES_NUM; $fileCounter++) {
    $pathToFile = $csvDir . '/source' . $fileCounter . '.csv';

    $file = fopen($pathToFile, 'wb');

    for ($rowCounter = 0; $rowCounter < ROWS_NUM; $rowCounter++) {
        /** @noinspection PhpUnhandledExceptionInspection */
        $fields = generateRow($fileCounter, $rowCounter);
        fwrite($file, implode(';', $fields) . PHP_EOL);
    }

    fclose($file);
}
