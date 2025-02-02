<?php

$xml = simplexml_load_string(file_get_contents($argv[1]));

$metrics = $xml->project->metrics;
$coveredstatements = (int) $metrics['coveredstatements'];
$statements = (int) $metrics['statements'];

$percent = round($coveredstatements / $statements * 100, 2);
echo $percent;
