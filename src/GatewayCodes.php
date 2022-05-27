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
 
//////////////////////////////////////////////////////////////////////
//
//	Declaration of static response codes.
//
//////////////////////////////////////////////////////////////////////
//
define("GatewayCodes__RESPONSE_SUCCESS", 0);	   // Function succeeded
define("GatewayCodes__RESPONSE_BANK_FAIL", 1);	   // Bank decline/failure
define("GatewayCodes__RESPONSE_RISK_FAIL", 2);	   // Risk failure
define("GatewayCodes__RESPONSE_SYSTEM_ERROR", 3);  // Server/recoverable error
define("GatewayCodes__RESPONSE_REQUEST_ERROR", 4); // Invalid request

//////////////////////////////////////////////////////////////////////
//
//	Declaration of static reason codes.
//
//////////////////////////////////////////////////////////////////////
//
define("GatewayCodes__REASON_SUCCESS", 0);      // Function succeeded

define("GatewayCodes__REASON_NOMATCHING_XACT", 100);
define("GatewayCodes__REASON_CANNOT_VOID", 101);
define("GatewayCodes__REASON_CANNOT_CREDIT", 102);
define("GatewayCodes__REASON_CANNOT_TICKET", 103);
define("GatewayCodes__REASON_DECLINED", 104);
define("GatewayCodes__REASON_DECLINED_OVERLIMIT", 105);
define("GatewayCodes__REASON_DECLINED_CVV2", 106);
define("GatewayCodes__REASON_DECLINED_EXPIRED", 107);
define("GatewayCodes__REASON_DECLINED_CALL", 108);
define("GatewayCodes__REASON_DECLINED_PICKUP", 109);
define("GatewayCodes__REASON_DECLINED_EXCESSIVE", 110);
define("GatewayCodes__REASON_DECLINED_INVALID_CARDNO", 111);
define("GatewayCodes__REASON_DECLINED_INVALID_EXPIRATION", 112);
define("GatewayCodes__REASON_BANK_UNAVAILABLE", 113);
define("GatewayCodes__REASON_EMPTY_BATCH", 114);
define("GatewayCodes__REASON_BATCH_REJECTED", 115);
define("GatewayCodes__REASON_DUPLICATE_BATCH", 116);
define("GatewayCodes__REASON_DECLINED_AVS", 117);
define("GatewayCodes__REASON_NO_BATCH_AVAILABLE", 118);
define("GatewayCodes__REASON_USER_BUSY", 119);
define("GatewayCodes__REASON_INVALID_REGION", 120);
define("GatewayCodes__REASON_UNKNOWN_CARRIER", 121);
define("GatewayCodes__REASON_CARRIER_REQUIRED", 122);
define("GatewayCodes__REASON_USER_DECLINED", 123);
define("GatewayCodes__REASON_USER_TIMEOUT", 124);
define("GatewayCodes__REASON_NETWORK_MISMATCH", 125);
define("GatewayCodes__REASON_CELLPHONE_BLACKLISTED", 126);
define("GatewayCodes__REASON_FULL_FAILURE", 127);
define("GatewayCodes__REASON_PARTAIL_FAILURE", 128);
define("GatewayCodes__REASON_DECLINED_AVS_AUTOVOID", 150);
define("GatewayCodes__REASON_DECLINED_CVV2_AUTOVOID", 151);
define("GatewayCodes__REASON_INVALID_TICKET_AMT", 152);
define("GatewayCodes__REASON_NO_SUCH_FILE", 153);
define("GatewayCodes__REASON_INTEGRATION_ERROR", 154);
define("GatewayCodes__REASON_DECLINED_CAVV", 155);
define("GatewayCodes__REASON_UNSUPPORTED_CARDTYPE", 156);
define("GatewayCodes__REASON_DECLINED_RISK", 157);
define("GatewayCodes__REASON_INVALID_DEBIT_ACCOUNT", 158);
define("GatewayCodes__REASON_INVALID_USER_DATA", 159);
define("GatewayCodes__REASON_AUTH_HAS_EXPIRED", 160);
define("GatewayCodes__REASON_PREVIOUS_HARD_DECLINE", 161);
define("GatewayCodes__REASON_MERCHACCT_LIMIT", 162);
define("GatewayCodes__REASON_DECLINED_CAVV_AUTOVOID", 163);
define("GatewayCodes__REASON_BANK_INVALID_TRANSACTION", 165);
define("GatewayCodes__REASON_CVV2_REQUIRED", 167);
define("GatewayCodes__REASON_INVALID_TAX_ID", 169);

