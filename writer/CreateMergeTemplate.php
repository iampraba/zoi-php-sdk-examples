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


class CreateMergeTemplate {

    public static function initializeSdk() {
        $user = new UserSignature("john@zylker.com");
        $environment = DataCenter::setEnvironment("https://api.office-integrator.com", null, null, null);
        $apikey = new APIKey("2ae438cf864488657cc9754a27daa480", Constants::PARAMS);
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

    public static function execute() {
        // Initializing SDK once is enough. Calling here since code sample will be tested standalone.
        // You can place SDK initializer code in your application and call once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $templateParameters = new MailMergeTemplateParameters();

            // Either use URL as document source or attach the document in the request body using the below methods
            $templateParameters->setUrl("https://demo.office-integrator.com/zdocs/Graphic-Design-Proposal.docx");
            $templateParameters->setMergeDataJsonUrl("https://demo.office-integrator.com/data/candidates.json");

            // $fileName = "OfferLetter.zdoc";
            // $filePath = "./sample_documents/OfferLetter.zdoc";
            // $fileStream = file_get_contents($filePath);
            // $streamWrapper = new StreamWrapper($fileName, $fileStream, $filePath);
            // $streamWrapper = new StreamWrapper(null, null, $filePath);

            // $templateParameters->setDocument($streamWrapper);

            // $jsonFileName = "candidates.json";
            // $jsonFilePath = "./sample_documents/candidates.json";
            // $jsonFileStream = file_get_contents($jsonFilePath);
            // $jsonStreamWrapper = new StreamWrapper($jsonFileName, $jsonFileStream, $jsonFilePath);

            // $templateParameters->setMergeDataJsonContent($jsonStreamWrapper);

            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId("" . time());
            $documentInfo->setDocumentName("Graphic-Design-Proposal.docx");

            $templateParameters->setDocumentInfo($documentInfo);

            $userInfo = new UserInfo();

            $userInfo->setUserId("1000");
            $userInfo->setDisplayName("Prabakaran R");

            $templateParameters->setUserInfo($userInfo);

            $margin = new Margin();

            $margin->setTop("2in");
            $margin->setBottom("2in");
            $margin->setLeft("2in");
            $margin->setRight("2in");

            $documentDefaults = new DocumentDefaults();

            $documentDefaults->setFontName("Arial");
            $documentDefaults->setFontSize(12);
            $documentDefaults->setOrientation("landscape");
            $documentDefaults->setPaperSize("A4");
            $documentDefaults->setTrackChanges("enabled");
            $documentDefaults->setMargin($margin);

            $templateParameters->setDocumentDefaults($documentDefaults);

            $editorSettings = new EditorSettings();

            $editorSettings->setUnit("mm");
            $editorSettings->setLanguage("en");
            $editorSettings->setView("pageview");

            $templateParameters->setEditorSettings($editorSettings);

            $permissions = [
                "document.export" => true,
                "document.print" => false,
                "document.edit" => true,
                "review.comment" => false,
                "review.changes.resolve" => false,
                "collab.chat" => false,
                "document.pausecollaboration" => false,
                "document.fill" => false
            ];

            $templateParameters->setPermissions($permissions);

            $callbackSettings = new CallbackSettings();
            $saveUrlParams = [
                "auth_token" => "1234",
                "id" => "123131"
            ];

            $callbackSettings->setSaveUrlParams($saveUrlParams);

            $callbackSettings->setHttpMethodType("post");
            $callbackSettings->setRetries(1);
            $callbackSettings->setTimeout(100000);
            $callbackSettings->setSaveUrl("https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157a25e63fc4dfd4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286");
            $callbackSettings->setSaveFormat("pdf");

            $templateParameters->setCallbackSettings($callbackSettings);

            $responseObject = $sdkOperations->createMailMergeTemplate($templateParameters);

            if ($responseObject !== null) {
                // Get the status code from the response
                echo "\nStatus Code: " . $responseObject->statusCode;

                // Get the API response object from responseObject
                $writerResponseObject = $responseObject->object;

                if ($writerResponseObject !== null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateDocumentResponse) {
                        echo "\nDocument ID - " . $writerResponseObject->getDocumentId();
                        echo "\nDocument session ID - " . $writerResponseObject->getSessionId();
                        echo "\nDocument session URL - " . $writerResponseObject->getDocumentUrl();
                        echo "\nDocument save URL - " . $writerResponseObject->getSaveUrl();
                        echo "\nDocument delete URL - " . $writerResponseObject->getDocumentDeleteUrl();
                        echo "\nDocument session delete URL - " . $writerResponseObject->getSessionDeleteUrl();
                    } elseif ($writerResponseObject instanceof InvaildConfigurationException) {
                        echo "\nInvalid configuration exception. Exception JSON - " . json_encode($writerResponseObject);
                    } else {
                        echo "\nRequest not completed successfully";
                    }
                }
            }
        } catch (Exception $error) {
            echo "\nException while running sample code: " . $error->getMessage();
        }
    }
}

CreateMergeTemplate::execute();
