<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

final class InvoiceFactory implements InvoiceFactoryInterface
{
    /**
     * @var string
     * @psalm-var class-string
     */
    private $className;

    /**
     * @psalm-param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createForData(
        string $id,
        string $number,
        string $orderNumber,
        \DateTimeInterface $issuedAt,
        BillingDataInterface $billingData,
        string $currencyCode,
        string $localeCode,
        int $total,
        Collection $lineItems,
        Collection $taxItems,
        ChannelInterface $channel,
        InvoiceShopBillingDataInterface $shopBillingData = null
    ): InvoiceInterface {
        return new $this->className(
            $id,
            $number,
            $orderNumber,
            $issuedAt,
            $billingData,
            $currencyCode,
            $localeCode,
            $total,
            $lineItems,
            $taxItems,
            $channel,
            $shopBillingData ?? new InvoiceShopBillingData()
        );
    }
}
