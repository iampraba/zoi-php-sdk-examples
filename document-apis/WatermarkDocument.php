<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\apiserver\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\util\StreamWrapper;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\FileBodyWrapper;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\officeintegrator\v1\WatermarkParameters;
use com\zoho\officeintegrator\v1\WatermarkSettings;


class WatermarkDocument {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/watermark-with-text.html
    public static function execute() {
        
        //Initializing SDK once is enough. Calling here since code sample will be tested standalone. 
        //You can place SDK initializer code in you application and call once while your application start-up. 
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $watermarkParameter = new WatermarkParameters();

            $watermarkParameter->setUrl("https://demo.office-integrator.com/zdocs/Graphic-Design-Proposal.docx");

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Graphic-Design-Proposal.docx";
            // $watermarkParameter->setDocument(new StreamWrapper(null, null, $filePath));

            $watermarkSettings = new WatermarkSettings();

            $watermarkSettings->setType("text");
            $watermarkSettings->getFontSize(18);
            $watermarkSettings->setOpacity(70.00);
            $watermarkSettings->setFontName("Arial");
            $watermarkSettings->setFontColor("#cd4544");
            $watermarkSettings->setOrientation("horizontal");
            $watermarkSettings->setText("Sample Water Mark Text");

            $watermarkParameter->setWatermarkSettings($watermarkSettings);

            $responseObject = $sdkOperations->createWatermarkDocument($watermarkParameter);

            if ($responseObject !== null) {
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject !== null) {
                    if ($writerResponseObject instanceof FileBodyWrapper) {
                        $convertedDocument = $writerResponseObject->getFile();

                        if ($convertedDocument instanceof StreamWrapper) {
                            $outputFilePath = __DIR__ . "/sample_documents/WaterMark_Output.docx";

                            file_put_contents($outputFilePath, $convertedDocument->getStream());
                            echo "\nWater mark output file in file path - $outputFilePath\n";
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
            echo "\nException while running sample code: " . $error;
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

WatermarkDocument::execute();