<?php
namespace com\zoho\officeintegrator\v1\writer;
use com\zoho\officeintegrator\v1\CreateSheetParameters;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\CreateSheetResponse;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\FileDeleteSuccessResponse;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class DeleteSpreadsheetSession {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-sheet-delete-user-session.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new CreateSheetParameters();

            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId(strval(time()));
            $documentInfo->setDocumentName("New Spreadsheet");

            $parameters->setDocumentInfo($documentInfo);

            $responseObject = $sdkOperations->createSheet($parameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateSheetResponse) {
                        $spreadsheetSessionId = $writerResponseObject->getSessionId();

                        echo "\nSpreadsheet Session ID to be deleted - " . $spreadsheetSessionId . "\n";

                        $deleteApiResponse = $sdkOperations->deleteSheetSession($spreadsheetSessionId);

                        if ($deleteApiResponse != null) {
                            // Get the status code from response
                            echo "\nDelete API Response Status Code: " . $deleteApiResponse->getStatusCode() . "\n";
            
                            // Get the api response object from responseObject
                            $deleteResponseObject = $deleteApiResponse->getObject();
            
                            if ($deleteResponseObject != null) {
                                // Check if the expected CreateDocumentResponse instance is received
                                if ($deleteResponseObject instanceof FileDeleteSuccessResponse) {            
                                    echo "\nSpreadsheet Session delete status :  - " . $deleteResponseObject->getDocDelete() . "\n";
                                } elseif ($deleteResponseObject instanceof InvalidConfigurationException) {
                                    echo "\nInvalid configuration exception." . "\n";
                                    echo "\nError Code - " . $deleteResponseObject->getCode() . "\n";
                                    echo "\nError Message - " . $deleteResponseObject->getMessage() . "\n";
                                    if ( $deleteResponseObject->getKeyName() ) {
                                        echo "\nError Key Name - " . $deleteResponseObject->getKeyName() . "\n";
                                    }
                                    if ( $deleteResponseObject->getParameterName() ) {
                                        echo "\nError Parameter Name - " . $deleteResponseObject->getParameterName() . "\n";
                                    }
                                } else {
                                    echo "\nRequest not completed successfully\n";
                                }
                            }
                        }
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

DeleteSpreadsheetSession::execute();

