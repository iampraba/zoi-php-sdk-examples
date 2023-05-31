<?php
namespace com\zoho\officeintegrator\v1\writer;
require_once '../vendor/autoload.php';
require_once '../Initialize.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\CreateDocumentParameters;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\DocumentDefaults;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\examples\Initialize;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\util\StreamWrapper;

class CoEditDocument {

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
        self::initializeSdk();

        $v1Operations = new V1Operations();

        $parameters = new CreateDocumentParameters();

        $documentInfo = new DocumentInfo();

        $currentTime = time();

        $documentInfo->setDocumentId($currentTime);
        
        $parameters->setDocumentInfo($documentInfo);

        $userInfo = new UserInfo();

        $userInfo->setDisplayName("John");

        $userInfo->setUserId(100);

        $parameters->setUserInfo($userInfo);

        $documentDefaults = new DocumentDefaults();

        $documentDefaults->setLanguage("ta");

        $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Graphic-Design-Proposal.docx";

        $parameters->setDocument(new StreamWrapper(null, null, $filePath));

        $parameters->setDocumentDefaults($documentDefaults);

        $url = "https://demo.office-integrator.com/zdocs/Graphic-Design-Proposal.docx";

        #$parameters->setUrl($url);

        $response = $v1Operations->createDocument($parameters);

        if($response != null)
        {
            //Get the status code from response
            echo("Status code " . $response->getStatusCode() . "\n");

            //Get object from response
            $responseHandler = $response->getObject();
            
            if($responseHandler instanceof CreateDocumentResponse)
            {
                echo("Document ID - " . $responseHandler->getDocumentId() . "\n");
                echo("Document URL - " . $responseHandler->getDocumentUrl() . "\n");
                echo("Document Session ID - " . $responseHandler->getSessionId() . "\n");
            }
        }
    }
}

CoEditDocument::execute();

?>