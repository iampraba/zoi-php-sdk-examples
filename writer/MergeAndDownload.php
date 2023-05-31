<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once '../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\FileBodyWrapper;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\MergeAndDownloadDocumentParameters;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;

class MergeAndDownload
{
    // Include zoi-nodejs-sdk package in your package.json and then execute this code.

    public static function initializeSdk() {
        $user = new UserSignature("john@zylker.com");
        $environment = DataCenter::setEnvironment("https://api.office-integrator.com", null, null, null);
        $apikey = new APIKey("2ae438cf864488657cc9754a27daa480", Constants::PARAMS);
        $logger = (new LogBuilder())
            ->level(Levels::INFO)
            ->filePath("./app.log")
            ->build();
        $initialize = (new InitializeBuilder())
            ->user($user)
            ->environment($environment)
            ->token($apikey)
            ->logger($logger)
            ->initialize();

        echo "SDK initialized successfully.\n";
    }

    public static function execute()
    {
        // Initializing SDK once is enough. Calling here since code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once during your application start-up. 
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new MergeAndDownloadDocumentParameters();

            $parameters->setFileUrl("https://demo.office-integrator.com/zdocs/OfferLetter.zdoc");
            $parameters->setMergeDataJsonUrl("https://demo.office-integrator.com/data/candidates.json");

            // $fileName = "OfferLetter.zdoc";
            // $filePath = __DIR__ . "/sample_documents/OfferLetter.zdoc";
            // $fileStream = file_get_contents($filePath);
            // $streamWrapper = new StreamWrapper($fileName, $fileStream, $filePath);
            
            $parameters->setPassword("***");
            $parameters->setOutputFormat("pdf");
            // $parameters->setFileContent($streamWrapper);

            // $jsonFileName = "candidates.json";
            // $jsonFilePath = __DIR__ . "/sample_documents/candidates.json";
            // $jsonFileStream = file_get_contents($jsonFilePath);
            // $jsonStreamWrapper = new StreamWrapper($jsonFileName, $jsonFileStream, $jsonFilePath);

            // $parameters->setMergeDataJsonContent($jsonStreamWrapper);

            /*
            $mergeData = new Map();

            $parameters->setMergeData($mergeData);

            $csvFileName = "csv_data_source.csv";
            $csvFilePath = __DIR__ . "/sample_documents/csv_data_source.csv";
            $csvFileStream = file_get_contents($csvFilePath);
            $csvStreamWrapper = new StreamWrapper($csvFileName, $csvFileStream, $csvFilePath);

            $parameters->setMergeDataCsvContent($csvStreamWrapper);

            $parameters->setMergeDataCsvUrl("https://demo.office-integrator.com/data/csv_data_source.csv");
            $parameters->setMergeDataJsonUrl("https://demo.office-integrator.com/zdocs/json_data_source.json");
            */

            $responseObject = $sdkOperations->mergeAndDownloadDocument($parameters);

            if ($responseObject !== null) {
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject !== null) {
                    if ($writerResponseObject instanceof FileBodyWrapper) {
                        $convertedDocument = $writerResponseObject->getFile();

                        if ($convertedDocument instanceof StreamWrapper) {
                            $outputFilePath = __DIR__ . "/sample_documents/merge_and_download.pdf";

                            file_put_contents($outputFilePath, $convertedDocument->getStream());
                            echo "\nCheck merged output file in file path - $outputFilePath\n";
                        }
                    } elseif ($writerResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception. Exception json - " . json_encode($writerResponseObject) . "\n";
                    } else {
                        echo "\nRequest not completed successfully\n";
                    }
                }
            }
        } catch (\Exception $error) {
            echo "\nException while running sample code: " . $error->getMessage() . "\n";
        }
    }
}

MergeAndDownload::execute();
