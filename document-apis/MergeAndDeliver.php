<?php
namespace com\zoho\officeintegrator\v1\writer;
use com\zoho\officeintegrator\v1\MergeAndDeliverRecordsMeta;

require_once dirname(__FILE__) . '/../vendor/autoload.php';


use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\apiserver\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\util\StreamWrapper;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\MailMergeWebhookSettings;
use com\zoho\officeintegrator\v1\MergeAndDeliverViaWebhookParameters;
use com\zoho\officeintegrator\v1\MergeAndDeliverViaWebhookSuccessResponse;
use com\zoho\officeintegrator\v1\V1Operations;


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

            $parameters->setFileUrl("https://demo.office-integrator.com/zdocs/OfferLetter.zdoc");

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "OfferLetter.zdoc";
            // $parameters->setFileContent(new StreamWrapper(null, null, $filePath));

            $parameters->setMergeDataJsonUrl('https://demo.office-integrator.com/data/candidates.json');

            // $jsonFilePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "candidates.json";
            // $parameters->setMergeDataJsonContent(new StreamWrapper(null, null, $jsonFilePath));

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
                            if ($record instanceof MergeAndDeliverRecordsMeta) {
                                echo "\n\nRecord Name : " . $record->getName();
                                echo "\nRecord Email : " . $record->getEmail();
                                echo "\nRecord Status : " . $record->getStatus();
                                echo "\nRecord Download Link : " . $record->getDownloadLink();
                            }
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

        # Update the api domain based on in which data center user register your apikey
        # To know more - https://www.zoho.com/officeintegrator/api/v1/getting-started.html
        $environment = new Production("https://api.office-integrator.com");
        # User your apikey that you have in office integrator dashboard
        //Update this apikey with your own apikey signed up in office inetgrator service
        $authBuilder = new AuthBuilder();
        $authentication = new Authentication();
        $authBuilder->addParam("apikey", "2ae438cf864488657cc9754a27daa480");
        $authBuilder->authenticationSchema($authentication->getTokenFlow());
        $tokens = [ $authBuilder->build() ];

        # Configure a proper file path to write the sdk logs
        $logger = (new LogBuilder())
            ->level(Levels::INFO)
            ->filePath("./app.log")
            ->build();
        
        (new InitializeBuilder())
            ->environment($environment)
            ->tokens($tokens)
            ->logger($logger)
            ->initialize();

        echo "SDK initialized successfully.\n";
    }
}

MergeAndDeliver::execute();
