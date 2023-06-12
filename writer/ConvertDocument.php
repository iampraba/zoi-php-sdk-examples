<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\DocumentConversionOutputOptions;
use com\zoho\officeintegrator\v1\DocumentConversionParameters;
use com\zoho\officeintegrator\v1\FileBodyWrapper;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;
use Exception;

class ConvertDocument {

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
        // You can place SDK initializer code in your application and call once while your application start-up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $documentConversionParameters = new DocumentConversionParameters();

            // Either use URL as document source or attach the document in the request body using the methods below
            $documentConversionParameters->setUrl("https://demo.office-integrator.com/zdocs/MS_Word_Document_v0.docx");

            // $fileName = "Graphic-Design-Proposal.docx";
            // $filePath = __DIR__ . "/sample_documents/Graphic-Design-Proposal.docx";
            // $fileStream = file_get_contents($filePath);
 
            // $streamWrapper = new StreamWrapper($fileName, $fileStream, $filePath);

            // $documentConversionParameters->setDocument($streamWrapper);

            $outputOptions = new DocumentConversionOutputOptions();

            $outputOptions->setFormat("pdf");
            $outputOptions->setDocumentName("conversion_output.pdf");
            $outputOptions->setIncludeComments("all");
            $outputOptions->setIncludeChanges("all");
            $outputOptions->setPassword("***");

            $documentConversionParameters->setOutputOptions($outputOptions);
            //$documentConversionParameters->setPassword("***");

            $responseObject = $sdkOperations->convertDocument($documentConversionParameters);

            if ($responseObject != null) {
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the API response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    if ($writerResponseObject instanceof FileBodyWrapper) {
                        $convertedDocument = $writerResponseObject->getFile();

                        if ($convertedDocument instanceof StreamWrapper) {
                            $outputFilePath = __DIR__ . "/sample_documents/conversion_output.pdf";

                            file_put_contents($outputFilePath, $convertedDocument->getStream());
                            echo "\nCheck converted output file in file path - " . $outputFilePath . "\n";
                        }
                    } elseif ($writerResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception. Exception JSON - " . $writerResponseObject->getMessage() . "\n";
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

ConvertDocument::execute(); 

?>
