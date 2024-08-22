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
use com\zoho\officeintegrator\v1\ConvertPresentationParameters;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\FileBodyWrapper;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class ConvertPresentation {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/show-conversion-api.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new ConvertPresentationParameters();

            $url = 'https://demo.office-integrator.com/samples/show/Zoho_Show.pptx';
            $parameters->setUrl($url);

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Zoho_Show.pptx";
            // $parameters->setDocument(new StreamWrapper(null, null, $filePath));

            $parameters->setFormat("pdf");

            $responseObject = $sdkOperations->convertPresentation($parameters);

            if ($responseObject != null) {
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the API response object from responseObject
                $presentationResponseObject = $responseObject->getObject();

                if ($presentationResponseObject != null) {
                    if ($presentationResponseObject instanceof FileBodyWrapper) {
                        $convertedDocument = $presentationResponseObject->getFile();

                        if ($convertedDocument instanceof StreamWrapper) {
                            $outputFilePath = __DIR__ . "/sample_documents/conversion_output.pdf";

                            file_put_contents($outputFilePath, $convertedDocument->getStream());
                            echo "\nCheck converted output file in file path - " . $outputFilePath . "\n";
                        }
                    } elseif ($presentationResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception." . "\n";
                        echo "\nError Code - " . $presentationResponseObject->getCode() . "\n";
                        echo "\nError Message - " . $presentationResponseObject->getMessage() . "\n";
                        if ( $presentationResponseObject->getKeyName() ) {
                            echo "\nError Key Name - " . $presentationResponseObject->getKeyName() . "\n";
                        }
                        if ( $presentationResponseObject->getParameterName() ) {
                            echo "\nError Parameter Name - " . $presentationResponseObject->getParameterName() . "\n";
                        }
                    } else {
                        echo "\nConversion request not completed successfully\n";
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

ConvertPresentation::execute();

