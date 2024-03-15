<?php declare(strict_types=1);

namespace Shopware\Storefront\Page;

use Shopware\Core\Checkout\Payment\PaymentMethodCollection;
use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Storefront\Pagelet\Footer\FooterPagelet;
use Shopware\Storefront\Pagelet\Header\HeaderPagelet;

#[Package('storefront')]
class Page extends Struct
{
    /**
     * @deprecated tag:v6.7.0 - #esi_rework#
     *
     * @var HeaderPagelet|null
     */
    protected $header;

    /**
     * @deprecated tag:v6.7.0 - #esi_rework#
     *
     * @var FooterPagelet|null
     */
    protected $footer;

    /**
     * @var ShippingMethodCollection
     */
    protected $salesChannelShippingMethods;

    /**
     * @var PaymentMethodCollection
     */
    protected $salesChannelPaymentMethods;

    /**
     * @var MetaInformation
     */
    protected $metaInformation;

    /**
     * @deprecated tag:v6.7.0 - #esi_rework#
     */
    public function getHeader(): ?HeaderPagelet
    {
        return $this->header;
    }

    /**
     * @deprecated tag:v6.7.0 - #esi_rework#
     */
    public function setHeader(?HeaderPagelet $header): void
    {
        $this->header = $header;
    }

    /**
     * @deprecated tag:v6.7.0 - #esi_rework#
     */
    public function getFooter(): ?FooterPagelet
    {
        return $this->footer;
    }

    /**
     * @deprecated tag:v6.7.0 - #esi_rework#
     */
    public function setFooter(?FooterPagelet $footer): void
    {
        $this->footer = $footer;
    }

    public function getSalesChannelShippingMethods(): ?ShippingMethodCollection
    {
        return $this->salesChannelShippingMethods;
    }

    public function setSalesChannelShippingMethods(ShippingMethodCollection $salesChannelShippingMethods): void
    {
        $this->salesChannelShippingMethods = $salesChannelShippingMethods;
    }

    public function getSalesChannelPaymentMethods(): ?PaymentMethodCollection
    {
        return $this->salesChannelPaymentMethods;
    }

    public function setSalesChannelPaymentMethods(PaymentMethodCollection $salesChannelPaymentMethods): void
    {
        $this->salesChannelPaymentMethods = $salesChannelPaymentMethods;
    }

    public function getMetaInformation(): ?MetaInformation
    {
        return $this->metaInformation;
    }

    public function setMetaInformation(MetaInformation $metaInformation): void
    {
        $this->metaInformation = $metaInformation;
    }
}
