<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\CompareDocumentParameters;
use com\zoho\officeintegrator\v1\CompareDocumentResponse;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;
use Exception;

class CompareDocument {

    //Refer Compare API documentation - https://www.zoho.com/officeintegrator/api/v1/writer-comparison-api.html
    public static function execute() {
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $compareDocumentParameters = new CompareDocumentParameters();

            //Documents that need to be compared need to be passed to below api's.
            $compareDocumentParameters->setUrl1("https://demo.office-integrator.com/zdocs/MS_Word_Document_v0.docx");
            $compareDocumentParameters->setUrl2("https://demo.office-integrator.com/zdocs/MS_Word_Document_v1.docx");

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            $file1Name = "MS_Word_Document_v0.docx";
            // $file1Path = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "MS_Word_Document_v0.docx";
            // $compareDocumentParameters->setDocument1(new StreamWrapper(null, null, $file1Path));

            $file2Name = "MS_Word_Document_v1.docx";
            // $file2Path = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "MS_Word_Document_v1.docx";
            // $compareDocumentParameters->setDocument2(new StreamWrapper(null, null, $file2Path));

            # Optional Configurations - To set language of the compare document user interface
            $compareDocumentParameters->setLang("en");
            $compareDocumentParameters->setTitle($file1Name . " vs " . $file2Name);

            $responseObject = $sdkOperations->compareDocument($compareDocumentParameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the API response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CompareDocumentResponse instance is received
                    if ($writerResponseObject instanceof CompareDocumentResponse) {
                        echo "\nCompare URL - " . $writerResponseObject->getCompareUrl() . "\n";
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
            echo "\nException while running sample code: " . $error->getMessage() . "\n";
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

CompareDocument::execute();

?>
