<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\MailMergeWebhookSettings;
use com\zoho\officeintegrator\v1\MergeAndDeliverViaWebhookParameters;
use com\zoho\officeintegrator\v1\MergeAndDeliverViaWebhookSuccessResponse;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;

class MergeAndDeliver {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/merge-and-deliver-via-webhook.html
    public static function execute()
    {
        // Initializing SDK once is enough. Calling here since code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once during your application start-up. 
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new MergeAndDeliverViaWebhookParameters();

            $parameters->setFileUrl('https://demo.office-integrator.com/zdocs/OfferLetter.zdoc');
            $parameters->setMergeDataJsonUrl('https://demo.office-integrator.com/data/candidates.json');

            // $fileName = "OfferLetter.zdoc";
            // $filePath = __DIR__ . "/sample_documents/OfferLetter.zdoc";
            // $fileStream = file_get_contents($filePath);
            // $streamWrapper = new StreamWrapper($fileName, $fileStream, $filePath);
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

            $parameters->setPassword("***");
            $parameters->setOutputFormat("pdf");
            $parameters->setMergeTo('separatedoc');

            $webhookSettings = new MailMergeWebhookSettings();

            $webhookSettings->setInvokeUrl('https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157a25e63fc4dfd4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286');
            $webhookSettings->setInvokePeriod('oncomplete');

            $parameters->setWebhook($webhookSettings);

            $responseObject = $sdkOperations->mergeAndDeliverViaWebhook($parameters);

            if ($responseObject !== null) {
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject !== null) {
                    if ($writerResponseObject instanceof MergeAndDeliverViaWebhookSuccessResponse) {
                        $mergeReportUrl = $writerResponseObject->getMergeReportDataUrl();

                        echo "\nMerge Report URL - " . $mergeReportUrl . "\n";

                        $records = $writerResponseObject->getRecords();

                        foreach ( $records as $record ) {
                            echo "Records : " . $record;
                        }
                    } elseif ($writerResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception." . "\n";
                        echo "\nError Code - " . $writerResponseObject->getCode() . "\n";
                        echo "\nError Message - " . $writerResponseObject->getMessage() . "\n";
                        if ( $writerResponseObject->getKeyName() ) {
                            echo "\nError Key Name - " . $writerResponseObject->getKeyName() . "\n";
                        }
                        if ( $writerResponseObject->getParameterName() ) {
                            echo "\nError Parameter Name - " . $writerResponseObject->getParameterName() . "\n";
                        }
                    } else {
                        echo "\nRequest not completed successfully\n";
                    }
                }
            }
        } catch (\Exception $error) {
            echo "\nException while running sample code: " . $error->getMessage() . "\n";
        }
    }

    public static function initializeSdk() {
        // Replace email address associated with your apikey below
        $user = new UserSignature("john@zylker.com");
        # Update the api domain based on in which data center user register your apikey
        # To know more - https://www.zoho.com/officeintegrator/api/v1/getting-started.html
        $environment = DataCenter::setEnvironment("https://api.office-integrator.com", null, null, null);
        # User your apikey that you have in office integrator dashboard
        $apikey = new APIKey("2ae438cf864488657cc9754a27daa480", Constants::PARAMS);
        # Configure a proper file path to write the sdk logs
        $logger = (new LogBuilder())
            ->level(Levels::INFO)
            ->filePath("./app.log")
            ->build();
        
        (new InitializeBuilder())
            ->user($user)
            ->environment($environment)
            ->token($apikey)
            ->logger($logger)
            ->initialize();

        echo "SDK initialized successfully.\n";
    }
}

MergeAndDeliver::execute();
