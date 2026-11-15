<?php

declare(strict_types=1);

// Usage: php tools/check-coverage.php <clover.xml> <minimum line coverage in percent>

/** @var list<string> $arguments */
$arguments = $_SERVER['argv'];

if (count($arguments) !== 3 || !is_numeric($arguments[2])) {
    fwrite(STDERR, "Usage: php tools/check-coverage.php <clover.xml> <minimum percent>\n");
    exit(2);
}

$cloverFile = $arguments[1];
$minimum = (float) $arguments[2];

$xml = is_file($cloverFile) ? simplexml_load_file($cloverFile) : false;
if ($xml === false) {
    fwrite(STDERR, sprintf("Cannot read the Clover file %s\n", $cloverFile));
    exit(2);
}

$metrics = $xml->xpath('/coverage/project/metrics');
if ($metrics === null || $metrics === []) {
    fwrite(STDERR, sprintf("No project metrics in %s\n", $cloverFile));
    exit(2);
}

$statements = (int) $metrics[0]['statements'];
$covered = (int) $metrics[0]['coveredstatements'];
$percent = $statements === 0 ? 100.0 : $covered / $statements * 100;

printf("Line coverage: %.2f%% (%d of %d lines), minimum: %.2f%%\n", $percent, $covered, $statements, $minimum);

if ($percent < $minimum) {
    fwrite(STDERR, "Line coverage is below the minimum.\n");
    exit(1);
}
