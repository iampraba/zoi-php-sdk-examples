<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\officeintegrator\v1\CreateSheetParameters;
use com\zoho\officeintegrator\v1\CreateSheetResponse;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\SheetCallbackSettings;
use com\zoho\officeintegrator\v1\SheetEditorSettings;
use com\zoho\officeintegrator\v1\SheetUiOptions;
use com\zoho\officeintegrator\v1\SheetUserSettings;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class EditSpreadsheet {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-sheet-edit-spreadsheet-v1.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new CreateSheetParameters();

            $parameters->setUrl('https://demo.office-integrator.com/samples/sheet/Contact_List.xlsx');

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Contact_List.xlsx";
            // $createDocumentParameters->setDocument(new StreamWrapper(null, null, $filePath));

            # Optional Configuration
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

            $callbackSettings->setSaveUrlParams($saveUrlParams);
            
            $saveUrlHeaders = array();

            $saveUrlHeaders["header1"] = "value1";
            $saveUrlHeaders["header2"] = "value2";

            $callbackSettings->setSaveUrlHeaders($saveUrlHeaders);

            $callbackSettings->setSaveFormat("xlsx");
            $callbackSettings->setSaveUrl("https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157123434d4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286");

            $parameters->setCallbackSettings($callbackSettings);

            $responseObject = $sdkOperations->createSheet($parameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateSheetResponse) {
                        echo "\nDocument ID - " . $writerResponseObject->getDocumentId() . "\n";
                        echo "\nDocument session ID - " . $writerResponseObject->getSessionId() . "\n";
                        echo "\nDocument session URL - " . $writerResponseObject->getDocumentUrl() . "\n";
                        echo "\nDocument session grid view URL - " . $writerResponseObject->getDocumentUrl() . "\n";
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

EditSpreadsheet::execute();