define("GatewayCodes__REASON_RISK_FAIL", 200);
define("GatewayCodes__REASON_CUSTOMER_BLOCKED", 201);
define("GatewayCodes__REASON_3DSECURE_AUTHENTICATION_REQUIRED", 202);
define("GatewayCodes__REASON_3DSECURE_NOT_ENROLLED", 203);
define("GatewayCodes__REASON_3DSECURE_UNAVAILABLE", 204);
define("GatewayCodes__REASON_3DSECURE_REJECTED", 205);
define("GatewayCodes__REASON_RISK_PREPAID_CARD", 206);
define("GatewayCodes__REASON_RISK_AVS_VS_ISSUER", 207);
define("GatewayCodes__REASON_DUPLICATE_MEMBERSHIP", 208);
define("GatewayCodes__REASON_DUPLICATE_CARD", 209);
define("GatewayCodes__REASON_DUPLICATE_EMAIL", 210);
define("GatewayCodes__REASON_EXCEEDED_MAX_PURCHASE", 211);
define("GatewayCodes__REASON_DUPLICATE_PURCHASE", 212);
define("GatewayCodes__REASON_VELOCITY_CUSTOMER", 213);
define("GatewayCodes__REASON_VELOCITY_CARD", 214);
define("GatewayCodes__REASON_VELOCITY_EMAIL", 215);
define("GatewayCodes__REASON_IOVATION_DECLINE", 216);
define("GatewayCodes__REASON_VELOCITY_DEVICE", 217);
define("GatewayCodes__REASON_DUPLICATE_DEVICE", 218);
define("GatewayCodes__REASON_1CLICK_SOURCE", 219);
define("GatewayCodes__REASON_TOO_MANY_CARDS", 220);
define("GatewayCodes__REASON_AFFILIATE_BLOCKED", 221);
define("GatewayCodes__REASON_TRIAL_ABUSE", 222);
define("GatewayCodes__REASON_3DSECURE_BYPASS", 223);
define("GatewayCodes__REASON_NEWCARD_NODEVICE", 224);
define("GatewayCodes__REASON_3DSECURE_INITIATION", 225);
define("GatewayCodes__REASON_3DSECURE_FRICTIONLESS_FAILED_AUTH", 227);
define("GatewayCodes__REASON_3DSECURE_SCA_REQUIRED", 228);


define("GatewayCodes__REASON_DNS_FAILURE", 300);
define("GatewayCodes__REASON_UNABLE_TO_CONNECT", 301);
define("GatewayCodes__REASON_REQUEST_XMIT_ERROR", 302);
define("GatewayCodes__REASON_RESPONSE_READ_TIMEOUT", 303);
define("GatewayCodes__REASON_RESPONSE_READ_ERROR", 304);
define("GatewayCodes__REASON_SERVICE_UNAVAILABLE", 305);
define("GatewayCodes__REASON_CONNECTION_UNAVAILABLE", 306);
define("GatewayCodes__REASON_BUGCHECK", 307);
define("GatewayCodes__REASON_UNHANDLED_EXCEPTION", 308);
define("GatewayCodes__REASON_SQL_EXCEPTION", 309);
define("GatewayCodes__REASON_SQL_INSERT_ERROR", 310);
define("GatewayCodes__REASON_BANK_CONNECT_ERROR", 311);
define("GatewayCodes__REASON_BANK_XMIT_ERROR", 312);
define("GatewayCodes__REASON_BANK_READ_ERROR", 313);
define("GatewayCodes__REASON_BANK_DISCONNECT_ERROR", 314);
define("GatewayCodes__REASON_BANK_TIMEOUT_ERROR", 315);
define("GatewayCodes__REASON_BANK_PROTOCOL_ERROR", 316);
define("GatewayCodes__REASON_ENCRYPTION_ERROR", 317);
define("GatewayCodes__REASON_BANK_XMIT_RETRIES", 318);
define("GatewayCodes__REASON_BANK_RESPONSE_RETRIES", 319);
define("GatewayCodes__REASON_BANK_REDUNDANT_RESPONSES", 320);
define("GatewayCodes__REASON_WEBSERVICE_FAILURE", 321);
define("GatewayCodes__REASON_PROCESSOR_BACKEND_FAILURE", 322);
define("GatewayCodes__REASON_JSON_FAILURE", 323);
define("GatewayCodes__REASON_GPG_FAILURE", 324);
define("GatewayCodes__REASON_3DS_SYSTEM_FAIULRE", 325);
define("GatewayCodes__REASON_USE_DIFFERENT_SERVER", 326);

