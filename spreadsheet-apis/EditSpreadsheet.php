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
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
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
            // $parameters->setDocument(new StreamWrapper(null, null, $filePath));

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
                $spreadsheetResponseObject = $responseObject->getObject();

                if ($spreadsheetResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($spreadsheetResponseObject instanceof CreateSheetResponse) {
                        echo "\nDocument ID - " . $spreadsheetResponseObject->getDocumentId() . "\n";
                        echo "\nDocument session ID - " . $spreadsheetResponseObject->getSessionId() . "\n";
                        echo "\nDocument session URL - " . $spreadsheetResponseObject->getDocumentUrl() . "\n";
                        echo "\nDocument session grid view URL - " . $spreadsheetResponseObject->getDocumentUrl() . "\n";
                        echo "\nDocument save URL - " . $spreadsheetResponseObject->getSaveUrl() . "\n";
                        echo "\nDocument delete URL - " . $spreadsheetResponseObject->getDocumentDeleteUrl() . "\n";
                        echo "\nDocument session delete URL - " . $spreadsheetResponseObject->getSessionDeleteUrl() . "\n";
                    } elseif ($spreadsheetResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception." . "\n";
                        echo "\nError Code - " . $spreadsheetResponseObject->getCode() . "\n";
                        echo "\nError Message - " . $spreadsheetResponseObject->getMessage() . "\n";
                        if ( $spreadsheetResponseObject->getKeyName() ) {
                            echo "\nError Key Name - " . $spreadsheetResponseObject->getKeyName() . "\n";
                        }
                        if ( $spreadsheetResponseObject->getParameterName() ) {
                            echo "\nError Parameter Name - " . $spreadsheetResponseObject->getParameterName() . "\n";
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

EditSpreadsheet::execute();

