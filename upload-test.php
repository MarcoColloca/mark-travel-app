<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Configura il client S3 per Backblaze B2
$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => 'eu-central-003',
    'endpoint' => 'https://s3.eu-central-003.backblazeb2.com',
    'credentials' => [
        'key'    => '003778abb1e85280000000001',
        'secret' => 'K003AHW8pWirQ1kouveuOR9dhFAQ9ew',
    ],
    'use_path_style_endpoint' => true,
]);

try {
    $result = $s3Client->putObject([
        'Bucket' => 'travel-app-bucket',
        'Key'    => 'test-file.txt',
        'Body'   => 'Hello, this is a test file!',
    ]);
    echo "File caricato con successo: " . $result['ObjectURL'] . "\n";
} catch (AwsException $e) {
    echo "Errore durante il caricamento del file: " . $e->getMessage() . "\n";
}
