<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\CreateSheetParameters;
use com\zoho\officeintegrator\v1\CreateSheetResponse;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\SheetCallbackSettings;
use com\zoho\officeintegrator\v1\SheetEditorSettings;
use com\zoho\officeintegrator\v1\SheetUiOptions;
use com\zoho\officeintegrator\v1\SheetUserSettings;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;

class CoEditSpreadsheet {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-sheet-co-edit-spreadsheet-v1.html
    public static function execute() {
        self::initializeSdk();

        $sdkOperations = new V1Operations();
        $parameters = new CreateSheetParameters();

        $parameters->setUrl('https://demo.office-integrator.com/samples/sheet/Contact_List.xlsx');

        // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
        // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Contact_List.xlsx";
        // $createDocumentParameters->setDocument(new StreamWrapper(null, null, $filePath));

        $documentInfo = new DocumentInfo();

        // Time value used to generate a unique document every time. You can replace it based on your application.
        $documentInfo->setDocumentId(strval(time()));
        $documentInfo->setDocumentName("New Document");

        $parameters->setDocumentInfo($documentInfo);

        # Optional Configuration 
        $userInfo = new SheetUserSettings();

        $userInfo->setDisplayName("User 1");

        $parameters->setUserInfo($userInfo);

        # Optional Configuration
        $editorSettings = new SheetEditorSettings();

        $editorSettings->setLanguage("en");

        $parameters->setEditorSettings($editorSettings);

        # Optional Configuration
        $uiOptions = new SheetUiOptions();

        $uiOptions->setSaveButton("show");

        $parameters->setUiOptions($uiOptions);

        # Optional Configuration
        $permissions = array();

        $permissions["document.export"] = true;
        $permissions["document.print"] = false;
        $permissions["document.edit"] = true;

        $parameters->setPermissions($permissions);

        # Optional Configuration
        $callbackSettings = new SheetCallbackSettings();
        $saveUrlParams = array();

        $saveUrlParams["param1"] = "value1";
        $saveUrlParams["param2"] = "value2";

        # Optional Configuration - Add callback settings to configure.
        # how file needs to be received while saving the document
        $callbackSettings->setSaveUrlParams($saveUrlParams);
        
        $saveUrlHeaders = array();

        $saveUrlHeaders["header1"] = "value1";
        $saveUrlHeaders["header2"] = "value2";

        $callbackSettings->setSaveUrlHeaders($saveUrlHeaders);

        $callbackSettings->setSaveFormat("xlsx");
        $callbackSettings->setSaveUrl("https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157123434d4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286");

        $parameters->setCallbackSettings($callbackSettings);

        $response = $sdkOperations->createSheet($parameters);

        if($response != null)
        {
            //Get the status code from response
            echo("Status code " . $response->getStatusCode() . "\n");

            //Get object from response
            $responseHandler = $response->getObject();
            
            if($responseHandler instanceof CreateSheetResponse)
            {
                echo("\nSpreadsheet ID - " . $responseHandler->getDocumentId() . "\n");
                echo("Spreadsheet Session 1 ID - " . $responseHandler->getSessionId() . "\n");
                echo("Spreadsheet Session 1 URL - " . $responseHandler->getDocumentUrl() . "\n");
                echo("Spreadsheet Session 1 Delete URL - " . $responseHandler->getSessionDeleteUrl() . "\n");
                echo("Spreadsheet Save URL - " . $responseHandler->getSaveUrl() . "\n");
                echo("Spreadsheet Delete URL - " . $responseHandler->getDocumentDeleteUrl() . "\n");
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
        $userInfo = new SheetUserSettings();

        $userInfo->setDisplayName("User 2");

        $parameters->setUserInfo($userInfo);

        $response = $sdkOperations->createSheet($parameters);

        if($response != null)
        {
            //Get the status code from response
            echo("Status code " . $response->getStatusCode() . "\n");

            //Get object from response
            $responseHandler = $response->getObject();
            
            if($responseHandler instanceof CreateSheetResponse)
            {
                echo("\nSpreadsheet ID - " . $responseHandler->getDocumentId() . "\n");
                echo("Spreadsheet Session 2 ID - " . $responseHandler->getSessionId() . "\n");
                echo("Spreadsheet Session 2 URL - " . $responseHandler->getDocumentUrl() . "\n");
                echo("Spreadsheet Session 2 Delete URL - " . $responseHandler->getSessionDeleteUrl() . "\n");
                echo("Spreadsheet Save URL - " . $responseHandler->getSaveUrl() . "\n");
                echo("Spreadsheet Delete URL - " . $responseHandler->getDocumentDeleteUrl() . "\n");
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

CoEditSpreadsheet::execute();

?>