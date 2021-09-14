<?php
/*
 * Copyright notice:
 * (c) Copyright 2020 RocketGate
 * All rights reserved.
 *
 * The copyright notice must not be removed without specific, prior
 * written permission from RocketGate.
 *
 * This software is protected as an unpublished work under the U.S. copyright
 * laws. The above copyright notice is not intended to effect a publication of
 * this work.
 * This software is the confidential and proprietary information of RocketGate.
 * Neither the binaries nor the source code may be redistributed without prior
 * written permission from RocketGate.
 *
 * The software is provided "as-is" and without warranty of any kind, express, implied
 * or otherwise, including without limitation, any warranty of merchantability or fitness
 * for a particular purpose.  In no event shall RocketGate be liable for any direct,
 * special, incidental, indirect, consequential or other damages of any kind, or any damages
 * whatsoever arising out of or in connection with the use or performance of this software,
 * including, without limitation, damages resulting from loss of use, data or profits, and
 * whether or not advised of the possibility of damage, regardless of the theory of liability.
 * 
 */
require_once("GatewayCodes.php");
require_once("GatewayRequest.php");
require_once("GatewayResponse.php");
require_once("GatewayChecksum.php");


////////////////////////////////////////////////////////////////////////////////
//
//	Compute the version number.
//
////////////////////////////////////////////////////////////////////////////////
//
GatewayChecksum::SetVersion();


