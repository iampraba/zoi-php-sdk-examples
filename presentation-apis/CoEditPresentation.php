<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\CallbackSettings;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\CreatePresentationParameters;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\officeintegrator\v1\ZohoShowEditorSettings;
use Exception;

class CoEditPresentation {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-show-co-edit-presentation-v1.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new CreatePresentationParameters();

            $url = 'https://demo.office-integrator.com/samples/show/Zoho_Show.pptx';
            $parameters->setUrl($url);

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Zoho_Show.pptx";
            // $parameters->setDocument(new StreamWrapper(null, null, $filePath));

            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId(strval(time()));
            $documentInfo->setDocumentName("New Presentation");

            $parameters->setDocumentInfo($documentInfo);

            $userInfo = new UserInfo();

            $userInfo->setUserId("100");
            $userInfo->setDisplayName("User 1");

            $parameters->setUserInfo($userInfo);

            $editorSettings = new ZohoShowEditorSettings();

            $editorSettings->setLanguage("en");

            $parameters->setEditorSettings($editorSettings);

            $permissions = array();

            $permissions["document.export"] = true;
            $permissions["document.print"] = false;
            $permissions["document.edit"] = true;

            $parameters->setPermissions($permissions);

            $callbackSettings = new CallbackSettings();
            $saveUrlParams = array();

            $saveUrlParams["param1"] = "value1";
            $saveUrlParams["param2"] = "value2";

            $callbackSettings->setSaveUrlParams($saveUrlParams);
            
            $saveUrlHeaders = array();

            $saveUrlHeaders["header1"] = "value1";
            $saveUrlHeaders["header2"] = "value2";

            //$callbackSettings->setSaveUrlHeaders($saveUrlHeaders);

            $callbackSettings->setRetries(1);
            $callbackSettings->setSaveFormat("pptx");
            $callbackSettings->setHttpMethodType("post");
            $callbackSettings->setTimeout(100000);
            $callbackSettings->setSaveUrl("https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157123434d4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286");

            $parameters->setCallbackSettings($callbackSettings);

            $responseObject = $sdkOperations->createPresentation($parameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateDocumentResponse) {
                        echo "\nPresentation ID - " . $writerResponseObject->getDocumentId() . "\n";
                        echo "\nPresentation Session 1 ID - " . $writerResponseObject->getSessionId() . "\n";
                        echo "\nPresentation Session 1 URL - " . $writerResponseObject->getDocumentUrl() . "\n";
                        echo "\nPresentation Session 1 save URL - " . $writerResponseObject->getSaveUrl() . "\n";
                        echo "\nPresentation delete URL - " . $writerResponseObject->getDocumentDeleteUrl() . "\n";
                        echo "\nPresentation Session 1 delete URL - " . $writerResponseObject->getSessionDeleteUrl() . "\n";
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


        # Optional Configuration - Add User meta in request to identify the user in document session
        $userInfo = new UserInfo();

        $userInfo->setUserId("101");
        $userInfo->setDisplayName("User 2");

        $parameters->setUserInfo($userInfo);

        $response = $sdkOperations->createPresentation($parameters);

        if($response != null)
        {
            //Get the status code from response
            echo("Status code " . $response->getStatusCode() . "\n");

            //Get object from response
            $responseHandler = $response->getObject();
            
            if($responseHandler instanceof CreateDocumentResponse)
            {
                echo("\nPresentation ID - " . $responseHandler->getDocumentId() . "\n");
                echo("\nPresentation Session 2 ID - " . $responseHandler->getSessionId() . "\n");
                echo("\nPresentation Session 2 URL - " . $responseHandler->getDocumentUrl() . "\n");
                echo("\nPresentation Session 2 Delete URL - " . $responseHandler->getSessionDeleteUrl() . "\n");
                echo("\nPresentation Session 2 Save URL - " . $responseHandler->getSaveUrl() . "\n");
                echo("\nPresentation Delete URL - " . $responseHandler->getDocumentDeleteUrl() . "\n");
            } elseif ($responseHandler instanceof InvalidConfigurationException) {
                echo "\nInvalid configuration exception." . "\n";
                echo "\nError Code - " . $responseHandler->getCode() . "\n";
                echo "\nError Message - " . $responseHandler->getMessage() . "\n";
                if ( $responseHandler->getKeyName() ) {
                    echo "\nError Key Name - " . $responseHandler->getKeyName() . "\n";
                }
                if ( $responseHandler->getParameterName() ) {
                    echo "\nError Parameter Name - " . $responseHandler->getParameterName() . "\n";
                }
            } else {
                echo "\nSession 2 Creation Request not completed successfully\n";
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

CoEditPresentation::execute();

