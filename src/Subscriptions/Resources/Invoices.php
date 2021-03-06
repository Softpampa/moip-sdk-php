<?php

/**
 * Moip Subscription Invoices API
 *
 * @since 0.0.1
 * @see http://dev.moip.com.br/assinaturas-api/#faturas Official Documentation
 * @author Nícolas Luís Huber <nicolasluishuber@gmail.com>
 */

namespace Softpampa\Moip\Subscriptions\Resources;

use Softpampa\Moip\MoipResource;

class Invoices extends MoipResource
{

    /**
     * Resource name
     *
     * @var string
     */
    protected $resource = 'invoices';

    /**
     * Find a invoice
     *
     * @param  int  $id
     * @return $this
     */
    public function find($id)
    {
        $this->populate($this->client->get('/{id}', [$id]));

        return $this;
    }

    /**
     * Return all payments from a invoice
     *
     * @return \Illuminate\Support\Collection
     */
    public function payments()
    {
        return $this->client->get('/{id}/payments', [$this->data->id])
                            ->setDataKey('payments')
                            ->getResults();
    }
}
