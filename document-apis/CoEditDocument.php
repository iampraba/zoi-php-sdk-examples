<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\CallbackSettings;
use com\zoho\officeintegrator\v1\CreateDocumentParameters;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\DocumentDefaults;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\EditorSettings;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\UiOptions;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;

class CoEditDocument {

    //Refer Co-Edit API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-writer-co-edit-document.html
    public static function execute() {
        self::initializeSdk();

        $v1Operations = new V1Operations();

        $createDocumentParameters = new CreateDocumentParameters();

        $url = "https://demo.office-integrator.com/zdocs/Graphic-Design-Proposal.docx";

        $createDocumentParameters->setUrl($url);

        // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
        // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Graphic-Design-Proposal.docx";
        // $createDocumentParameters->setDocument(new StreamWrapper(null, null, $filePath));

        # Optional Configuration - Add document meta in request to identify the file in Zoho Server
        $documentInfo = new DocumentInfo();
        $currentTime = time();

        $documentInfo->setDocumentName("New Document");
        $documentInfo->setDocumentId($currentTime);
        
        $createDocumentParameters->setDocumentInfo($documentInfo);

        # Optional Configuration - Add User meta in request to identify the user in document session
        $userInfo = new UserInfo();

        $userInfo->setUserId(100);
        $userInfo->setDisplayName("User 1");

        $createDocumentParameters->setUserInfo($userInfo);

        # Optional Configuration - Set default settings for document while creating document itself.
        # It's applicable only for new documents.
        $documentDefaults = new DocumentDefaults();

        $documentDefaults->getTrackChanges("enabled");
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

        # Optional Configuration - To add additional custom parameter in save_url callback request
        $callbackSettings = new CallbackSettings();
        $saveUrlParams = array();

        $saveUrlParams["param1"] = "value1";
        $saveUrlParams["param2"] = "value2";

        $callbackSettings->setSaveUrlParams($saveUrlParams);
        
        # Optional Configuration - To add additional custom header in save_url callback request
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

        $response = $v1Operations->createDocument($createDocumentParameters);

        if($response != null)
        {
            //Get the status code from response
            echo("Status code " . $response->getStatusCode() . "\n");

            //Get object from response
            $responseHandler = $response->getObject();
            
            if($responseHandler instanceof CreateDocumentResponse)
            {
                echo("\nDocument ID - " . $responseHandler->getDocumentId() . "\n");
                echo("Document Session 1 ID - " . $responseHandler->getSessionId() . "\n");
                echo("Document Session 1 URL - " . $responseHandler->getDocumentUrl() . "\n");
                echo("Document Session 1 Delete URL - " . $responseHandler->getSessionDeleteUrl() . "\n");
                echo("Document Save URL - " . $responseHandler->getSaveUrl() . "\n");
                echo("Document Delete URL - " . $responseHandler->getDocumentDeleteUrl() . "\n");
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

        # Optional Configuration - Add User meta in request to identify the user in document session
        $userInfo = new UserInfo();

        $userInfo->setUserId(101);
        $userInfo->setDisplayName("User 2");

        $createDocumentParameters->setUserInfo($userInfo);

        $response = $v1Operations->createDocument($createDocumentParameters);

        if($response != null)
        {
            //Get the status code from response
            echo("Status code " . $response->getStatusCode() . "\n");

            //Get object from response
            $responseHandler = $response->getObject();
            
            if($responseHandler instanceof CreateDocumentResponse)
            {
                echo("\nDocument ID - " . $responseHandler->getDocumentId() . "\n");
                echo("Document Session 2 ID - " . $responseHandler->getSessionId() . "\n");
                echo("Document Session 2 URL - " . $responseHandler->getDocumentUrl() . "\n");
                echo("Document Session 2 Delete URL - " . $responseHandler->getSessionDeleteUrl() . "\n");
                echo("Document Save URL - " . $responseHandler->getSaveUrl() . "\n");
                echo("Document Delete URL - " . $responseHandler->getDocumentDeleteUrl() . "\n");
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

CoEditDocument::execute();

?>