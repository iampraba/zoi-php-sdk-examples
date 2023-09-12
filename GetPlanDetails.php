<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/vendor/autoload.php';

use com\zoho\api\authenticator\APIKey;
use com\zoho\api\logger\Levels;
use com\zoho\api\logger\LogBuilder;
use com\zoho\dc\DataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\UserSignature;
use com\zoho\util\Constants;
use com\zoho\officeintegrator\v1\PlanDetails;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class GetPlanDetails {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/get-plan-details.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();

            $responseObject = $sdkOperations->getPlanDetails();

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $responseObject = $responseObject->getObject();

                if ($responseObject != null) {
                    // Check if the expected PlanDetails instance is received
                    if ($responseObject instanceof PlanDetails) {
                        echo "\nPlan name :  - " . $responseObject->getPlanName() . "\n";
                        echo "\nAPI usage limit :  - " . $responseObject->getUsageLimit() . "\n";
                        echo "\nAPI usage so far :  - " . $responseObject->getTotalUsage() . "\n";
                        echo "\nPlan upgrade payment link :  - " . $responseObject->getPaymentLink() . "\n";
                        echo "\nSubscription period :  - " . $responseObject->getSubscriptionPeriod() . "\n";
                        echo "\nSubscription interval :  - " . $responseObject->getSubscriptionInterval() . "\n";
                    } elseif ($responseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception." . "\n";
                        echo "\nError Code - " . $responseObject->getCode() . "\n";
                        echo "\nError Message - " . $responseObject->getMessage() . "\n";
                        if ( $responseObject->getKeyName() ) {
                            echo "\nError Key Name - " . $responseObject->getKeyName() . "\n";
                        }
                        if ( $responseObject->getParameterName() ) {
                            echo "\nError Parameter Name - " . $responseObject->getParameterName() . "\n";
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

GetPlanDetails::execute();

