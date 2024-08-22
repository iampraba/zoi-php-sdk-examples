<?php

namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';


use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\apiserver\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\util\StreamWrapper;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\CallbackSettings;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\DocumentDefaults;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\EditorSettings;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\MailMergeTemplateParameters;
use com\zoho\officeintegrator\v1\Margin;
use com\zoho\officeintegrator\v1\UiOptions;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;


class CreateMergeTemplate {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/create-template.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since code sample will be tested standalone.
        // You can place SDK initializer code in your application and call once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $templateParameters = new MailMergeTemplateParameters();

            // Either use URL as document source or attach the document in the request body using the below methods
            $templateParameters->setUrl("https://demo.office-integrator.com/zdocs/OfferLetter.zdoc");

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "OfferLetter.zdoc";
            // $templateParameters->setDocument(new StreamWrapper(null, null, $filePath));

            $templateParameters->setMergeDataJsonUrl('https://demo.office-integrator.com/data/candidates.json');

            // $jsonFilePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "candidates.json";
            // $templateParameters->setMergeDataJsonContent(new StreamWrapper(null, null, $jsonFilePath));

            # Optional Configuration - Add document meta in request to identify the file in Zoho Server
            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId("" . time());
            $documentInfo->setDocumentName("OfferLetter.zdoc");

            $templateParameters->setDocumentInfo($documentInfo);

            # Optional Configuration - Add User meta in request to identify the user in document session
            $userInfo = new UserInfo();

            $userInfo->setUserId("1000");
            $userInfo->setDisplayName("Prabakaran R");

            $templateParameters->setUserInfo($userInfo);

            # Optional Configuration
            $margin = new Margin();

            $margin->setTop("2in");
            $margin->setBottom("2in");
            $margin->setLeft("2in");
            $margin->setRight("2in");

            # Optional Configuration
            $documentDefaults = new DocumentDefaults();

            $documentDefaults->setFontName("Arial");
            $documentDefaults->setFontSize(12);
            $documentDefaults->setOrientation("landscape");
            $documentDefaults->setPaperSize("A4");
            $documentDefaults->setTrackChanges("enabled");
            $documentDefaults->setMargin($margin);

            $templateParameters->setDocumentDefaults($documentDefaults);

            # Optional Configuration
            $editorSettings = new EditorSettings();

            $editorSettings->setUnit("mm");
            $editorSettings->setLanguage("en");
            $editorSettings->setView("pageview");

            $templateParameters->setEditorSettings($editorSettings);

            # Optional Configuration
            $uiOptions = new UiOptions();

            $uiOptions->setChatPanel("show");
            $uiOptions->setDarkMode("show");
            $uiOptions->setFileMenu("show");
            $uiOptions->setSaveButton("show");

            $templateParameters->setUiOptions($uiOptions);

            # Optional Configuration
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

            # Optional Configuration - Add callback settings to configure.
            # how file needs to be received while saving the document
            $callbackSettings = new CallbackSettings();

            # Optional Configuration - configure additional parameters
            # which can be received along with document while save callback
            $saveUrlParams = array();

            $saveUrlHeaders["param1"] = "value1";
            $saveUrlHeaders["param2"] = "value2";
            # Following $<> values will be replaced by actual value in callback request
            # To know more - https://www.zoho.com/officeintegrator/api/v1/zoho-writer-create-document.html#saveurl_params

            $callbackSettings->setSaveUrlParams($saveUrlParams);

            # Optional Configuration - configure additional headers
            # which can be received along with document while save callback
            $saveUrlHeaders = array();

            $saveUrlHeaders["header1"] = "value1";
            $saveUrlHeaders["header2"] = "value2";

            $callbackSettings->setSaveUrlHeaders($saveUrlHeaders);

            $callbackSettings->setHttpMethodType("post");
            $callbackSettings->setRetries(1);
            $callbackSettings->setTimeout(100000);
            $callbackSettings->setSaveUrl("https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157a25e63fc4dfd4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286");
            $callbackSettings->setSaveFormat("zdoc");

            $templateParameters->setCallbackSettings($callbackSettings);

            $responseObject = $sdkOperations->createMailMergeTemplate($templateParameters);

            if ($responseObject !== null) {
                // Get the status code from the response
                echo "\nStatus Code: " . $responseObject->getStatusCode();

                // Get the API response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject !== null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateDocumentResponse) {
                        echo "\nDocument ID - " . $writerResponseObject->getDocumentId();
                        echo "\nDocument session ID - " . $writerResponseObject->getSessionId();
                        echo "\nDocument session URL - " . $writerResponseObject->getDocumentUrl();
                        echo "\nDocument save URL - " . $writerResponseObject->getSaveUrl();
                        echo "\nDocument delete URL - " . $writerResponseObject->getDocumentDeleteUrl();
                        echo "\nDocument session delete URL - " . $writerResponseObject->getSessionDeleteUrl();
                    } elseif ($writerResponseObject instanceof InvalidConfigurationException) {
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

CreateMergeTemplate::execute();
