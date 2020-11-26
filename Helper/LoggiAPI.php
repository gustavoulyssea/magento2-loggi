<?php
/**
 * @author Gustavo Ulyssea - gustavo.ulyssea@gmail.com
 * @copyright Copyright (c) 2020 GumNet (https://gum.net.br)
 * @package GumNet AME
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY GUM Net (https://gum.net.br). AND CONTRIBUTORS
 * ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE FOUNDATION OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace GumNet\Loggi\Helper;

use \Ramsey\Uuid\Uuid;
use \Magento\Quote\Model\Quote\Address\RateRequest;


class API
{
    protected $url;
    protected $_logger;
    protected $_mlogger;
    protected $_connection;
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_db;
    protected $_email;
    protected $_gumapi;

    public function __construct(\GumNet\Loggi\Helper\LoggerLoggi $logger,
                                \Psr\Log\LoggerInterface $mlogger,
                                \Magento\Framework\App\ResourceConnection $resource,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \GumNet\Loggi\Helper\Db $db,
                                \GumNet\Loggi\Helper\Mailer $email,
                                \GumNet\Loggi\Helper\GumApi $gumApi,
                                \GumNet\Loggi\Helper\Mlogger $nmlogger
    )
    {
        $this->_logger = $logger;
        $this->_mlogger = $mlogger;
        $this->_connection = $resource->getConnection();
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_db = $db;

        if(!$this->_scopeConfig->getValue('loggi/general/debug_log', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $this->_mlogger = $nmlogger;
        }
        $this->url = "https://staging.loggi.com/graphql";
        $this->_email = $email;
        $this->_gumapi = $gumApi;
    }
    public function getQuote(RateRequest $request)
    {
        $items = $request->getAllItems();
        $origCountry = $request->getOrigCountryId();
        $origRegionId = $request->getOrigRegionId();
        $origPostCore = $request->getOrigPostcode();
        $origCity = $request->getOrigCity();
        $destCountryId = $request->getDestCountryId();


        /* @method int getStoreId()
 * @method \Magento\Quote\Model\Quote\Address\RateRequest setDestCountryId(string $value)
 * @method string getDestCountryId()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setDestRegionId(int $value)
 * @method int getDestRegionId()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setDestRegionCode(string $value)
 * @method string getDestRegionCode()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setDestPostcode(string $value)
 * @method string getDestPostcode()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setDestCity(string $value)
 * @method string getDestCity()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setDestStreet(string $value)
 * @method string getDestStreet()
    *
 * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageValue(float $value)
 * @method float getPackageValue()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageValueWithDiscount(float $value)
 * @method float getPackageValueWithDiscount()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackagePhysicalValue(float $value)
 * @method float getPackagePhysicalValue()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageQty(float $value)
 * @method float getPackageQty()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageWeight(float $value)
 * @method float getPackageWeight()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageHeight(int $value)
 * @method int getPackageHeight()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageWidth(int $value)
 * @method int getPackageWidth()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageDepth(int $value)
 * @method int getPackageDepth()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setPackageCurrency(string $value)
 * @method string getPackageCurrency()
    *
 * @method \Magento\Quote\Model\Quote\Address\RateRequest setOrderTotalQty(float $value)
 * @method float getOrderTotalQty()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setOrderSubtotal(float $value)
 * @method float getOrderSubtotal()
    *
 * @method boolean getFreeShipping()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setFreeShipping(boolean $flag)
 * @method float getFreeMethodWeight()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setFreeMethodWeight(float $value)
 *
 * @method \Magento\Quote\Model\Quote\Address\RateRequest setOptionInsurance(boolean $value)
 * @method boolean getOptionInsurance()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setOptionHandling(float $flag)
 * @method float getOptionHandling()
    *
 * @method \Magento\Quote\Model\Quote\Address\RateRequest setConditionName(array|string $value)
 * @method array|string getConditionName()
    *
 * @method \Magento\Quote\Model\Quote\Address\RateRequest setLimitCarrier(string $value)
 * @method string getLimitCarrier()
    * @method \Magento\Quote\Model\Quote\Address\RateRequest setLimitMethod(string $value)
 * @method string getLimitMethod()
    */


    }
    public function refundOrder($ame_id, $amount)
    {
        $this->_mlogger->info("AME REFUND ORDER:" . $ame_id);
        $this->_mlogger->info("AME REFUND amount:" . $amount);

        $transaction_id = $this->_db->getTransactionIdByOrderId($ame_id);
        $this->_mlogger->info("AME REFUND TRANSACTION:" . $transaction_id);

        $refund_id = Uuid::uuid4()->toString();
        while($this->_db->refundIdExists($refund_id)){
            $refund_id = Uuid::uuid4()->toString();
        }
        $this->_mlogger->info("AME REFUND ID:" . $refund_id);
        $url = $this->url . "/payments/" . $transaction_id . "/refunds/MAGENTO-" . $refund_id;
        $this->_mlogger->info("AME REFUND URL:" . $url);
//        echo $url;
        $json_array['amount'] = $amount;
        $json = json_encode($json_array);
        $this->_mlogger->info("AME REFUND JSON:" . $json);
        $result[0] = $this->ameRequest($url, "PUT", $json);
//        echo $result[0];
        $this->_mlogger->info("AME REFUND Result:" . $result[0]);
        if ($this->hasError($result[0], $url, $json)) return false;
        $result[1] = $refund_id;
        return $result;
    }
    public function cancelOrder($ame_id)
    {
        $transaction_id = $this->_db->getTransactionIdByOrderId($ame_id);
        if (!$transaction_id) {
//            echo "Transaction ID not found";
            return false;
        }
        $url = $this->url . "/wallet/user/payments/" . $transaction_id . "/cancel";
        $result = $this->ameRequest($url, "PUT", "");
        if ($this->hasError($result, $url, "")) return false;
        return true;
    }
    public function captureOrder($ame_id)
    {
        $ame_transaction_id = $this->_db->getTransactionIdByOrderId($ame_id);
        $url = $this->url . "/wallet/user/payments/" . $ame_transaction_id . "/capture";
        $result = $this->ameRequest($url, "PUT", "");
        if ($this->hasError($result, $url)) return false;
        $result_array = json_decode($result, true);

        return $result_array;
    }
    public function createOrder($order)
    {
        $url = $this->url . "/orders";

        $shippingAmount = $order->getShippingAmount();
        $productsAmount = $order->getGrandTotal() - $shippingAmount;
        $amount = intval($order->getGrandTotal() * 100);
//        $cashbackAmountValue = intval($this->getCashbackPercent() * $amount * 0.01);

        $json_array['title'] = "GumNet Pedido " . $order->getIncrementId();
        $json_array['description'] = "Pedido " . $order->getIncrementId();
        $json_array['amount'] = $amount;
        $json_array['currency'] = "BRL";
//        $json_array['attributes']['cashbackamountvalue'] = $cashbackAmountValue;
        $json_array['attributes']['transactionChangedCallbackUrl'] = $this->getCallbackUrl();
        $json_array['attributes']['items'] = [];

        $items = $order->getAllItems();
        $amount = 0;
        $total_discount = 0;
        foreach ($items as $item) {
            if (isset($array_items)) unset($array_items);
            $array_items['description'] = $item->getName() . " - SKU " . $item->getSku();
            $array_items['quantity'] = intval($item->getQtyOrdered());
            $array_items['amount'] = intval(($item->getRowTotal() - $item->getDiscountAmount()) * 100);
            $products_amount = $amount + $array_items['amount'];
            $total_discount = $total_discount + abs($item->getDiscountAmount());
            array_push($json_array['attributes']['items'], $array_items);
        }
        if($total_discount){
//            $amount = intval($products_amount + $shippingAmount * 100);
//            $json_array['amount'] = $amount;
//            $cashbackAmountValue = intval($this->getCashbackPercent() * $products_amount * 0.01);
//            $json_array['attributes']['cashbackamountvalue'] = $cashbackAmountValue;
        }

        $json_array['attributes']['customPayload']['ShippingValue'] = intval($order->getShippingAmount() * 100);
        $json_array['attributes']['customPayload']['shippingAddress']['country'] = "BRA";

        $number_line = $this->_scopeConfig->getValue('ame/address/number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $json_array['attributes']['customPayload']['shippingAddress']['number'] = $order->getShippingAddress()->getStreet()[$number_line];

        $json_array['attributes']['customPayload']['shippingAddress']['city'] = $order->getShippingAddress()->getCity();

        $street_line = $this->_scopeConfig->getValue('ame/address/street', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $json_array['attributes']['customPayload']['shippingAddress']['street'] = $order->getShippingAddress()->getStreet()[$street_line];

        $json_array['attributes']['customPayload']['shippingAddress']['postalCode'] = $order->getShippingAddress()->getPostcode();

        $neighborhood_line = $this->_scopeConfig->getValue('ame/address/neighborhood', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $json_array['attributes']['customPayload']['shippingAddress']['neighborhood'] = $order->getShippingAddress()->getStreet()[$neighborhood_line];

        $json_array['attributes']['customPayload']['shippingAddress']['state'] = $this->codigoUF($order->getShippingAddress()->getRegion());

        $json_array['attributes']['customPayload']['billingAddress'] = $json_array['attributes']['customPayload']['shippingAddress'];
        $json_array['attributes']['customPayload']['isFrom'] = "MAGENTO";
        $json_array['attributes']['paymentOnce'] = true;
        $json_array['attributes']['riskHubProvider'] = "SYNC";
        $json_array['attributes']['origin'] = "ECOMMERCE";

        $json = json_encode($json_array);
        $result = $this->ameRequest($url, "POST", $json);

        if ($this->hasError($result, $url, $json)) return false;
        $this->_gumapi->createOrder($json,$result);
        $this->_logger->log($result, "info", $url, $json);
        $result_array = json_decode($result, true);

        $this->_db->insertOrder($order,$result_array);

        $this->_logger->log($result, "info", $url, $json);
        return $result;
    }
    public function getCallbackUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . "m2amecallbackendpoint";
    }
    public function hasError($result, $url, $input = "")
    {
        $result_array = json_decode($result, true);
        if (is_array($result_array)) {
            if (array_key_exists("error", $result_array)) {
                $this->_logger->log($result, "error", $url, $input);
                $subject = "AME Error";
                $message = "Result: ".$result."\r\n\r\nurl: ".$url."\r\n\r\n";
                if($input){
                    $message = $message . "Input: ".$input;
                }
                $this->_email->sendDebug($subject,$message);
                return true;
            }
        } else {
            $this->_mlogger->info("ameRequest hasError:" . $result);
            return true;
        }
        return false;
    }
//    public function getCashbackPercent()
//    {
//        return $this->_scopeConfig->getValue('ame/general/cashback_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
//    }
    public function getStoreName()
    {
        return $this->_scopeConfig->getValue('ame/general/store_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function createOrder($order){
        $recipient['name'] = $order->getShippingAddress()->getFirstName()
            . " " . $order->getShippingAddress()->getLastName();
        $recipient['phone'] = $order->getShippingAddress()->getTelephone();
//        $address['lat']
//        $address['lng']
        $address['address'] = $rua.", ".$numero." - ".$bairro.", ".$estado.", ".$cep.", Brasil";
        $address['complement'] = $complemento;

        $dimensions['width'] = $largura_cm;
        $dimensions['length'] = $comprimento_cm;
        $dimensions['height'] = $altura_cm;
        $dimensions['weight'] = $peso_gramas;

        "recipient: {
          name: \"Baruch Spinoza\"
  phone: "1122819603"
}"

        /*
         * formas de pagamento
         * Cartão de crédito	1
Cartão de débito	2
Dinheiro sem troco	4
Dinheiro com troco	8
Cheque	16
Pagamento com maquininha da loja	32
Pagamento já realizado	64
Vale-Refeição	128
Sodexo	256
Alelo	512
Ticket	1024
         */

    }

    public function getAllPackages(){
        $json = "query {
                  allPackages(shopId: 10) {
                    edges {
                      node {
                        pk
                        status
                        orderId
                        orderStatus
                      }
                    }
                  }
                }";
        /*
         * exemplo de saida
         * {
              "data": {
                "allPackages": {
                  "edges": [
                    {
                      "node": {
                        "pk": 80456,
                        "status": null,
                        "orderId": 33860,
                        "orderStatus": "dropped"
                      }
                    },
                    {
                      "node": {
                        "pk": 80455,
                        "status": null,
                        "orderId": 33859,
                        "orderStatus": "dropped"
                      }
                    }
                  ]
                }
              }
            }
         */
    }
    public function getAllShops(){
        $json = "query {
                  allShops {
                    edges {
                      node {
                        name
                        pickupInstructions
                        pk
                        externalId
                        address {
                          pos
                          addressSt
                          addressData
                        }
                        chargeOptions {
                          label
                        }
                      }
                    }
                  }
                }";
        /*
         * Exemplo de saida
         * {"data": {
                    "allShops": {
                      "edges": [
                        {
                          "node": {
                            "name": "Coelho Burguer",
                            "pickupInstructions": "Retirar pacotes no balcão",
                            "pk": 129,
                            "externalId": "my_external_id"
                            "pricingAreaDiscount": {
                              "value": "4.00",
                              "percentage": "31.01"
                            },
                            "address": {
                              "pos": "{ \"type\": \"Point\", \"coordinates\": [ -46.6516703, -23.5516433 ] }",
                              "addressSt": "Rua Augusta",
                              "addressData": "{\"geometry\": {\"location\": {\"lat\": -23.5516433, \"lng\": -46.6516703}}, \"address_components\": [{\"long_name\": \"588\", \"short_name\": \"588\", \"types\": [\"street_number\"]}, {\"long_name\": \"Rua Augusta\", \"short_name\": \"R. Augusta\", \"types\": [\"route\"]}, {\"long_name\": \"Consola\\u00e7\\u00e3o\", \"short_name\": \"Consola\\u00e7\\u00e3o\", \"types\": [\"political\", \"sublocality\", \"sublocality_level_1\"]}, {\"long_name\": \"S\\u00e3o Paulo\", \"short_name\": \"S\\u00e3o Paulo\", \"types\": [\"administrative_area_level_2\", \"political\"]}, {\"long_name\": \"S\\u00e3o Paulo\", \"short_name\": \"SP\", \"types\": [\"administrative_area_level_1\", \"political\"]}, {\"long_name\": \"Brazil\", \"short_name\": \"BR\", \"types\": [\"country\", \"political\"]}, {\"long_name\": \"01304\", \"short_name\": \"01304\", \"types\": [\"postal_code\", \"postal_code_prefix\"]}], \"partial_match\": true, \"formatted_address\": \"R. Augusta, 588 - Consola\\u00e7\\u00e3o, S\\u00e3o Paulo - SP, Brazil\", \"types\": [\"street_address\"]}"
                            },
                            "chargeOptions": [
                              {
                                "label": "Cartão de Crédito"
                              },
                              {
                                "label": "Cartão de Débito"
                              },
                              {
                                "label": "Dinheiro com troco"
                              },
                              {
                                "label": "Dinheiro sem troco"
                              },
                              {
                                "label": "Máquina da loja"
                              },
                              {
                                "label": "Cheque"
                              },
                              {
                                "label": "Não há cobrança"
                              },
                              {
                                "label": "Vale refeição VR"
                              },
                              {
                                "label": "Vale refeição Sodexo"
                              },
                              {
                                "label": "Vale refeição Alelo"
                              },
                              {
                                "label": "Vale refeição Ticket"
                              }
                            ]
                          }
                        }
                      ]
                    }
                  }
                }
         */
        return $this->loggiRequest($this->url,"POST",$json);
    }
    public function loggiRequest($url, $method = "GET", $json = "")
    {
        $this->_mlogger->info("loggiRequest starting...");
        $_apiKey = $this->getToken();
        if (!$_apiKey) return false;
        $method = strtoupper($method);
        $this->_mlogger->info("loggiRequest URL:" . $url);
        $this->_mlogger->info("loggiRequest METHOD:" . $method);
        if ($json) {
            $this->_mlogger->info("loggiRequest JSON:" . $json);
        }
        $username = $this->_scopeConfig->getValue('gum_loggi/general/api_user', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: ApiKey " $username.":". $_apiKey));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method == "POST" || $method == "PUT") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $this->_mlogger->info("ameRequest OUTPUT:" . $result);
        $this->_logger->log(curl_getinfo($ch, CURLINFO_HTTP_CODE), "header", $url, $json);
        $this->_logger->log($result, "info", $url, $json);
        curl_close($ch);
        return $result;
    }
    public function getApiKey()
    {
        $this->_mlogger->info("loggiRequest getToken starting...");
        // check if existing token will be expired within 10 minutes
        if($apiKey = $this->_db->getApiKey()){
            return $apiKey;
        }
        // get user & pass from core_config_data
        $username = $this->_scopeConfig->getValue('gum_loggi/general/api_user', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $password = $this->_scopeConfig->getValue('gum_loggi/general/api_password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$username || !$password) {
            $this->_logger->log("user/pass not found on db", "error", "-", "-");
            return false;
        }
        $post = 'mutation { login ( input:  { email: "'.$username.'", password: "'.$password.'" }) { user { apiKey } } }';
        $url = $this->url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/x-www-form-urlencoded',
//        ));
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $status = $info['http_code'];
        if($status!="200") return false;

        $result_array = json_decode($result, true);
        $apiKey = $result_array['data']['login']['user']['apiKey'];
        $this->_logger->log($result, "info", $url, $username . ":" . $password);

        $expires_in = (int)time() + intval($result_array['expires_in']);
        $this->_db->updateToken($expires_in,$result_array['access_token']);
        return $apiKey;
    }
    public function codigoUF($txt_uf)
    {
        $array_ufs = array("Rondônia" => "RO",
            "Acre" => "AC",
            "Amazonas" => "AM",
            "Roraima" => "RR",
            "Pará" => "PA",
            "Amapá" => "AP",
            "Tocantins" => "TO",
            "Maranhão" => "MA",
            "Piauí" => "PI",
            "Ceará" => "CE",
            "Rio Grande do Norte" => "RN",
            "Paraíba" => "PB",
            "Pernambuco" => "PE",
            "Alagoas" => "AL",
            "Sergipe" => "SE",
            "Bahia" => "BA",
            "Minas Gerais" => "MG",
            "Espírito Santo" => "ES",
            "Rio de Janeiro" => "RJ",
            "São Paulo" => "SP",
            "Paraná" => "PR",
            "Santa Catarina" => "SC",
            "Rio Grande do Sul (*)" => "RS",
            "Mato Grosso do Sul" => "MS",
            "Mato Grosso" => "MT",
            "Goiás" => "GO",
            "Distrito Federal" => "DF");
        $uf = "RJ";
        foreach ($array_ufs as $key => $value) {
            if ($key == $txt_uf) {
                $uf = $value;
                break;
            }
        }
        return $uf;
    }
}
