<?php
namespace com\zoho\officeintegrator\v1\writer;
use com\zoho\officeintegrator\v1\SheetPreviewResponse;

require_once dirname(__FILE__) . '/../vendor/autoload.php';


use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\apiserver\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\util\StreamWrapper;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\SheetPreviewParameters;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class PreviewSpreadsheet {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-sheet-preview-spreadsheet.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new SheetPreviewParameters();

            $parameters->setUrl('https://demo.office-integrator.com/samples/sheet/Contact_List.xlsx');

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Contact_List.xlsx";
            // $parameters->setDocument(new StreamWrapper(null, null, $filePath));

            $parameters->setLanguage("en");
            
            $permissions = array();

            $permissions["document.export"] = true;
            $permissions["document.print"] = false;

            $parameters->setPermissions($permissions);

            $responseObject = $sdkOperations->createSheetPreview($parameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $spreadsheetResponseObject = $responseObject->getObject();

                if ($spreadsheetResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($spreadsheetResponseObject instanceof SheetPreviewResponse) {
                        echo "\nSpreadsheet ID - " . $spreadsheetResponseObject->getDocumentId() . "\n";
                        echo "\nSpreadsheet session ID - " . $spreadsheetResponseObject->getSessionId() . "\n";
                        echo "\nSpreadsheet preview session URL - " . $spreadsheetResponseObject->getPreviewUrl() . "\n";
                        echo "\nSpreadsheet delete URL - " . $spreadsheetResponseObject->getDocumentDeleteUrl() . "\n";
                        echo "\nSpreadsheet session delete URL - " . $spreadsheetResponseObject->getSessionDeleteUrl() . "\n";
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

PreviewSpreadsheet::execute();

