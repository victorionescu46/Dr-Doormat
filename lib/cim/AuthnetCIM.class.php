<?php

class AuthnetCIMException extends Exception {}

class AuthnetCIM
{

    // Testing settings
    const   LOGIN    = '4y5BfuW7jm';
    const   TRANSKEY = '4cAmW927n8uLf5J8';

    // Production settings
//    const   LOGIN    = '4RwPnYej648M';
//    const   TRANSKEY = '4F6W7qpA946PePM9';

    private $test    = true;

    private $params  = array();
    private $items   = array();
    private $success = false;
    private $error   = true;

    private $xml;
    private $response;
    private $resultCode;
    private $code;
    private $text;
    private $profileId;
    private $validation;
    private $paymentProfileId;
    private $paymentProfileIdList = null;
    private $results;

    public function __construct()
    {
        if (!trim(self::LOGIN) || !trim(self::TRANSKEY))
        {
            throw new AuthnetCIMException('You have not configured your Authnet login credentials.');
        }

        $subdomain = ($this->test) ? 'apitest' : 'api';
        $this->url = 'https://' . $subdomain . '.authorize.net/xml/v1/request.api';

        $this->params['customerType']     = 'individual';
        $this->params['validationMode']   = 'liveMode';
        $this->params['taxExempt']        = 'false';
        $this->params['recurringBilling'] = 'false';
    }

    public function isSuccess() {
      return $this->success;
    }

    public function __toString()
    {
        if (!$this->params)
        {
            return (string) $this;
        }

        $output  = '<table summary="Authnet Results" id="authnet">' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Outgoing Parameters</b></th>' . "\n" . '</tr>' . "\n";

        foreach ($this->params as $key => $value)
        {
            $output .= "\t" . '<tr>' . "\n\t\t" . '<td><b>' . $key . '</b></td>';
            $output .= '<td>' . $value . '</td>' . "\n" . '</tr>' . "\n";
        }

        $output .= '</table>' . "\n";
        return $output;
    }

    public function getApiResponse() {
      return $this->response;
    }

