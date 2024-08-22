<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\apiserver\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\util\StreamWrapper;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\PreviewDocumentInfo;
use com\zoho\officeintegrator\v1\PreviewParameters;
use com\zoho\officeintegrator\v1\PreviewResponse;
use com\zoho\officeintegrator\v1\V1Operations;


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

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Graphic-Design-Proposal.docx";
            // $previewParameters->setDocument(new StreamWrapper(null, null, $filePath));

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

PreviewDocument::execute();