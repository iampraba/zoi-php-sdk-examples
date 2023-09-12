<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\officeintegrator\v1\CreateDocumentParameters;
use com\zoho\officeintegrator\v1\DocumentMeta;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class GetDocumentDetails {

    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $createDocumentParameters = new CreateDocumentParameters();

            $userInfo = new UserInfo();

            $userInfo->setUserId("1000");
            $userInfo->setDisplayName("Prabakaran R");

            $createDocumentParameters->setUserInfo($userInfo);

            echo "\nCreating a document to demonstrate get all document information api\n";

            $responseObject = $sdkOperations->createDocument($createDocumentParameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateDocumentResponse) {
                        $documentId = $writerResponseObject->getDocumentId();

                        echo "\nCreated Document ID : " . $documentId . "\n";
                        
                        $documentDetailsResponse = $sdkOperations->getDocumentInfo($documentId);

                        if ($documentDetailsResponse != null) {
                            // Get the status code from response
                            echo "\nStatus Code: " . $documentDetailsResponse->getStatusCode() . "\n";
            
                            // Get the api response object from responseObject
                            $documentDetailsResponseObj = $documentDetailsResponse->getObject();
            
                            if ($documentDetailsResponseObj != null) {
                                // Check if the expected CreateDocumentResponse instance is received
                                if ($documentDetailsResponseObj instanceof DocumentMeta) {            
                                    echo "\nDocument ID :  - " . $documentDetailsResponseObj->getDocumentId() . "\n";
                                    echo "\nDocument Name :  - " . $documentDetailsResponseObj->getDocumentName() . "\n";
                                    echo "\nDocument Type :  - " . $documentDetailsResponseObj->getDocumentType() . "\n";
                                    echo "\nDocument Collaborators Count :" . $documentDetailsResponseObj->getCollaboratorsCount() . "\n";
                                    echo "\nDocument Created Time :  - " . $documentDetailsResponseObj->getCreatedTime() . "\n";
                                    echo "\nDocument Created Timestamp :  - " . $documentDetailsResponseObj->getCreatedTimeMs() . "\n";
                                    echo "\nDocument Expiry Time :  - " . $documentDetailsResponseObj->getExpiresOn() . "\n";
                                    echo "\nDocument Expiry Timestamp :  - " . $documentDetailsResponseObj->getExpiresOnMs() . "\n";
                                } elseif ($documentDetailsResponseObj instanceof InvalidConfigurationException) {
                                    echo "\nInvalid configuration exception." . "\n";
                                    echo "\nError Code - " . $documentDetailsResponseObj->getCode() . "\n";
                                    echo "\nError Message - " . $documentDetailsResponseObj->getMessage() . "\n";
                                    if ( $documentDetailsResponseObj->getKeyName() ) {
                                        echo "\nError Key Name - " . $documentDetailsResponseObj->getKeyName() . "\n";
                                    }
                                    if ( $documentDetailsResponseObj->getParameterName() ) {
                                        echo "\nError Parameter Name - " . $documentDetailsResponseObj->getParameterName() . "\n";
                                    }
                                } else {
                                    echo "\nRequest not completed successfully\n";
                                }
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
        } catch (Exception $error) {
            echo "\nException while running sample code: " . $error . "\n";
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

GetDocumentDetails::execute();

