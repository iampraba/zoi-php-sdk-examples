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
    public static function initializeSdk() {
        $user = new UserSignature("john@zylker.com");
        $environment = DataCenter::setEnvironment("https://api.office-integrator.com", null, null, null);
        $apiKey = new APIKey("2ae438cf864488657cc9754a27daa480", Constants::PARAMS);
        $logger = (new LogBuilder())
            ->level(Levels::INFO)
            ->filePath("./app.log")
            ->build();
        $initializeBuilder = (new InitializeBuilder())
            ->user($user)
            ->environment($environment)
            ->token($apiKey)
            ->logger($logger);
        $initializeBuilder->initialize();

        echo "\nSDK initialized successfully.\n";
    }

    public static function execute() {
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $compareDocumentParameters = new CompareDocumentParameters();

            $compareDocumentParameters->setUrl1("https://demo.office-integrator.com/zdocs/MS_Word_Document_v0.docx");
            $compareDocumentParameters->setUrl2("https://demo.office-integrator.com/zdocs/MS_Word_Document_v1.docx");

            $file1Name = "MS_Word_Document_v0.docx";
            // $file1Path = __DIR__ . "/sample_documents/MS_Word_Document_v0.docx";
            // $file1Stream = file_get_contents($file1Path);
            // $stream1Wrapper = new StreamWrapper($file1Name, $file1Stream, $file1Path);
            // $stream1Wrapper = new StreamWrapper(null, null, $file1Path);

            $file2Name = "MS_Word_Document_v1.docx";
            // $file2Path = __DIR__ . "/sample_documents/MS_Word_Document_v1.docx";
            // $file2Stream = file_get_contents($file2Path);
            // $stream2Wrapper = new StreamWrapper($file2Name, $file2Stream, $file2Path);
            // $stream2Wrapper = new StreamWrapper(null, null, $file2Path);

            // $compareDocumentParameters->setDocument1($stream1Wrapper);
            // $compareDocumentParameters->setDocument2($stream2Wrapper);

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
                        echo "\nInvalid configuration exception. Exception JSON - " . json_encode($writerResponseObject) . "\n";
                    } else {
                        echo "\nRequest not completed successfully\n";
                    }
                }
            }
        } catch (Exception $error) {
            echo "\nException while running sample code: " . $error->getMessage() . "\n";
        }
    }
}

CompareDocument::execute();

?>
