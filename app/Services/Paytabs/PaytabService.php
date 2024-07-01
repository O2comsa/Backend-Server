<?php
// Copyright
declare(strict_types=1);

namespace App\Services\Paytabs;

class PaytabService
{
    private $testing;
    private $transaction_url;
    private $verify_url;
    private $profile_id;
    private $server_key;

    public $responseResult;
    private $responseCode;
    private $responseMessage;

    public $errors;
    public $success = false;

    private $country;
    private $callback_url;
    private $site_url;
    private $currency;
    private $details;

    public function __construct()
    {
        $this->transaction_url = config('paytabs.TRANSACTION_URL');
        $this->verify_url = config('paytabs.VERIFY_URL');

        $this->profile_id = config('paytabs.PROFILE_ID');

        $this->country = config('paytabs.COUNTRY');
        $this->callback_url = route('paytabs.verify_payment');
        $this->site_url = config('paytabs.SITE_URL');
        $this->currency = config('paytabs.CURRENCY');
        $this->server_key = config('paytabs.SERVER_KEY');
    }


    public function create_pay_page($values)
    {

        $values['profile_id'] = $this->profile_id;
        $values['tran_type'] = 'sale';
        $values['tran_class'] = 'ecom';
        $values['hide_shipping'] = true;

        $values['cart_currency'] = $this->currency;
        $values["return"] = $this->site_url;
        $values['callback'] = $this->callback_url;

        $this->runPost($this->transaction_url, json_encode($values));

//        if ($this->isSuccess()) {
//            $this->paymentURL = $this->responseResult->payment_url;
//            $this->payPageID = $this->responseResult->p_id;
//        }

        return $this;
    }

    public function send_request()
    {
        $values['ip_customer'] = $_SERVER['REMOTE_ADDR'];
        $values['ip_merchant'] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '::1';
        return json_decode($this->runPost($this->testing, $values));
    }

    public function verify_payment($callback_request): object
    {

        /// there is to way for validation payment
        /// The response from the transaction API can be grouped into 3 main categories:
        /// Result:
        /// If the transaction can be processed without requiring any additional details, then the response from the API will be the final transaction results.
        /// {
        //  "tran_ref": "TST2014900000688",
        //  "cart_id": "Sample Payment",
        //  "cart_description": "Sample Payment",
        //  "cart_currency": "AED",
        //  "cart_amount": "1",
        //  "customer_details": {
        //    "name": "John Smith",
        //    "email": "jsmith@gmail.com",
        //    "phone": "9711111111111",
        //    "street1": "404, 11th st, void",
        //    "city": "Dubai",
        //    "state": "DU",
        //    "country": "AE",
        //    "ip": "127.0.0.1"
        //  },
        //  "payment_result": {
        //    "response_status": "A",
        //    "response_code": "831000",
        //    "response_message": "Authorised",
        //    "acquirer_message": "ACCEPT",
        //    "acquirer_rrn": "014910159369",
        //    "transaction_time": "2020-05-28T14:35:38+04:00"
        //  },
        //  "payment_info": {
        //    "card_type": "Credit",
        //    "card_scheme": "Visa",
        //    "payment_description": "4111 11## #### 1111"
        //  }
        /// }
        ///
        /// Redirection:
        //If for any reason additional information is needed from the customer, then the system will trigger a browser redirect. You must direct the customer to the URL provided, using the method indicated.
        //
        //This can happen if (for example) the customers card issuer requires 3D Secure authentication of the customer.
        //
        //REDIRECT SAMPLE:
        //{
        //  "tran_ref": "TST2011800000216",
        //  "cart_id": "4244b9fd-c7e9-4f16-8d3c-4fe7bf6c48ca",
        //  "cart_description": "Dummy Order 123456",
        //  "cart_currency": "AED",
        //  "cart_amount": "46.17",
        //  "callback": "https://yourdomain.com/yourcallback",
        //  "return": "https://yourdomain.com/yourpage",
        //  "redirect_url": "https://secure.paytabs.sa/payment/page/REF/redirect"
        //}
        $data = null;

        if (!empty($callback_request['payment_result'])) {
            if ($callback_request['payment_result']['response_status'] == "A") {
                $data = [
                    'success' => true,
                    'responseResult' => (object)$callback_request,
                ];
            } else {
                $data = [
                    'success' => false,
                    'responseResult' => (object)$callback_request,
                ];
            }
        } elseif (
            !empty($callback_request['respCode']) &&
            !empty($callback_request['respStatus']) &&
            !empty($callback_request['signature']) &&
            $callback_request['respStatus'] == "A"
        ) {
            if ($this->decodeToken($callback_request)) {
                $data = [
                    'success' => true,
                    'responseResult' => (object)$callback_request,
                ];
            } else {
                $data = [
                    'success' => false,
                    'responseResult' => (object)$callback_request,
                ];
            }
        } else {
            $data = [
                'success' => false,
                'responseResult' => null,
            ];
        }

        return (object)$data;
    }

    public function decodeToken($signature_fields)
    {
        // Profile Key (ServerKey)
        $serverKey = $this->server_key; // Example

        // Request body include a signature post Form URL encoded field
        // 'signature' (hexadecimal encoding for hmac of sorted post form fields)
//        $signature_fields = filter_input_array(INPUT_POST);
        $requestSignature = $signature_fields["signature"];
        unset($signature_fields["signature"]);

        // Ignore empty values fields
        $signature_fields = array_filter($signature_fields);

        // Sort form fields
        ksort($signature_fields);

        // Generate URL-encoded query string of Post fields except signature field.
        $query = http_build_query($signature_fields);

        $signature = hash_hmac('sha256', $query, $serverKey);

        if (hash_equals($signature, $requestSignature) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    public function runPost($url, $fields)
    {
        $ch = curl_init();

        $ip_address = array(
            'Content-Type: application/json',
            "Authorization: " . $this->server_key
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $ip_address);
        curl_setopt($ch, CURLOPT_POST, count((array)json_decode($fields)));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 1);

        $result = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $result = json_decode($result);

        $this->responseResult = $result;
        $this->responseCode = (int)$http_code;

        if (isset($result) && $http_code == 200) {
            $this->responseMessage = $result;
        } else {
            $this->details = $result->message;
        }

        $this->handleErrors();

        return $this;
    }

    #region Setters && getters
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function getResponseResult()
    {
        return $this->responseResult;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function isSuccess()
    {
        return $this->success;
    }

    public function isFail()
    {
        return !$this->success;
    }

    #endregion

    protected function handleErrors()
    {
        switch ($this->responseCode) {
            case 200:
            case 100:
            case 4000:
            case 4012:
                $this->success = true;
                unset($this->errors);
                break;
            default:
                $this->success = false;
                $this->errors = [
                    'code' => $this->responseCode,
                    'message' => $this->responseMessage ?? $this->details
                ];
        }
        return $this;
    }
}
