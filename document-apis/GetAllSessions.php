<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\AllSessionsResponse;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\officeintegrator\v1\CreateDocumentParameters;
use com\zoho\officeintegrator\v1\SessionInfo;
use com\zoho\officeintegrator\v1\SessionMeta;
use com\zoho\officeintegrator\v1\SessionUserInfo;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class GetAllSessions {

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

            echo "\nCreating a document to demonstrate get all document session information api";

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
                        
                        $allSessionsResponse = $sdkOperations->getAllSessions($documentId);

                        if ($allSessionsResponse != null) {
                            // Get the status code from response
                            echo "\nStatus Code: " . $allSessionsResponse->getStatusCode() . "\n";
            
                            // Get the api response object from responseObject
                            $allSessionsResponseObj = $allSessionsResponse->getObject();
            
                            if ($allSessionsResponseObj != null) {
                                // Check if the expected CreateDocumentResponse instance is received
                                if ($allSessionsResponseObj instanceof AllSessionsResponse) {            
                                    echo "\nDocument ID :  - " . $allSessionsResponseObj->getDocumentId() . "\n";
                                    echo "\nDocument Name :  - " . $allSessionsResponseObj->getDocumentName() . "\n";
                                    echo "\nDocument Type :  - " . $allSessionsResponseObj->getDocumentType() . "\n";
                                    echo "\nDocument Created Time :  - " . $allSessionsResponseObj->getCreatedTime() . "\n";
                                    echo "\nDocument Created Timestamp :  - " . $allSessionsResponseObj->getCreatedTimeMs() . "\n";
                                    echo "\nDocument Expiry Time :  - " . $allSessionsResponseObj->getExpiresOn() . "\n";
                                    echo "\nDocument Expiry Timestamp :  - " . $allSessionsResponseObj->getExpiresOnMs() . "\n";

                                    $sessions = $allSessionsResponseObj->getSessions();

                                    echo "\n---- Document Session Details ----\n";

                                    foreach ($sessions as $session) {
                                        if ($session instanceof SessionMeta) {
                                            echo "Session Status : " . $session->getStatus();

                                            $sessionInfo = $session->getInfo();

                                            if ($sessionInfo instanceof SessionInfo) {
                                                echo "\nSession Status : " . $sessionInfo->getSessionUrl();
                                                echo "\nSession Session - Document ID  : " . $sessionInfo->getDocumentId();
                                                echo "\nSession Session URL : " . $sessionInfo->getSessionUrl();
                                                echo "\nSession Session Delete URL : " . $sessionInfo->getSessionDeleteUrl();
                                                echo "\nSession Session Created Time : " . $sessionInfo->getCreatedTime();
                                                echo "\nSession Session Created Timestamp  : " . $sessionInfo->getCreatedTimeMs();
                                                echo "\nSession Session Expiry Time  : " . $sessionInfo->getExpiresOn();
                                                echo "\nSession Session Expiry Timestamp  : " . $sessionInfo->getExpiresOnMs();
                                            }
                                            $sessionUserInfo = $session->getUserInfo();

                                            if ($sessionUserInfo instanceof SessionUserInfo) {
                                                echo "\nSession User ID : " . $sessionUserInfo->getUserId();
                                                echo "\nSession User DisplayName  : " . $sessionUserInfo->getDisplayName();
                                            }
                                        }
                                    }

                                } elseif ($allSessionsResponseObj instanceof InvalidConfigurationException) {
                                    echo "\nInvalid configuration exception." . "\n";
                                    echo "\nError Code - " . $allSessionsResponseObj->getCode() . "\n";
                                    echo "\nError Message - " . $allSessionsResponseObj->getMessage() . "\n";
                                    if ( $allSessionsResponseObj->getKeyName() ) {
                                        echo "\nError Key Name - " . $allSessionsResponseObj->getKeyName() . "\n";
                                    }
                                    if ( $allSessionsResponseObj->getParameterName() ) {
                                        echo "\nError Parameter Name - " . $allSessionsResponseObj->getParameterName() . "\n";
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

GetAllSessions::execute();

