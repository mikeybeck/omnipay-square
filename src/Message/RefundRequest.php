<?php

namespace Omnipay\Square\Message;

use Omnipay\Common\Message\AbstractRequest;
use SquareConnect;

/**
 * Square Refund Request
 */
class RefundRequest extends AbstractRequest
{

    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    public function setAccessToken($value)
    {
        return $this->setParameter('accessToken', $value);
    }

    public function getLocationId()
    {
        return $this->getParameter('locationId');
    }

    public function setLocationId($value)
    {
        return $this->setParameter('locationId', $value);
    }

    public function getCheckoutId()
    {
        return $this->getParameter('checkoutId');
    }

    public function setCheckoutId($value)
    {
        return $this->setParameter('ReceiptId', $value);
    }

    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    public function setTransactionId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    public function setTransactionReference($value)
    {
        return $this->setParameter('transactionReference', $value);
    }

    public function getIdempotencyKey()
    {
        return $this->getParameter('idempotencyKey');
    }

    public function setIdempotencyKey($value)
    {
        return $this->setParameter('idempotencyKey', $value);
    }

    public function getNonce()
    {
        return $this->getParameter('nonce');
    }

    public function setNonce($value)
    {
        return $this->setParameter('nonce', $value);
    }

    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    public function getCustomerCardId()
    {
        return $this->getParameter('customerCardId');
    }

    public function setCustomerCardId($value)
    {
        return $this->setParameter('customerCardId', $value);
    }

    public function getTenderId()
    {
        return $this->getParameter('tenderId');
    }

    public function setTenderId($value)
    {
        return $this->setParameter('tenderId', $value);
    }

    public function getReason()
    {
        return $this->getParameter('reason');
    }

    public function setReason($value)
    {
        return $this->setParameter('reason', $value);
    }

    public function getData()
    {
        $data = [];

		$data['location_id'] = $this->getLocationId();
		$data['transaction_id'] = $this->getTransactionId();
		$data['body'] = new \SquareConnect\Model\CreateRefundRequest();
		$data['body']->setIdempotencyKey($this->getIdempotencyKey());
		$data['body']->setTenderId($this->getTenderId());
		$data['body']->setReason($this->getReason());
		$money = new \SquareConnect\Model\Money();
		$money->setAmount($this->getAmountInteger());
		$money->setCurrency($this->getCurrency());
		$data['body']->setAmountMoney($money);

        return $data;
    }

    public function sendData($data)
    {

        SquareConnect\Configuration::getDefaultConfiguration()->setAccessToken($this->getAccessToken());

        $api_instance = new SquareConnect\Api\TransactionsApi();


        try {
            $result = $api_instance->createRefund($data['location_id'], $data['transaction_id'], $data['body']);

            if ($error = $result->getErrors()) {
                $response = [
                    'status' => 'error',
                    'code' => $error['code'],
                    'detail' => $error['detail']
                ];
            } else {
                $response = [
                    'status' 			=> $result->getRefund()->getStatus(),
                    'id' 				=> $result->getRefund()->getId(),
                    'location_id' 		=> $result->getRefund()->getLocationId(),
                    'transaction_id'	=> $result->getRefund()->getTransactionId(),
                    'tender_id'			=> $result->getRefund()->getTenderId(),
                    'created_at'		=> $result->getRefund()->getCreatedAt(),
                    'reason'			=> $result->getRefund()->getReason(),
                    'amount'			=> $result->getRefund()->getAmountMoney()->getAmount(),
                    'currency'          => $result->getRefund()->getAmountMoney()->getCurrency(),
                ];
                $processing_fee = $result->getRefund()->getProcessingFeeMoney();
                if (!empty($processing_fee)) {
                    $response['processing_fee']	= $processing_fee->getAmount();
				}
            }
            return $this->createResponse($response);
        } catch (Exception $e) {
            echo 'Exception when creating transaction: ', $e->getMessage(), PHP_EOL;
        }
    }

    public function createResponse($response)
    {
        return $this->response = new RefundResponse($this, $response);
    }
}
