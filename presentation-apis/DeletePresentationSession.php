<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';


use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\apiserver\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\CreatePresentationParameters;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\SessionDeleteSuccessResponse;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class DeletePresentationSession {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-show-delete-user-session.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new CreatePresentationParameters();

            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId(strval(time()));
            $documentInfo->setDocumentName("New Presentation");

            $parameters->setDocumentInfo($documentInfo);

            $responseObject = $sdkOperations->createPresentation($parameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $presentationResponseObject = $responseObject->getObject();

                if ($presentationResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($presentationResponseObject instanceof CreateDocumentResponse) {
                        $sessionId = $presentationResponseObject->getSessionId();

                        echo "\nPresentation Session ID to be deleted - " . $sessionId . "\n";

                        $deleteApiResponse = $sdkOperations->deletePresentationSession($sessionId);

                        if ($deleteApiResponse != null) {
                            // Get the status code from response
                            echo "\nDelete API Response Status Code: " . $deleteApiResponse->getStatusCode() . "\n";
            
                            // Get the api response object from responseObject
                            $deleteResponseObject = $deleteApiResponse->getObject();
            
                            if ($deleteResponseObject != null) {
                                // Check if the expected CreateDocumentResponse instance is received
                                if ($deleteResponseObject instanceof SessionDeleteSuccessResponse) {            
                                    echo "\nPresentation session delete status :  - " . $deleteResponseObject->getSessionDelete() . "\n";
                                } elseif ($deleteResponseObject instanceof InvalidConfigurationException) {
                                    echo "\nInvalid configuration exception." . "\n";
                                    echo "\nError Code - " . $deleteResponseObject->getCode() . "\n";
                                    echo "\nError Message - " . $deleteResponseObject->getMessage() . "\n";
                                    if ( $deleteResponseObject->getKeyName() ) {
                                        echo "\nError Key Name - " . $deleteResponseObject->getKeyName() . "\n";
                                    }
                                    if ( $deleteResponseObject->getParameterName() ) {
                                        echo "\nError Parameter Name - " . $deleteResponseObject->getParameterName() . "\n";
                                    }
                                } else {
                                    echo "\nRequest not completed successfully\n";
                                }
                            }
                        }
                    } elseif ($presentationResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception." . "\n";
                        echo "\nError Code - " . $presentationResponseObject->getCode() . "\n";
                        echo "\nError Message - " . $presentationResponseObject->getMessage() . "\n";
                        if ( $presentationResponseObject->getKeyName() ) {
                            echo "\nError Key Name - " . $presentationResponseObject->getKeyName() . "\n";
                        }
                        if ( $presentationResponseObject->getParameterName() ) {
                            echo "\nError Parameter Name - " . $presentationResponseObject->getParameterName() . "\n";
                        }
                    } else {
                        echo "\nRequest not completed successfully\n";
                    }
                }
            }
        } catch (Exception $error) {
            echo "\nException while running sample code: " . $error . "\n";
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

DeletePresentationSession::execute();