////////////////////////////////////////////////////////////////////////////////
//
//	GatewayService() - Object that performs sends transactions
//			   to a RocketGate Gateway Server.
//				    
////////////////////////////////////////////////////////////////////////////////
//
class GatewayService {
  var $rocketGateHost;			// Gateway hostname
  var $rocketGateProtocol;		// Message protocol
  var $rocketGatePortNo;		// Network connection port
  var $rocketGateServlet;		// Destination servlet
  var $rocketGateConnectTimeout;	// Timeout for network connection
  var $rocketGateReadTimeout;		// Timeout for network read


//////////////////////////////////////////////////////////////////////
//
//	GatewayService() - Constructor for class.
//
//////////////////////////////////////////////////////////////////////
//
  public function __construct()
  {
//
//	Set the standard production destinations for the
//	service.
//
    $this->SetTestMode(FALSE);			// Assume production mode
    $this->rocketGateServlet = "gateway/servlet/ServiceDispatcherAccess";
    $this->rocketGateConnectTimeout = 10;	// 10 second connection timeout
    $this->rocketGateReadTimeout = 90;		// 90 second operation timeout
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformAuthOnly() - Perform an auth-only transaction.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformAuthOnly($request, $response)
  {
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CC_AUTH");
    if ($request->Get(GatewayRequest::REFERENCE_GUID()) != NULL) {
      if (!($this->PerformTargetedTransaction($request, $response))) return FALSE;
    } else {
      if (!($this->PerformTransaction($request, $response))) return FALSE;
    }
    return $this->PerformConfirmation($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformTicket() - Perform a ticket operation for a previous
//			  auth-only transaction.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformTicket($request, $response)
  {
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CC_TICKET");
    return $this->PerformTargetedTransaction($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformPurchase() - Perform a complete purchase transaction
//			    using the information contained in
//			    a request.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformPurchase($request, $response)
  {
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CC_PURCHASE");
    if ($request->Get(GatewayRequest::REFERENCE_GUID()) != NULL) {
      if (!($this->PerformTargetedTransaction($request, $response))) return FALSE;
    } else {
      if (!($this->PerformTransaction($request, $response))) return FALSE;
    }
    return $this->PerformConfirmation($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformCredit() - Perform a credit operation for a previously
//			  completed transaction.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformCredit($request, $response)
  {
//
//	Apply the transaction type to the request.
//
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CC_CREDIT");

//
//	If the credit references a previous transaction, we
//	need to send it back to the origination site.  Otherwise,
//	it can be sent to any server.
//
    if ($request->Get(GatewayRequest::REFERENCE_GUID()) != NULL)
      return $this->PerformTargetedTransaction($request, $response);
    return $this->PerformTransaction($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformVoid() - Perform a void operation for a previously
//			completed transaction.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformVoid($request, $response)
  {
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CC_VOID");
    return $this->PerformTargetedTransaction($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformCardScrub() - Perform scrubbing on a card/customer.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformCardScrub($request, $response)
  {
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CARDSCRUB");
    return $this->PerformTransaction($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformRebillCancel() - Schedule cancellation of rebilling.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformRebillCancel($request, $response)
  {
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "REBILL_CANCEL");
    return $this->PerformTransaction($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformRebillUpdate() - Update terms of rebilling.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformRebillUpdate($request, $response)
  {
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "REBILL_UPDATE");

//
//	If there is no prorated charage, just perform the update.
//
    $amount = $request->Get(GatewayRequest::AMOUNT());
    if (($amount == NULL) || ($amount <= 0.0))
      return $this->PerformTransaction($request, $response);

//
//	If there is a charge, perform the update and confirm
//	the charge.
//
    if (!($this->PerformTransaction($request, $response))) return FALSE;
    return $this->PerformConfirmation($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformLookup() - Lookup previous transaction.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformLookup($request, $response)
  {
//
//	Apply the transaction type to the request.
//
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "LOOKUP");
    if ($request->Get(GatewayRequest::REFERENCE_GUID()) != NULL)
      return $this->PerformTargetedTransaction($request, $response);
    return $this->PerformTransaction($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformCardUpload() - Upload card data to the servers.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformCardUpload($request, $response)
  {
//
//	Apply the transaction type to the request.
//
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CARDUPLOAD");
    return $this->PerformTransaction($request, $response);
  }


//////////////////////////////////////////////////////////////////////
//
//	GenerateXsell() - Add an entry to the XsellQueue.
//
//////////////////////////////////////////////////////////////////////
//
  function GenerateXsell($request, $response)
  {
//
//	Apply the transaction type to the request.
//
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "GENERATEXSELL");
    $request->Set(GatewayRequest::REFERENCE_GUID(),
		  $request->Get(GatewayRequest::XSELL_REFERENCE_XACT()));
    if ($request->Get(GatewayRequest::REFERENCE_GUID()) != NULL)
      return $this->PerformTargetedTransaction($request, $response);
    return $this->PerformTransaction($request, $response);
  }
  
//////////////////////////////////////////////////////////////////////
//
//	BuildPaymentLink() - Create an embeddable RG hosted payment link 
//
//////////////////////////////////////////////////////////////////////
//	
  function BuildPaymentLink($request, $response): bool
  {
    $request->Set("gatewayServlet", "/hostedpage/servlet/BuildPaymentLinkSubmit");
    $this->PerformTransaction($request, $response);
    return ($response->Get(GatewayResponse::RESPONSE_CODE()) == GatewayCodes__RESPONSE_SUCCESS &&
        $response->Get(GatewayResponse::PAYMENT_LINK_URL()) != NULL);
  }


//////////////////////////////////////////////////////////////////////
//
//	SetTestMode() - Set the communications parameters for
//			production or test mode.
//
//////////////////////////////////////////////////////////////////////
//
  function SetTestMode($testFlag)
  {
//
//	If the test flag is set, use the test setup parameters.
//
    if ($testFlag) {				// In test mode?
      $this->rocketGateHost = "dev-gateway.rocketgate.com";
      $this->rocketGateProtocol = "https";	// Use SSL
      $this->rocketGatePortNo = "443";		// SSL port
    
//
//	If the test flag is not set, use the production parameters.
//
    } else {
      $this->rocketGateHost = "gateway.rocketgate.com";
      $this->rocketGateProtocol = "https";	// Use SSL
      $this->rocketGatePortNo = "443";		// SSL port
    }
  }


//////////////////////////////////////////////////////////////////////
//
//	SetHost() - Set the host used by the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetHost($hostname)
  {
    $this->rocketGateHost = $hostname;		// Use this hostname
  }


//////////////////////////////////////////////////////////////////////
//
//	SetProtocol() - Set the communications protocol used by
//			the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetProtocol($protocol)
  {
    $this->rocketGateProtocol = $protocol;	// HTTP, HTTPS, etc.
  }


//////////////////////////////////////////////////////////////////////
//
//	SetPortNo() - Set the port number used by the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetPortNo($portNo)
  {
    $this->rocketGatePortNo = $portNo;		// IP port
  }


//////////////////////////////////////////////////////////////////////
//
//	SetServlet() - Set the servlet used by the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetServlet($servlet)
  {
    $this->rocketGateServlet = $servlet;	// Tomcat servlet
  }


//////////////////////////////////////////////////////////////////////
//
//	SetConnectTimouet() - Set the timeout used during connection
//			      to the servlet.
//
//////////////////////////////////////////////////////////////////////
//
  function SetConnectTimeout($timeout)
  {
    $this->rocketGateConnectTimeout = $timeout;	// Number of seconds
  }


//////////////////////////////////////////////////////////////////////
//
//	SetReadTimouet() - Set the timeout used while waiting for
//			   the servlet to answer.
//
//////////////////////////////////////////////////////////////////////
//
  function SetReadTimeout($timeout)
  {
    $this->rocketGateReadTimeout = $timeout;	// Number of seconds
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformTransaction() - Perform the transaction outlined
//			       in a GatewayRequest.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformTransaction($request, $response)
  {
//
//	Check if an override is requested.
//
    $fullURL = $request->Get("gatewayURL");
    if ($fullURL == NULL) $fullURL = $request->Get("embeddedFieldsToken");

//
//	If an override is in use, split it into
//	its individual elements.
//
    if ($fullURL != NULL) {			// Overriding?
      $urlBits = parse_url($fullURL);		// Split the URL
      if ($request->Get("gatewayServer") == NULL)
	$request->Set("gatewayServer", $urlBits['host']);
      $request->Set("gatewayProtocol", $urlBits['scheme']);
      if (array_key_exists("port", $urlBits))
        $request->Set("gatewayPortNo", $urlBits['port']);
      $request->Set("gatewayServlet", $urlBits['path'] . "?" . $urlBits['query']);
    }

//
//	If the request specifies a server name, use it.
//	Otherwise, use the default for the service.
//
    $serverName = $request->Get("gatewayServer");
    if ($serverName == NULL) $serverName = $this->rocketGateHost;

//
//	Clear any error tracking that may be leftover in
//	a re-used request.
//
    $request->Clear(GatewayRequest::FAILED_SERVER());
    $request->Clear(GatewayRequest::FAILED_RESPONSE_CODE());
    $request->Clear(GatewayRequest::FAILED_REASON_CODE());
    $request->Clear(GatewayRequest::FAILED_GUID());

//
//	Lookup the hostname in DNS.
//
    if (strcmp($serverName, "gateway.rocketgate.com") != 0) {
      $hostList = array();			// Create an array
      $hostList[0] = $serverName;		// Use name directly
    } else {
      $hostList = gethostbynamel($serverName);	// Lookup the hostname
      if (!($hostList)) {			// Lookup failed?
	$hostList = array();			// Create an array
	$hostList[0] = "gateway-16.rocketgate.com";	// Add default resolution
	$hostList[1] = "gateway-17.rocketgate.com";
      } else {
	$index = 0;				// Initialize index
	$listSize = count($hostList);		// Get element count
	while ($index < $listSize) {		// Loop over all entries
	  if (strcmp($hostList[$index], "69.20.127.91") == 0)
	    $hostList[$index] = "gateway-16.rocketgate.com";
	  if (strcmp($hostList[$index], "72.32.126.131") == 0)
	    $hostList[$index] = "gateway-17.rocketgate.com";
	  $index++;				// Look at next in list
	}
      }
    }

//
//	Randomly select an end-point to use first.
//
    if (($listSize = count($hostList)) > 1) {	// More than one address?
      $index = rand(0, ($listSize - 1));	// Get random index
      if ($index > 0) {				// Want to swap?
	$swapper = $hostList[0];		// Save this one
	$hostList[0] = $hostList[$index];	// Put this one first
	$hostList[$index] = $swapper;		// And put this one here
      }
    }

//
//	Loop over the hosts in the DNS entry.  Try to send the
//	transaction to each host until it finally succeeds.  If it
//	fails due to an unrecoverable system error, we must quit.
//
    $index = 0;					// Start with first entry
    while ($index < $listSize) {		// Loop over all entries
      $results = $this->PerformCURLTransaction($hostList[$index],
					       $request,
					       $response);
      if ($results == GatewayCodes__RESPONSE_SUCCESS) return TRUE;
      if ($results != GatewayCodes__RESPONSE_SYSTEM_ERROR) return FALSE;

//
//	Save any errors in the response so they can be
//	transmitted along with the next request.
//
      $request->Set(GatewayRequest::FAILED_SERVER(), $hostList[$index]);
      $request->Set(GatewayRequest::FAILED_RESPONSE_CODE(),
		    $response->Get(GatewayResponse::RESPONSE_CODE()));
      $request->Set(GatewayRequest::FAILED_REASON_CODE(),
		    $response->Get(GatewayResponse::REASON_CODE()));
      $request->Set(GatewayRequest::FAILED_GUID(),
		    $response->Get(GatewayResponse::TRANSACT_ID()));
      $index++;					// Try next host in list
    }
    return FALSE;				// Transaction failed
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformTargetedTransaction() - Send a transaction to a server
//				       based upon the reference GUID.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformTargetedTransaction($request, $response)
  {
//
//	Clear any error tracking that may be leftover in
//	a re-used request.
//
    $request->Clear(GatewayRequest::FAILED_SERVER());
    $request->Clear(GatewayRequest::FAILED_RESPONSE_CODE());
    $request->Clear(GatewayRequest::FAILED_REASON_CODE());
    $request->Clear(GatewayRequest::FAILED_GUID());

//
//	Check if an override is requested.
//
    $fullURL = $request->Get("gatewayURL");
    if ($fullURL == NULL) $fullURL = $request->Get("embeddedFieldsToken");

//
//	If an override is in use, split it into
//	its individual elements.
//
    if ($fullURL != NULL) {			// Overriding?
      $urlBits = parse_url($fullURL);		// Split the URL
      if ($request->Get("gatewayServer") == NULL)
	$request->Set("gatewayServer", $urlBits['host']);
      $request->Set("gatewayProtocol", $urlBits['scheme']);
      $request->Set("gatewayPortNo", $urlBits['port']);
      $request->Set("gatewayServlet", $urlBits['path'] . "?" . $urlBits['query']);
    }

//
//	This transaction must go to the host that processed a
//	previous referenced transaction.  Get the GUID of the
//	reference transaction.
//
    $referenceGUID = $request->Get(GatewayRequest::REFERENCE_GUID());
    if ($referenceGUID == NULL) {		// Don't have reference?
      $response->SetResults(GatewayCodes__RESPONSE_REQUEST_ERROR,
			    GatewayCodes__REASON_INVALID_REFGUID);
      return FALSE;				// Transaction failed
    }

//
//	Strip off the bits that indicate which server should
//	be used.
//
    if (strlen($referenceGUID) > 15) {		// Server 16 and above?
      $siteNo = substr($referenceGUID, 0, 2);	// Get first two digits
    } else {
      $siteNo = substr($referenceGUID, 0, 1);	// Get first digit only
    }
    $siteNo = hexdec($siteNo);			// Convert to decimal

//
//	Build the hostname to which the transaction should
//	be directed.
//
    $serverName = $request->Get("gatewayServer");
    if ($serverName == NULL) {			// Was server specified?
      $serverName = $this->rocketGateHost;	// No - Use default
      if (($separator = strpos($serverName, ".")) > 0) {
	$prefix = substr($serverName, 0, $separator);
        $serverName = substr($serverName, $separator);
	$serverName = $prefix . "-" . $siteNo . $serverName;
      }
    }

//
//	Send the transaction to the named host.
//
    $results = $this->PerformCURLTransaction($serverName, $request, $response);
    if ($results == GatewayCodes__RESPONSE_SUCCESS) return TRUE;
    return FALSE;
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformConfirmation() - Perform the confirmation pass that
//				tells the server we have received
//				transaction reply.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformConfirmation($request, $response)
  {

//
//	Verify that we have a transaction ID for the confirmation
//	message.
//
    $confirmGUID = $response->Get(GatewayResponse::TRANSACT_ID());
    if ($confirmGUID == NULL) {			// Don't have reference?
      $response->Set(GatewayResponse::EXCEPTION(),
		     "BUG-CHECK - Missing confirmation GUID");
      $response->SetResults(GatewayCodes__RESPONSE_SYSTEM_ERROR,
			    GatewayCodes__REASON_BUGCHECK);
      return FALSE;				// Transaction failed
    }

//
//	Add the GUID to the request and send it back to the
//	original server for confirmation.
//
    $confirmResponse = new GatewayResponse();	// Need a new response object
    $request->Set(GatewayRequest::TRANSACTION_TYPE(), "CC_CONFIRM");
    $request->Set(GatewayRequest::REFERENCE_GUID(), $confirmGUID);
    if ($this->PerformTargetedTransaction($request, $confirmResponse))
      return TRUE;

//////////////////////////////////////////////////////////////////////
//
//	12-21-2011	darcy
//
//	If we experienced a system error, retry the confirmation.
//
    if ($confirmResponse->Get(GatewayResponse::RESPONSE_CODE()) == GatewayCodes__RESPONSE_SYSTEM_ERROR) {
      sleep(2);					// Short delay
      if ($this->PerformTargetedTransaction($request, $confirmResponse))
	return TRUE;
    }
//
//////////////////////////////////////////////////////////////////////

//
//	If the confirmation failed, copy the reason and response code
//	into the original response object to override the success.
//
    $response->SetResults(
                $confirmResponse->Get(GatewayResponse::RESPONSE_CODE()),
                $confirmResponse->Get(GatewayResponse::REASON_CODE()));
    $response->Set(GatewayResponse::EXCEPTION(),
		$confirmResponse->Get(GatewayResponse::EXCEPTION()));
    return FALSE;				// And quit
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformCURLTransaction() - Perform a transaction exchange
//				   with a given host.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformCURLTransaction($host, $request, $response)
  {
//
//	Reset the response object and turn the request into
//	a string that can be transmitted.
//
    $response->Reset();				// Clear old contents
    $requestBytes = $request->ToXMLString();	// Change to XML request

//
//	Gather override attibutes used for the connection URL.
//
    $urlServlet = $request->Get("gatewayServlet");
    $urlProtocol = $request->Get("gatewayProtocol");
    $urlPortNo = $request->Get("gatewayPortNo");

//
//	If the parameters were not set in the request,
//	use the system defaults.
//
    if ($urlServlet == NULL) $urlServlet = $this->rocketGateServlet;
    if ($urlProtocol == NULL) $urlProtocol = $this->rocketGateProtocol;
    if ($urlPortNo == NULL) $urlPortNo = $this->rocketGatePortNo;

//
//	Build the URL for the gateway service.
//
    $url = $urlProtocol . "://" 		// Start with protocol
			. $host	. ":"		// Add the host
			. $urlPortNo . "/"	// Add the port number
			. $urlServlet;		// Add servlet path

//
//	Gather the override timeout values that will be used
//	for the connection.
//
    $connectTimeout = $request->Get("gatewayConnectTimeout");
    $readTimeout = $request->Get("gatewayReadTimeout");

//
//	Use default values if the parameters were not set.
//
    if ($connectTimeout == NULL)		// No connect timeout specified?
      $connectTimeout = $this->rocketGateConnectTimeout;
    if ($readTimeout == NULL) $readTimeout = $this->rocketGateReadTimeout;
 
//
//	Create a handle that can be used for the URL operation.
//
    if (!($handle = curl_init())) {		// Failed to initialize?
      $response->Set(GatewayResponse::EXCEPTION(), "curl_init() error");
      $response->SetResults(GatewayCodes__RESPONSE_REQUEST_ERROR,
			    GatewayCodes__REASON_INVALID_URL);
      $response->Set(GatewayResponse::REASON_CODE_NAME(), 'REASON_INVALID_URL');
      $response->Set(GatewayResponse::MERCHANT_REASON_CODE_DESCRIPTION(), 'Invalid URL');
      $response->Set(GatewayResponse::CARDHOLDER_REASON_CODE_DESCRIPTION(), 'Invalid URL');
      return GatewayCodes__RESPONSE_REQUEST_ERROR;
    }

//
//	Set timeout values used in the operation.
//
    curl_setopt($handle, CURLOPT_NOSIGNAL, TRUE);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
    curl_setopt($handle, CURLOPT_TIMEOUT, $readTimeout);

//////////////////////////////////////////////////////////////////////
//
//	03-24-2015	darcy
//
//	Remove SSL override.
//	
////
////	Setup verification for SSL connections.
////
//    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
//    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
//
//////////////////////////////////////////////////////////////////////

//
//	Setup the call to the URL.
//
    curl_setopt($handle, CURLOPT_POST, TRUE);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $requestBytes);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_FAILONERROR, TRUE);

//////////////////////////////////////////////////////////////////////
//
//	04-30-2013	darcy
//
//	Updated user agent.
//
//	12-20-2011	darcy
//
//	Updated user agent.
//
//	08-25-2011	darcy
//
//	Updated user agent.
//
//	05-31-2009	darcy
//
//	Updated the user agent.
//
//	04-27-2009	darcy
//
//	Set the user-agent.
//
//    curl_setopt($handle, CURLOPT_USERAGENT, "RG PHP Client 2.0");
//    curl_setopt($handle, CURLOPT_USERAGENT, "RG PHP Client 2.1");
//    curl_setopt($handle, CURLOPT_USERAGENT, "RG PHP Client 3.0");
//
//  12-11-2017	Jason	Set the user-agent dynamically
    curl_setopt($handle, CURLOPT_USERAGENT, "RG PHP Client " . GatewayChecksum::$versionNo);
//
//////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////
//
// 2/21/2010 Jason. Set content-type.
//
  curl_setopt ($handle, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
//////////////////////////////////////////////////////////////////////

//
//	Execute the operation.
//
    $results = curl_exec($handle);		// Execute the operation
    if (!($results)) {				// Did it fail?
      $errorCode = curl_errno($handle);		// Get the error code
      $errorString = curl_error($handle);	// Get the error text
      curl_close($handle);			// Done with handle

//
//	Translate the CURL error code into a Gateway code.
//
      switch($errorCode) {			// Classify error code
        case CURLE_SSL_CONNECT_ERROR:		// Connection failures
	case CURLE_COULDNT_CONNECT:
          $internalCode = GatewayCodes__REASON_UNABLE_TO_CONNECT;
          $internalCodeName = 'REASON_UNABLE_TO_CONNECT';
          $internalMerchantCodeDescription = 'Unable to Connect';
          $internalCardholderCodeDescription = 'Unable to Connect';
	  break;				// Done with request
        case CURLE_SEND_ERROR:			// Failed sending data
          $internalCode = GatewayCodes__REASON_REQUEST_XMIT_ERROR;
          $internalCodeName = 'REASON_REQUEST_XMIT_ERROR';
          $internalMerchantCodeDescription = 'Transmit Error';
          $internalCardholderCodeDescription = 'Transmit Error';
	  break;				// Done with request
        case CURLE_OPERATION_TIMEOUTED:		// Time-out reached
          $internalCode = GatewayCodes__REASON_RESPONSE_READ_TIMEOUT;
          $internalCodeName = 'REASON_RESPONSE_READ_TIMEOUT';
          $internalMerchantCodeDescription = 'Read Timeout';
          $internalCardholderCodeDescription = 'Read Timeout';
	  break;				// Done with request
        case CURLE_RECV_ERROR:			// Failed reading data
        case CURLE_READ_ERROR:
        default:
          $internalCode = GatewayCodes__REASON_RESPONSE_READ_ERROR;
          $internalCodeName = 'REASON_RESPONSE_READ_ERROR';
          $internalMerchantCodeDescription = 'Read Error';
          $internalCardholderCodeDescription = 'Read Error';
      }

//
//	If the operation failed, return an error code.
//
      if (strlen($errorString) != 0)		// Have an error?
        $response->Set(GatewayResponse::EXCEPTION(), $errorString);
      $response->SetResults(GatewayCodes__RESPONSE_SYSTEM_ERROR,
			    $internalCode);
      $response->Set(GatewayResponse::REASON_CODE_NAME(), $internalCodeName);
      $response->Set(GatewayResponse::MERCHANT_REASON_CODE_DESCRIPTION(), $internalMerchantCodeDescription);
      $response->Set(GatewayResponse::CARDHOLDER_REASON_CODE_DESCRIPTION(), $internalCardholderCodeDescription);
      return GatewayCodes__RESPONSE_SYSTEM_ERROR;
    }

//
//	Parse the returned message into the response
//	object.
//
    curl_close($handle);			// Done with handle
    $response->SetFromXML($results);		// Set response
    return $response->Get(GatewayResponse::RESPONSE_CODE());
  }
}
?>
