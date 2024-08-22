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
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\CreatePresentationParameters;
use com\zoho\officeintegrator\v1\SessionInfo;
use com\zoho\officeintegrator\v1\SessionMeta;
use com\zoho\officeintegrator\v1\SessionUserInfo;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class GetSessionDetails {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-show-session-information.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new CreatePresentationParameters();

            $userInfo = new UserInfo();

            $userInfo->setUserId("1000");
            $userInfo->setDisplayName("Prabakaran R");

            $parameters->setUserInfo($userInfo);

            echo "\nCreating a presentation to demonstrate get document session information api";

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
                        
                        $sessionsResponse = $sdkOperations->getPresentationSession($sessionId);

                        if ($sessionsResponse != null) {
                            // Get the status code from response
                            echo "\nGet Session Details API Status Code: " . $sessionsResponse->getStatusCode() . "\n";
            
                            // Get the api response object from responseObject
                            $sessionsResponseObj = $sessionsResponse->getObject();
            
                            if ($sessionsResponseObj != null) {
                                // Check if the expected CreateDocumentResponse instance is received
                                if ($sessionsResponseObj instanceof SessionMeta) {            
                                    echo "Session Status : " . $sessionsResponseObj->getStatus();

                                        $sessionInfo = $sessionsResponseObj->getInfo();

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
                                        $sessionUserInfo = $sessionsResponseObj->getUserInfo();

                                        if ($sessionUserInfo instanceof SessionUserInfo) {
                                            echo "\nSession User ID : " . $sessionUserInfo->getUserId();
                                            echo "\nSession User DisplayName  : " . $sessionUserInfo->getDisplayName();
                                        }
                                } elseif ($sessionsResponseObj instanceof InvalidConfigurationException) {
                                    echo "\nInvalid configuration exception." . "\n";
                                    echo "\nError Code - " . $sessionsResponseObj->getCode() . "\n";
                                    echo "\nError Message - " . $sessionsResponseObj->getMessage() . "\n";
                                    if ( $sessionsResponseObj->getKeyName() ) {
                                        echo "\nError Key Name - " . $sessionsResponseObj->getKeyName() . "\n";
                                    }
                                    if ( $sessionsResponseObj->getParameterName() ) {
                                        echo "\nError Parameter Name - " . $sessionsResponseObj->getParameterName() . "\n";
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

GetSessionDetails::execute();

