<?php

namespace Webkul\YapePlin\Payment;

use Webkul\Payment\Payment\Payment;

class YapePlin extends Payment
{
    /**
     * Payment method code - must match payment-methods.php key.
     *
     * @var string
     */
    protected $code = 'yapeplin';

    /**
     * Get redirect URL for payment processing.
     *
     * Redirects to an intermediate page that creates the order
     * and then redirects to the upload page where customer can
     * upload their Yape/Plin payment receipt.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('yapeplin.process');
    }

    /**
     * Get additional details for frontend display.
     *
     * @return array
     */
    public function getAdditionalDetails()
    {
        return [
            'title'       => $this->getConfigData('title'),
            'description' => $this->getConfigData('description'),
            'instructions' => $this->getConfigData('instructions'),
            'account_number' => $this->getConfigData('account_number'),
            'account_holder' => $this->getConfigData('account_holder'),
        ];
    }

    /**
     * Get payment method configuration data.
     *
     * @param string $field
     * @return mixed
     */
    public function getConfigData($field)
    {
        return core()->getConfigData('sales.payment_methods.yapeplin.' . $field);
    }

    /**
     * Returns payment method image
     *
     * @return string
     */
    public function getImage()
    {
        return asset('yape-logo.png');
    }
}
