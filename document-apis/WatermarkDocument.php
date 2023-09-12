<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\FileBodyWrapper;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\PreviewDocumentInfo;
use com\zoho\officeintegrator\v1\PreviewParameters;
use com\zoho\officeintegrator\v1\PreviewResponse;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\officeintegrator\v1\WatermarkParameters;
use com\zoho\officeintegrator\v1\WatermarkSettings;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;

class WatermarkDocument {

    public static function execute() {
        
        //Initializing SDK once is enough. Calling here since code sample will be tested standalone. 
        //You can place SDK initializer code in you application and call once while your application start-up. 
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $watermarkParameter = new WatermarkParameters();

            $watermarkParameter->setUrl("https://demo.office-integrator.com/zdocs/Graphic-Design-Proposal.docx");

            // $fileName = "Graphic-Design-Proposal.docx";
            // $filePath = __DIR__ . "/sample_documents/Graphic-Design-Proposal.docx";
            // $fileStream = file_get_contents($filePath);
            // $streamWrapper = new StreamWrapper($fileName, $fileStream, $filePath);
            // $previewParameters->setDocument($streamWrapper);

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

WatermarkDocument::execute();