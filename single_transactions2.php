<?php

#Function to store customer's information and credit card to BrainTree Vault
function create_customer(){
    
 #Set timezone if not specified in php.ini
        //date_default_timezone_set('America/Los_Angeles');
 require_once './_environment.php';
 $includeAddOn = false;
 
 /* First we create a new user using the BT API */
 $result = Braintree_Customer::create(array(
                'firstName' => mysql_real_escape_string($_POST['first_name']),
                'lastName' => mysql_real_escape_string($_POST['last_name']),
                'company' => mysql_real_escape_string($_POST['company']),
 'email' => mysql_real_escape_string($_POST['user_email']),
 'phone' => mysql_real_escape_string($_POST['user_phone']),
                
 // we can create a credit card at the same time
                'creditCard' => array(
                    'cardholderName' => mysql_real_escape_string($_POST['full_name']),
                    'number' => mysql_real_escape_string($_POST['card_number']),
                    'expirationMonth' => mysql_real_escape_string($_POST['expiry_month']),
                    'expirationYear' => mysql_real_escape_string($_POST['expiry_year']),
                    'cvv' => mysql_real_escape_string($_POST['card_cvv']),
                    'billingAddress' => array(
                        'firstName' => mysql_real_escape_string($_POST['first_name']),
                        'lastName' => mysql_real_escape_string($_POST['last_name'])
                       /*Optional Information you can supply
 'company' => mysql_real_escape_string($_POST['company']),
                        'streetAddress' => mysql_real_escape_string($_POST['user_address']),
                        'locality' => mysql_real_escape_string($_POST['user_city']),
                        'region' => mysql_real_escape_string($_POST['user_state']), 
                        //'postalCode' => mysql_real_escape_string($_POST['zip_code']),
                        'countryCodeAlpha2' => mysql_real_escape_string($_POST['user_country'])
       */
                    )
                )
            ));
    if ($result->success) {
       //Do your stuff
       //$creditCardToken = $result->customer->creditCards[0]->token;
       //echo("Customer ID: " . $result->customer->id . "<br />");
       //echo("Credit card ID: " . $result->customer->creditCards[0]->token . "<br />");
    } else {
        foreach ($result->errors->deepAll() as $error) {
            $errorFound = $error->message . "<br />";
        }
 echo $errorFound ;
        exit;
    }
}