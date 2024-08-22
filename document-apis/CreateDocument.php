<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';


use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\apiserver\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\CallbackSettings;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\CreateDocumentParameters;
use com\zoho\officeintegrator\v1\DocumentDefaults;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\EditorSettings;
use com\zoho\officeintegrator\v1\Margin;
use com\zoho\officeintegrator\v1\UiOptions;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class CreateDocument {

    // Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-writer-create-document.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $createDocumentParameters = new CreateDocumentParameters();

            # Optional Configuration - Add document meta in request to identify the file in Zoho Server
            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId(strval(time()));
            $documentInfo->setDocumentName("New Document");

            $createDocumentParameters->setDocumentInfo($documentInfo);

            # Optional Configuration - Add User meta in request to identify the user in document session
            $userInfo = new UserInfo();

            $userInfo->setUserId("1000");
            $userInfo->setDisplayName("Prabakaran R");

            $createDocumentParameters->setUserInfo($userInfo);

            # Optional Configuration 
            $margin = new Margin();

            $margin->setTop("2in");
            $margin->setBottom("2in");
            $margin->setLeft("2in");
            $margin->setRight("2in");

            # Optional Configuration 
            $documentDefaults = new DocumentDefaults();

            $documentDefaults->setFontSize(12);
            $documentDefaults->setPaperSize("A4");
            $documentDefaults->setFontName("Arial");
            $documentDefaults->setTrackChanges("enabled");
            $documentDefaults->setOrientation("landscape");

            $documentDefaults->setMargin($margin);
            $documentDefaults->setLanguage("ta");

            $createDocumentParameters->setDocumentDefaults($documentDefaults);

            # Optional Configuration 
            $editorSettings = new EditorSettings();

            $editorSettings->setUnit("in");
            $editorSettings->setLanguage("en");
            $editorSettings->setView("pageview");

            $createDocumentParameters->setEditorSettings($editorSettings);

            # Optional Configuration 
            $uiOptions = new UiOptions();

            $uiOptions->setDarkMode("show");
            $uiOptions->setFileMenu("show");
            $uiOptions->setSaveButton("show");
            $uiOptions->setChatPanel("show");

            $createDocumentParameters->setUiOptions($uiOptions);

            # Optional Configuration 
            $permissions = array();

            $permissions["document.export"] = true;
            $permissions["document.print"] = false;
            $permissions["document.edit"] = true;
            $permissions["review.comment"] = false;
            $permissions["review.changes.resolve"] = false;
            $permissions["collab.chat"] = false;
            $permissions["document.pausecollaboration"] = false;
            $permissions["document.fill"] = false;

            $createDocumentParameters->setPermissions($permissions);

            # Optional Configuration - Add callback settings to configure.
            # how file needs to be received while saving the document
            $callbackSettings = new CallbackSettings();

            # Optional Configuration - configure additional parameters
            # which can be received along with document while save callback
            $saveUrlParams = array();

            $saveUrlParams["param1"] = "value1";
            $saveUrlParams["param2"] = "value2";

            $callbackSettings->setSaveUrlParams($saveUrlParams);
            
            # Optional Configuration - configure additional headers
            # which could be received in callback request headers while saving document
            $saveUrlHeaders = array();

            $saveUrlHeaders["header1"] = "value1";
            $saveUrlHeaders["header2"] = "value2";

            $callbackSettings->setSaveUrlHeaders($saveUrlHeaders);

            $callbackSettings->setRetries(1);
            $callbackSettings->setSaveFormat("zdoc");
            $callbackSettings->setHttpMethodType("post");
            $callbackSettings->setTimeout(100000);
            $callbackSettings->setSaveUrl("https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157123434d4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286");

            $createDocumentParameters->setCallbackSettings($callbackSettings);

            $responseObject = $sdkOperations->createDocument($createDocumentParameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateDocumentResponse) {
                        echo "\nDocument ID - " . $writerResponseObject->getDocumentId() . "\n";
                        echo "\nDocument session ID - " . $writerResponseObject->getSessionId() . "\n";
                        echo "\nDocument session URL - " . $writerResponseObject->getDocumentUrl() . "\n";
                        echo "\nDocument save URL - " . $writerResponseObject->getSaveUrl() . "\n";
                        echo "\nDocument delete URL - " . $writerResponseObject->getDocumentDeleteUrl() . "\n";
                        echo "\nDocument session delete URL - " . $writerResponseObject->getSessionDeleteUrl() . "\n";
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

CreateDocument::execute();

