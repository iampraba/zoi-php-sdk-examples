<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\PreviewDocumentInfo;
use com\zoho\officeintegrator\v1\PreviewParameters;
use com\zoho\officeintegrator\v1\PreviewResponse;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;

class PreviewDocument {

    //Refer Preview API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-writer-preview-document.html
    public static function execute() {
        
        //Initializing SDK once is enough. Calling here since code sample will be tested standalone. 
        //You can place SDK initializer code in you application and call once while your application start-up. 
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $previewParameters = new PreviewParameters();

            $previewParameters->setUrl("https://demo.office-integrator.com/zdocs/Graphic-Design-Proposal.docx");

            // $fileName = "Graphic-Design-Proposal.docx";
            // $filePath = __DIR__ . "/sample_documents/Graphic-Design-Proposal.docx";
            // $fileStream = file_get_contents($filePath);
            // $streamWrapper = new StreamWrapper($fileName, $fileStream, $filePath);
            // $previewParameters->setDocument($streamWrapper);

            $previewDocumentInfo = new PreviewDocumentInfo();

            //Time value used to generate unique document everytime. You can replace based on your application.
            $previewDocumentInfo->setDocumentName("Graphic-Design-Proposal.docx");

            $previewParameters->setDocumentInfo($previewDocumentInfo);

            $permissions = array();

            //SDK-ERROR: Array value not sent properly
            $permissions["document.print"] = false;

            $previewParameters->setPermissions($permissions);

            $responseObject = $sdkOperations->createDocumentPreview($previewParameters);

            if ($responseObject != null) {
                //Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode();

                //Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    //Check if expected PreviewResponse instance is received
                    if ($writerResponseObject instanceof PreviewResponse) {
                        echo "\nDocument ID - " . $writerResponseObject->getDocumentId();
                        echo "\nDocument session ID - " . $writerResponseObject->getSessionId();
                        echo "\nDocument preview URL - " . $writerResponseObject->getPreviewUrl();
                        echo "\nDocument delete URL - " . $writerResponseObject->getDocumentDeleteUrl();
                        echo "\nDocument session delete URL - " . $writerResponseObject->getSessionDeleteUrl();
                    } else if ($writerResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception. Exception json - " . $writerResponseObject->getMessage();
                    } else {
                        echo "\nRequest not completed successfully";
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

PreviewDocument::execute();