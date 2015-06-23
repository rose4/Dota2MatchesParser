<?php
require 'vendor/autoload.php';
$data = (new Gurzhii\D2Parser\Parsers\CSLoungeParser())->getStructuredDataSet();
$output = new \Gurzhii\D2Parser\Outputs\JsonOutput($data);
echo ($output->output());