define("GatewayCodes__REASON_XML_ERROR", 400);
define("GatewayCodes__REASON_INVALID_URL", 401);
define("GatewayCodes__REASON_INVALID_TRANSACTION", 402);
define("GatewayCodes__REASON_INVALID_CARDNO", 403);
define("GatewayCodes__REASON_INVALID_EXPIRATION", 404);
define("GatewayCodes__REASON_INVALID_AMOUNT", 405);
define("GatewayCodes__REASON_INVALID_MERCHANT_ID", 406);
define("GatewayCodes__REASON_INVALID_MERCHANT_ACCOUNT", 407);
define("GatewayCodes__REASON_INCOMPATIBLE_CARDTYPE", 408);
define("GatewayCodes__REASON_NO_SUITABLE_ACCOUNT", 409);
define("GatewayCodes__REASON_INVALID_REFGUID", 410);
define("GatewayCodes__REASON_INVALID_ACCESS_CODE", 411);
define("GatewayCodes__REASON_INVALID_CUSTDATA_LENGTH", 412);
define("GatewayCodes__REASON_INVALID_EXTDATA_LENGTH", 413);
define("GatewayCodes__REASON_INVALID_CUSTOMER_ID", 414);
define("GatewayCodes__REASON_INVALID_CURRENCY", 418);
define("GatewayCodes__REASON_INCOMPATIBLE_CURRENCY", 419);
define("GatewayCodes__REASON_INVALID_REBILL_ARGS", 420);
define("GatewayCodes__REASON_INVALID_PHONE", 421);
define("GatewayCodes__REASON_INVALID_COUNTRY_CODE", 422);
define("GatewayCodes__REASON_INVALID_BILLING_MODE", 423);
define("GatewayCodes__REASON_INCOMPATABLE_COUNTRY", 424);
define("GatewayCodes__REASON_INVALID_TIMEOUT", 425);
define("GatewayCodes__REASON_INVALID_ACCOUNT_NO", 426);
define("GatewayCodes__REASON_INVALID_ROUTING_NO", 427);
define("GatewayCodes__REASON_INVALID_LANGUAGE_CODE", 428);
define("GatewayCodes__REASON_INVALID_BANK_NAME", 429);
define("GatewayCodes__REASON_INVALID_BANK_CITY", 430);
define("GatewayCodes__REASON_INVALID_CUSTOMER_NAME", 431);
define("GatewayCodes__REASON_INVALID_BANKDATA_LENGTH", 432);
define("GatewayCodes__REASON_INVALID_PIN_NO", 433);
define("GatewayCodes__REASON_INVALID_PHONE_NO", 434);
define("GatewayCodes__REASON_INVALID_ACCOUNT_HOLDER", 435);
define("GatewayCodes__REASON_INCOMPATIBLE_DESCRIPTORS", 436);
define("GatewayCodes__REASON_INVALID_REFERRAL_DATA", 437);
define("GatewayCodes__REASON_INVALID_SITEID", 438);
define("GatewayCodes__REASON_DUPLICATE_INVOICE_ID", 439);
define("GatewayCodes__REASON_EXISTING_MEMBERSHIP", 440);
define("GatewayCodes__REASON_INVOICE_NOT_FOUND", 441);
define("GatewayCodes__REASON_INVALID_BATCH_DURATION", 442);
define("GatewayCodes__REASON_MISSING_CUSTOMER_ID", 443);
define("GatewayCodes__REASON_MISSING_CUSTOMER_NAME", 444);
define("GatewayCodes__REASON_MISSING_CUSTOMER_ADDRESS", 445);
define("GatewayCodes__REASON_MISSING_CVV2", 446);
define("GatewayCodes__REASON_MISSING_PARES", 447);
define("GatewayCodes__REASON_NO_ACTIVE_MEMBERSHIP", 448);
define("GatewayCodes__REASON_INVALID_CVV2", 449);
define("GatewayCodes__REASON_INVALID_3D_DATA", 450);
define("GatewayCodes__REASON_INVALID_CLONE_DATA", 451);
define("GatewayCodes__REASON_REDUNDANT_SUSPEND_OR_RESUME", 452);
define("GatewayCodes__REASON_INVALID_PAYINFO_TRANSACT_ID", 453);
define("GatewayCodes__REASON_INVALID_CAPTURE_DAYS", 454);
define("GatewayCodes__REASON_INVALID_SUBMERCHANT_ID", 455);
define("GatewayCodes__REASON_INVALID_COF_FRAMEWORK", 458);
define("GatewayCodes__REASON_INVALID_REFERENCE_SCHEME_TRANSACTION", 459);
define("GatewayCodes__REASON_INVALID_CUSTOMER_ADDRESS", 460);
?>