    private function process($retries = 3)
    {
        $count = 0;
        while ($count < $retries) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $this->response = curl_exec($ch);
            $this->parseResults();
            if ($this->resultCode === 'Ok') {
                $this->success = true;
                $this->error   = false;
                break;
            }
            else
            {
                $this->success = false;
                $this->error   = true;
                break;
            }

            $count++;
        }
        curl_close($ch);
    }

    public function authAndCapture() {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
          <createCustomerProfileTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
          <merchantAuthentication>
                                        <name>' . self::LOGIN . '</name>
                                        <transactionKey>' . self::TRANSKEY . '</transactionKey>
          </merchantAuthentication>
          <transaction>
          <profileTransAuthCapture>
          <amount>' . $this->params['amount'] . '</amount>
          <customerProfileId>' . $this->params['customerProfileId'] . '</customerProfileId>
          <customerPaymentProfileId>' . $this->params['customerPaymentProfileId'] . '</customerPaymentProfileId>
          <order>
          <invoiceNumber>INV000001</invoiceNumber>
          <description>description of transaction</description>
          <purchaseOrderNumber>PONUM000001</purchaseOrderNumber>
          </order>
          </profileTransAuthCapture>
          </transaction>
          </createCustomerProfileTransactionRequest>';

        $this->process();        
    }

    public function voidTransaction() {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
          <createCustomerProfileTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
          <merchantAuthentication>
                                        <name>' . self::LOGIN . '</name>
                                        <transactionKey>' . self::TRANSKEY . '</transactionKey>
          </merchantAuthentication>
          <transaction>
          <profileTransVoid>
          <transId>' . $this->params['transId'] . '</transId>
          </profileTransVoid>
          </transaction>
          </createCustomerProfileTransactionRequest>';

        $this->process();        
    }

    public function createCustomerProfile($type = 'credit')
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <profile>
                              <merchantCustomerId>'. $this->params['custID'] .'</merchantCustomerId>
                              <description>'. $this->params['description'] .'</description>
                              <email>'. $this->params['email'] .'</email>
                              <paymentProfiles>
                                  <customerType>'. $this->params['customerType'] .'</customerType>
                                  <billTo>
                                      <firstName>'. $this->params['firstName'] .'</firstName>
                                      <lastName>'. $this->params['lastName'] .'</lastName>
                                      <company>'. $this->params['company'] .'</company>
                                      <address>'. $this->params['address'] .'</address>
                                      <city>'. $this->params['city'] .'</city>
                                      <state>'. $this->params['state'] .'</state>
                                      <zip>'. $this->params['zip'] .'</zip>
                                      <country>'. $this->params['country'] .'</country>
                                      <phoneNumber>'. $this->params['phoneNumber'] .'</phoneNumber>
                                      <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                                  </billTo>
                                  <payment>';
        if ($type === 'credit')
        {
            $this->xml .= '
                                      <creditCard>
                                          <cardNumber>'. $this->params['cardNumber'] .'</cardNumber>
                                          <expirationDate>'. $this->params['expirationDate'] .'</expirationDate>
                                      </creditCard>';
        }
        else if ($type === 'check')
        {
            $this->xml .= '
                                      <bankAccount>
                                          <accountType>'. $this->params['accountType'] .'</accountType>
                                          <nameOnAccount>'. $this->params['nameOnAccount'] .'</nameOnAccount>
                                          <echeckType>'. $this->params['echeckType'] .'</echeckType>
                                          <bankName>'. $this->params['bankName'] .'</bankName>
                                          <routingNumber>'. $this->params['routingNumber'] .'</routingNumber>
                                          <accountNumber>'. $this->params['accountNumber'] .'</accountNumber>
                                      </bankAccount>
                                      <driversLicense>
                                          <dlState>'. $this->params['dlState'] .'</dlState>
                                          <dlNumber>'. $this->params['dlNumber'] .'</dlNumber>
                                          <dlDateOfBirth>'. $this->params['dlDateOfBirth'] .'</dlDateOfBirth>
                                      </driversLicense>';
        }
        $this->xml .= '
                                  </payment>
                              </paymentProfiles>
                              <shipToList>
                                  <firstName>'. $this->params['shipFirstName'] .'</firstName>
                                  <lastName>'. $this->params['shipLastName'] .'</lastName>
                                  <company>'. $this->params['shipCompany'] .'</company>
                                  <address>'. $this->params['shipAddress'] .'</address>
                                  <city>'. $this->params['shipCity'] .'</city>
                                  <state>'. $this->params['shipState'] .'</state>
                                  <zip>'. $this->params['shipZip'] .'</zip>
                                  <country>'. $this->params['shipCountry'] .'</country>
                                  <phoneNumber>'. $this->params['shipPhoneNumber'] .'</phoneNumber>
                                  <faxNumber>'. $this->params['shipFaxNumber'] .'</faxNumber>
                              </shipToList>
                          </profile>
                      </createCustomerProfileRequest>';
        $this->process();
    }

    public function createCustomerProfile2($type = 'credit')
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <profile>
                              <merchantCustomerId>'. $this->params['custID'] .'</merchantCustomerId>
                              <paymentProfiles>
                                  <billTo>
                                      <firstName>'. $this->params['firstName'] .'</firstName>
                                      <lastName>'. $this->params['lastName'] .'</lastName>
                                      <address>'. $this->params['address'] .'</address>
                                      <city>'. $this->params['city'] .'</city>
                                      <state>'. $this->params['state'] .'</state>
                                      <zip>'. $this->params['zip'] .'</zip>
                                      <country>'. $this->params['country'] .'</country>
                                      <phoneNumber>'. $this->params['phoneNumber'] .'</phoneNumber>
                                      <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                                  </billTo>
                                  <payment>';
        if ($type === 'credit')
        {
            $this->xml .= '
                                      <creditCard>
                                          <cardNumber>'. $this->params['cardNumber'] .'</cardNumber>
                                          <expirationDate>'. $this->params['expirationDate'] .'</expirationDate>
                                      </creditCard>';
        }
        else if ($type === 'check')
        {
            $this->xml .= '
                                      <bankAccount>
                                          <accountType>'. $this->params['accountType'] .'</accountType>
                                          <nameOnAccount>'. $this->params['nameOnAccount'] .'</nameOnAccount>
                                          <echeckType>'. $this->params['echeckType'] .'</echeckType>
                                          <bankName>'. $this->params['bankName'] .'</bankName>
                                          <routingNumber>'. $this->params['routingNumber'] .'</routingNumber>
                                          <accountNumber>'. $this->params['accountNumber'] .'</accountNumber>
                                      </bankAccount>
                                      <driversLicense>
                                          <dlState>'. $this->params['dlState'] .'</dlState>
                                          <dlNumber>'. $this->params['dlNumber'] .'</dlNumber>
                                          <dlDateOfBirth>'. $this->params['dlDateOfBirth'] .'</dlDateOfBirth>
                                      </driversLicense>';
        }
        $this->xml .= '
                                  </payment>
                              </paymentProfiles>
                          </profile>
                      </createCustomerProfileRequest>';
        $this->process();
    }

    public function createCustomerPaymentProfile($type = 'credit')
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <paymentProfile>
                              <customerType>'. $this->params['customerType'] .'</customerType>
                              <billTo>
                                  <firstName>'. $this->params['firstName'] .'</firstName>
                                  <lastName>'. $this->params['lastName'] .'</lastName>
                                  <company>'. $this->params['company'] .'</company>
                                  <address>'. $this->params['address'] .'</address>
                                  <city>'. $this->params['city'] .'</city>
                                  <state>'. $this->params['state'] .'</state>
                                  <zip>'. $this->params['zip'] .'</zip>
                                  <country>'. $this->params['country'] .'</country>
                                  <phoneNumber>'. $this->params['phoneNumber'] .'</phoneNumber>
                                  <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                              </billTo>
                              <payment>';
        if ($type === 'credit')
        {
            $this->xml .= '
                                  <creditCard>
                                      <cardNumber>'. $this->params['cardNumber'] .'</cardNumber>
                                      <expirationDate>'. $this->params['expirationDate'] .'</expirationDate>
                                  </creditCard>';
        }
        else if ($type === 'check')
        {
            $this->xml .= '
                                  <bankAccount>
                                      <accountType>'. $this->params['accountType'] .'</accountType>
                                      <nameOnAccount>'. $this->params['nameOnAccount'] .'</nameOnAccount>
                                      <echeckType>'. $this->params['echeckType'] .'</echeckType>
                                      <bankName>'. $this->params['bankName'] .'</bankName>
                                      <routingNumber>'. $this->params['routingNumber'] .'</routingNumber>
                                      <accountNumber>'. $this->params['accountNumber'] .'</accountNumber>
                                  </bankAccount>
                                  <driversLicense>
                                      <dlState>'. $this->params['dlState'] .'</dlState>
                                      <dlNumber>'. $this->params['dlNumber'] .'</dlNumber>
                                      <dlDateOfBirth>'. $this->params['dlDateOfBirth'] .'</dlDateOfBirth>
                                  </driversLicense>';
        }
        $this->xml .= '
                              </payment>
                          </paymentProfile>
                          <validationMode>'. $this->params['validationMode'] .'</validationMode>
                      </createCustomerPaymentProfileRequest>';
        $this->process();
    }

    public function createCustomerShippingAddress()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <address>
                              <firstName>'. $this->params['firstName'] .'</firstName>
                              <lastName>'. $this->params['lastName'] .'</lastName>
                              <company>'. $this->params['company'] .'</company>
                              <address>'. $this->params['address'] .'</address>
                              <city>'. $this->params['city'] .'</city>
                              <state>'. $this->params['state'] .'</state>
                              <zip>'. $this->params['zip'] .'</zip>
                              <country>'. $this->params['country'] .'</country>
                              <phoneNumber>'. $this->params['phoneNumber'] .'</phoneNumber>
                              <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                          </address>
                      </createCustomerShippingAddressRequest>';
        $this->process();
    }

    public function createCustomerProfileTransaction($type = 'profileTransAuthCapture')
    {
        $types = array('profileTransAuthCapture', 'profileTransCaptureOnly', 'profileTransAuthOnly');
        if (!in_array($type, $types))
        {
            throw new AuthnetCIMException('createCustomerProfileTransaction() parameter must be "profileTransAuthCapture", "profileTransCaptureOnly", "profileTransAuthOnly", or empty');
        }

        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerProfileTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <transaction>
                              <' . $type . '>
                                  <amount>'. $this->params['amount'] .'</amount>';
        if (isset($this->params['taxAmount']))
        {
            $this->xml .= '
                                  <tax>
                                       <amount>'. $this->params['taxAmount'] .'</amount>
                                       <name>'. $this->params['taxName'] .'</name>
                                       <description>'. $this->params['taxDescription'] .'</description>
                                  </tax>';
        }
        if (isset($this->params['shipAmount']))
        {
            $this->xml .= '
                                  <shipping>
                                       <amount>'. $this->params['shipAmount'] .'</amount>
                                       <name>'. $this->params['shipName'] .'</name>
                                       <description>'. $this->params['shipDescription'] .'</description>
                                  </shipping>';
        }
        if (isset($this->params['dutyAmount']))
        {
            $this->xml .= '
                                  <duty>
                                       <amount>'. $this->params['dutyAmount'] .'</amount>
                                       <name>'. $this->params['dutyName'] .'</name>
                                       <description>'. $this->params['dutyDescription'] .'</description>
                                  </duty>';
        }
        $this->xml .= '
                                  <lineItems>' . $this->getLineItems() . '</lineItems>
                                  <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                                  <customerPaymentProfileId>'. $this->params['customerPaymentProfileId'] .'</customerPaymentProfileId>';
											// <customerShippingAddressId>'. $this->params['customerShippingAddressId'] .'</customerShippingAddressId>';
        if (isset($this->params['orderInvoiceNumber']))
        {
            $this->xml .= '
                                  <order>
                                       <orderInvoiceNumber>'. $this->params['orderInvoiceNumber'] .'</orderInvoiceNumber>
                                       <orderDescription>'. $this->params['orderDescription'] .'</orderDescription>
                                       <orderPurchaseOrderNumber>'. $this->params['orderPurchaseOrderNumber'] .'</orderPurchaseOrderNumber>
                                  </order>';
        }
        $this->xml .= '
                                  <taxExempt>'. $this->params['taxExempt'] .'</taxExempt>
                                  <recurringBilling>'. $this->params['recurringBilling'] .'</recurringBilling>
                                  <cardCode>'. $this->params['cardCode'] .'</cardCode>';
        if (isset($this->params['orderInvoiceNumber']))
        {
            $this->xml .= '
                                  <approvalCode>'. $this->params['approvalCode'] .'</approvalCode>';
        }
        $this->xml .= '
                              </' . $type . '>
                          </transaction>
                      </createCustomerProfileTransactionRequest>';
        $this->process();
    }

    public function deleteCustomerProfile()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <deleteCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                      </deleteCustomerProfileRequest>';
        $this->process();
    }

    public function deleteCustomerPaymentProfile()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <deleteCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <customerPaymentProfileId>'. $this->params['customerProfileId'] .'</customerPaymentProfileId>
                      </deleteCustomerPaymentProfileRequest>';
        $this->process();
    }

    public function deleteCustomerShippingAddress()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <deleteCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <customerAddressId>'. $this->params['customerAddressId'] .'</customerAddressId>
                      </deleteCustomerShippingAddressRequest>';
        $this->process();
    }

    public function getCustomerProfileIds()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <getCustomerProfileIdsRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                      </getCustomerProfileIdsRequest>';
        $this->process();
    }

    public function getCustomerProfile()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <getCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                      </getCustomerProfileRequest>';
        $this->process();
    }

    public function getCustomerPaymentProfile()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <getCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <customerPaymentProfileId>'. $this->params['customerPaymentProfileId'] .'</customerPaymentProfileId>
                      </getCustomerPaymentProfileRequest>';
        $this->process();
    }

    public function getCustomerShippingAddress()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <getCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                              <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                              <customerAddressId>'. $this->params['customerAddressId'] .'</customerAddressId>
                      </getCustomerShippingAddressRequest>';
        $this->process();
    }

    public function updateCustomerProfile()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <updateCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <profile>
                              <merchantCustomerId>'. $this->params['merchantCustomerId'] .'</merchantCustomerId>
                              <description>'. $this->params['description'] .'</description>
                              <email>'. $this->params['email'] .'</email>
                              <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          </profile>
                      </updateCustomerProfileRequest>';
        $this->process();
    }

    public function updateCustomerPaymentProfile2($type = 'credit')
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <updateCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <paymentProfile>
                              <customerType>'. $this->params['customerType'] .'</customerType>
                              <billTo>
                                  <firstName>'. $this->params['firstName'] .'</firstName>
                                  <lastName>'. $this->params['lastName'] .'</lastName>
                                  <address>'. $this->params['address'] .'</address>
                                  <city>'. $this->params['city'] .'</city>
                                  <state>'. $this->params['state'] .'</state>
                                  <zip>'. $this->params['zip'] .'</zip>
                                  <country>'. $this->params['country'] .'</country>
                                  <phoneNumber>'. $this->params['phoneNumber'] .'</phoneNumber>
                                  <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                              </billTo>
                              <payment>
