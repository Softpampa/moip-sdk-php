<?php

/**
 * Moip Payments Payments API
 *
 * @since 0.0.1
 * @see http://dev.moip.com.br/referencia-api/#pagamentos Official Documentation
 * @author Nícolas Luís Huber <nicolasluishuber@gmail.com>
 */

namespace Softpampa\Moip\Payments\Resources;

use DateTime;
use stdClass;
use Softpampa\Moip\MoipResource;
use Softpampa\Moip\Payments\Resources\Customers;

class Payments extends MoipResource {

    /**
     * @const  string  METHOD_CREDIT_CARD  Payment method
     */
    const METHOD_CREDIT_CARD = 'CREDIT_CARD';

    /**
     * @const  string  METHOD_BOLETO  Payment method
     */
    const METHOD_BOLETO = 'BOLETO';

    /**
     * @var  string  $path
     */
    protected $path = 'payments';

    /**
     * @var  Orders  $order
     */
    protected $order;

    /**
     * Initialize a resource
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->data->installmentCount = 1;
        $this->data->fundingInstrument = new stdClass();
        $this->data->fundingInstrument->method = self::METHOD_CREDIT_CARD;
    }

    /**
     * Find a payment
     *
     * @param  int  $payment_id
     * @return $this
     */
    public function find($payment_id)
    {
        return $this->populate($this->client->get('/{payment_id}', ['payment_id' => $payment_id]));
    }

    /**
     * Execute a payment
     *
     * @return $this
     */
    public function execute()
    {
        $this->client->setPath($this->order->getPath());

        $this->client->post('/{order_id}/payments', ['order_id' => $this->order->id], $this->data);

        return $this;
    }

    /**
     * Set order
     *
     * @param Orders $order
     * @retrun $this
     */
    public function setOrder(Orders $order)
    {
        $this->order = $order;

        return $this;
    }

    public function setFundingInstrument(stdClass $fundingInstrument)
    {
        $this->data->fundingInstrument = $fundingInstrument;

        return $this;
    }

    /**
     * Set a credit card
     *
     * @param  int  $expirationMonth
     * @param  int  $expirationYear
     * @param  int  $number
     * @param  int  $cvc
     * @param  Customer  $holder
     * @return $this
     */
    public function setCreditCard($expirationMonth, $expirationYear, $number, $cvc, Customers $holder)
    {
        $this->data->fundingInstrument->method = self::METHOD_CREDIT_CARD;
        $this->data->fundingInstrument->creditCard = new stdClass();
        $this->data->fundingInstrument->creditCard->expirationMonth = $expirationMonth;
        $this->data->fundingInstrument->creditCard->expirationYear = $expirationYear;
        $this->data->fundingInstrument->creditCard->number = $number;
        $this->data->fundingInstrument->creditCard->cvc = $cvc;
        $this->setCreditCardHolder($holder);

        return $this;
    }

    /**
     * Set credit card holder.
     *
     * @param  Customer  $holder
     */
    private function setCreditCardHolder(Customers $holder)
    {
        $birthdate = $holder->birthDate;

        if ($birthdate instanceof DateTime) {
            $birthdate = $birthdate->format('Y-m-d');
        }

        $this->data->fundingInstrument->creditCard->holder = new stdClass();
        $this->data->fundingInstrument->creditCard->holder->fullname = $holder->fullname;
        $this->data->fundingInstrument->creditCard->holder->birthdate = $birthdate;
        $this->data->fundingInstrument->creditCard->holder->taxDocument = $holder->taxDocument;
        $this->data->fundingInstrument->creditCard->holder->phone = $holder->phone;

        var_dump($this->data->fundingInstrument->creditCard->holder->birthdate);
    }

    /**
     * Set credit card hash.
     *
     * @param  string  $hash
     * @param  Customer  $holder
     */
    public function setCreditCardHash($hash, Customer $holder)
    {
        $this->data->fundingInstrument->method = self::METHOD_CREDIT_CARD;
        $this->data->fundingInstrument->creditCard = new stdClass();
        $this->data->fundingInstrument->creditCard->hash = $hash;
        $this->setCreditCardHolder($holder);

        return $this;
    }

}