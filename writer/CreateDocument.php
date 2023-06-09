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
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
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
    public static function initializeSdk() {
        $user = new UserSignature("john@zylker.com");
        $environment = DataCenter::setEnvironment("https://api.office-integrator.com", null, null, null);
        $apikey = new APIKey("2ae438cf864488657cc9754a27daa480", Constants::PARAMS);
        $logger = (new LogBuilder())
            ->level(Levels::INFO)
            ->filePath("./app.log")
            ->build();
        $initialize = (new InitializeBuilder())
            ->user($user)
            ->environment($environment)
            ->token($apikey)
            ->logger($logger)
            ->initialize();

        echo "SDK initialized successfully.\n";
    }

    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $createDocumentParameters = new CreateDocumentParameters();

            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId(strval(time()));
            $documentInfo->setDocumentName("New Document");

            $createDocumentParameters->setDocumentInfo($documentInfo);

            $userInfo = new UserInfo();

            $userInfo->setUserId("1000");
            $userInfo->setDisplayName("Prabakaran R");

            $createDocumentParameters->setUserInfo($userInfo);

            $margin = new Margin();

            $margin->setTop("2in");
            $margin->setBottom("2in");
            $margin->setLeft("2in");
            $margin->setRight("2in");

            $documentDefaults = new DocumentDefaults();

            $documentDefaults->setFontSize(12);
            $documentDefaults->setPaperSize("A4");
            $documentDefaults->setFontName("Arial");
            $documentDefaults->setTrackChanges("enabled");
            $documentDefaults->setOrientation("landscape");

            $documentDefaults->setMargin($margin);
            $documentDefaults->setLanguage("ta");

            $createDocumentParameters->setDocumentDefaults($documentDefaults);

            $editorSettings = new EditorSettings();

            $editorSettings->setUnit("in");
            $editorSettings->setLanguage("en");
            $editorSettings->setView("pageview");

            $createDocumentParameters->setEditorSettings($editorSettings);

            $uiOptions = new UiOptions();

            $uiOptions->setDarkMode("show");
            $uiOptions->setFileMenu("show");
            $uiOptions->setSaveButton("show");
            $uiOptions->setChatPanel("show");

            $createDocumentParameters->setUiOptions($uiOptions);

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

            $callbackSettings = new CallbackSettings();
            $saveUrlParams = array();

            $saveUrlParams["auth_token"] = "1234";
            $saveUrlParams["id"] = "123131";

            $callbackSettings->setSaveUrlParams($saveUrlParams);
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
                        echo "\nInvalid configuration exception. Exception json - " . $writerResponseObject . "\n";
                    } else {
                        echo "\nRequest not completed successfully\n";
                    }
                }
            }
        } catch (Exception $error) {
            echo "\nException while running sample code: " . $error . "\n";
        }
    }
}

CreateDocument::execute();