';
        if ($type === 'credit')
        {
            $this->xml .= '
                                      <creditCard>
                                          <cardNumber>'. $this->params['cardNumber'] .'</cardNumber>
                                          <expirationDate>'. $this->params['expirationDate'] .'</expirationDate>
                                      </creditCard>';
        }
        else if ($type === 'check')
        {
            $this->xml .= '
                                      <bankAccount>
                                          <accountType>'. $this->params['accountType'] .'</accountType>
                                          <nameOnAccount>'. $this->params['nameOnAccount'] .'</nameOnAccount>
                                          <echeckType>'. $this->params['echeckType'] .'</echeckType>
                                          <bankName>'. $this->params['bankName'] .'</bankName>
                                          <routingNumber>'. $this->params['routingNumber'] .'</routingNumber>
                                          <accountNumber>'. $this->params['accountNumber'] .'</accountNumber>
                                      </bankAccount>
                                      <driversLicense>
                                          <dlState>'. $this->params['dlState'] .'</dlState>
                                          <dlNumber>'. $this->params['dlNumber'] .'</dlNumber>
                                          <dlDateOfBirth>'. $this->params['dlDateOfBirth'] .'</dlDateOfBirth>
                                      </driversLicense>';
        }
        $this->xml .= '
                                  </payment>
                          <customerPaymentProfileId>' . $this->params['customerPaymentProfileId'] . '</customerPaymentProfileId>
                          </paymentProfile>
                      </updateCustomerPaymentProfileRequest>';
        $this->process();
    }


    public function updateCustomerPaymentProfile($type = 'credit')
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <updateCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <paymentProfile>
                              <customerType>'. $this->params['customerType'] .'</customerType>
                              <billTo>
                                  <firstName>'. $this->params['firstName'] .'</firstName>
                                  <lastName>'. $this->params['lastName'] .'</lastName>
                                  <company>'. $this->params['company'] .'</company>
                                  <address>'. $this->params['address'] .'</address>
                                  <city>'. $this->params['city'] .'</city>
                                  <state>'. $this->params['state'] .'</state>
                                  <zip>'. $this->params['zip'] .'</zip>
                                  <country>'. $this->params['country'] .'</country>
                                  <phoneNumber>'. $this->params['phoneNumber'] .'</phoneNumber>
                                  <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                              </billTo>
                              <payment>';
        if ($type === 'credit')
        {
            $this->xml .= '
                                      <creditCard>
                                          <cardNumber>'. $this->params['cardNumber'] .'</cardNumber>
                                          <expirationDate>'. $this->params['expirationDate'] .'</expirationDate>
                                      </creditCard>';
        }
        else if ($type === 'check')
        {
            $this->xml .= '
                                      <bankAccount>
                                          <accountType>'. $this->params['accountType'] .'</accountType>
                                          <nameOnAccount>'. $this->params['nameOnAccount'] .'</nameOnAccount>
                                          <echeckType>'. $this->params['echeckType'] .'</echeckType>
                                          <bankName>'. $this->params['bankName'] .'</bankName>
                                          <routingNumber>'. $this->params['routingNumber'] .'</routingNumber>
                                          <accountNumber>'. $this->params['accountNumber'] .'</accountNumber>
                                      </bankAccount>
                                      <driversLicense>
                                          <dlState>'. $this->params['dlState'] .'</dlState>
                                          <dlNumber>'. $this->params['dlNumber'] .'</dlNumber>
                                          <dlDateOfBirth>'. $this->params['dlDateOfBirth'] .'</dlDateOfBirth>
                                      </driversLicense>';
        }
        $this->xml .= '
                                  </payment>
                          </paymentProfile>
                      </updateCustomerPaymentProfileRequest>';
        $this->process();
    }

    public function updateCustomerShippingAddress()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <updateCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <address>
                              <firstName>'. $this->params['firstName'] .'</firstName>
                              <lastName>'. $this->params['lastName'] .'</lastName>
                              <company>'. $this->params['company'] .'</company>
                              <address>'. $this->params['address'] .'</address>
                              <city>'. $this->params['city'] .'</city>
                              <state>'. $this->params['state'] .'</state>
                              <zip>'. $this->params['zip'] .'</zip>
                              <country>'. $this->params['country'] .'</country>
                              <phoneNumber>'. $this->params['phoneNumber'] .'</phoneNumber>
                              <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                              <customerAddressId>'. $this->params['customerAddressId'] .'</customerAddressId>
                          </address>
                      </updateCustomerShippingAddressRequest>';
        $this->process();
    }

    public function validateCustomerPaymentProfile()
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <validateCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . self::LOGIN . '</name>
                              <transactionKey>' . self::TRANSKEY . '</transactionKey>
                          </merchantAuthentication>
                          <customerProfileId>'. $this->params['customerProfileId'] .'</customerProfileId>
                          <customerPaymentProfileId>'. $this->params['customerPaymentProfileId'] .'</customerPaymentProfileId>
                          <customerAddressId>'. $this->params['customerAddressId'] .'</customerAddressId>
                          <validationMode>'. $this->params['validationMode'] .'</validationMode>
                      </validateCustomerPaymentProfileRequest>';
        $this->process();
    }

    private function getLineItems()
    {
        $tempXml = '';
        foreach ($this->items as $item)
        {
            foreach ($item as $key => $value)
            {
                $tempXml .= "\t" . '<' . $key . '>' . $value . '</' . $key . '>' . "\n";
            }
        }
        return $tempXml;
    }

    public function setLineItem($itemId, $name, $description, $quantity, $unitprice, $taxable = 'false')
    {
        $this->items[] = array('itemId' => $itemId, 'name' => $name, 'description' => $description, 'quantity' => $quantity, 'unitPrice' => $unitprice, 'taxable' => $taxable);
    }

    public function setParameter($field = '', $value = null)
    {
        $field = (is_string($field)) ? trim($field) : $field;
        $value = (is_string($value)) ? trim($value) : $value;
        if (!is_string($field))
        {
            throw new AuthnetCIMException('setParameter() arg 1 must be a string: ' . gettype($field) . ' given.');
        }
        if (!is_string($value) && !is_numeric($value) && !is_bool($value))
        {
            throw new AuthnetCIMException('setParameter() arg 2 must be a string, integer, or boolean value: ' . gettype($value) . ' given.');
        }
        if (empty($field))
        {
            throw new AuthnetCIMException('setParameter() requires a parameter field to be named.');
        }
        if ($value === '')
        {
            throw new AuthnetCIMException('setParameter() requires a parameter value to be assigned: $field');
        }
        $this->params[$field] = $value;
    }

    private function parseResults()
    {
        $this->resultCode       = $this->parseXML('<resultCode>', '</resultCode>');
        $this->code             = $this->parseXML('<code>', '</code>');
        $this->text             = $this->parseXML('<text>', '</text>');
        $this->validation       = $this->parseXML('<validationDirectResponse>', '</validationDirectResponse>');
        $this->directResponse   = $this->parseXML('<directResponse>', '</directResponse>');
        $this->profileId        = (int) $this->parseXML('<customerProfileId>', '</customerProfileId>');
        $this->addressId        = (int) $this->parseXML('<customerAddressId>', '</customerAddressId>');
        $this->paymentProfileId = (int) $this->parseXML('<customerPaymentProfileId>', '</customerPaymentProfileId>');
        $raw_str = $this->parseXML('<customerPaymentProfileIdList>', '</customerPaymentProfileIdList>');
        $matches = array();
        if(preg_match('/\d+/i', $raw_str, $matches) === 1) {
          $this->paymentProfileIdList = $matches[0];
        }

        $this->results          = explode(',', $this->directResponse);
    }

    public function getPaymentProfileIdList() {
      return $this->paymentProfileIdList;
    }

    private function parseXML($start, $end)
    {
        return preg_replace('|^.*?'.$start.'(.*?)'.$end.'.*?$|i', '$1', substr($this->response, 335));
    }

    public function isSuccessful()
    {
        return $this->success;
    }

    public function isError()
    {
        return $this->error;
    }

    public function getErrorResponse()
    {
        return 'Error code: ' . $this->code . ' Message: ' . strip_tags($this->text);
    }

    public function getResponse()
    {
        return strip_tags($this->text);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getProfileID()
    {
        return $this->profileId;
    }

    public function validationDirectResponse()
    {
        return $this->validation;
    }

    public function getCustomerAddressId()
    {
        return $this->addressId;
    }

    public function getDirectResponse()
    {
        return $this->directResponse;
    }

    public function getPaymentProfileId()
    {
        return $this->paymentProfileId;
    }

    public function getResponseSubcode()
    {
        return $this->results[1];
    }

    public function getResponseCode()
    {
        return $this->results[2];
    }

    public function getResponseText()
    {
        return $this->results[3];
    }

    public function getAuthCode()
    {
        return $this->results[4];
    }

    public function getAVSResponse()
    {
        return $this->results[5];
    }

    public function getTransactionID()
    {
        return $this->results[6];
    }

    public function getCVVResponse()
    {
        return $this->results[38];
    }

    public function getCAVVResponse()
    {
        return $this->results[39];
    }
}

?>
