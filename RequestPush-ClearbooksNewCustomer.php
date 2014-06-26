<?php
/*
#############################################################################################################

For more information on HelpSpot see our website:
http://www.kamazoy.com/our-services/helpspot-implementation-consultancy-partner/

This file will allow HelpSpot to make a new customer record within Clearbooks using information that the
customer has provided to HelpSpot such as name and email and phone, and you can use HelpSpot's "comments" field
to specify the company name if you wish, otherwise it will be the domain name.

Make sure you read and install the clearbooks SOAP dependencies within your custom_code directory.
The path to your include may need changing within this script and also within the includes.php itself.

This script is provided free and as-is with no warranty whatsoever.

#############################################################################################################
*/

// SECURITY: This prevents this script from being called from outside the context of HelpSpot

if (!defined('cBASEPATH')) die();

class RequestPush_ClearbooksNewCustomer{
	
	var $errorMsg = "";
	var $myClearBooksUserName = ""; // this is your Clearbooks domain name. Look in the URL bar when in Clearbooks.
	
	function push($request){

		$clearbooksUrl = 'https://secure.clearbooks.co.uk/';
 
			$client = new SoapClient($clearbooksUrl.'api/wsdl/');
		
			$client->__setSoapHeaders(array(
				new SoapHeader($clearbooksUrl . 'api/soap/',
					'authenticate', array('apiKey' => 'apikey')), // Your API key goes here!
			));

		require_once __DIR__ . '/clearbooks/includes.php';
		
		// did we enter a company name into the comments field?
		$companyname = $request['staff_comment'];
		if(!$request['staff_comment']) $companyname = substr(strrchr(($request['sEmail']), "@"), 1);
		   
		// create the entity
		$entity = new \Clearbooks_Soap_1_0_Entity();
		$entity->company_name = $companyname;
		$entity->contact_name = $request['sFirstName'] . ' ' . $request['sLastName'];
		$entity->email        = $request['sEmail'];
		$entity->website      = substr(strrchr(($request['sEmail']), "@"), 1);
		$entity->phone1       = $request[sPhone];
	
		// indicate that the entity is a customer
		$entity->customer     = new \Clearbooks_Soap_1_0_EntityExtra();
		$entity->customer->default_vat_rate = "0.2";      // it's 20% in the UK, change this if you want
		$entity->customer->default_credit_terms = "30";   // 30 days of credit by default
	
		// now create the entity
		$entityId = $client->createEntity( $entity );
	
		$result = "Created customer record <a href='https://secure.clearbooks.co.uk/" . $myClearBooksUserName . "/accounting/contacts/overview/" . $entityId . "/'>" . $entityId .  "</a>.";
		return $result;
		
	}


	function details($id){

		return $result;
		
	}

}
?>
