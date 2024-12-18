<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidProducerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $eventBus,
        ClockInterface $clock,
        InvoiceRepositoryInterface $invoiceRepository,
    ): void {
        $this->beConstructedWith($eventBus, $clock, $invoiceRepository);
    }

    function it_dispatches_order_payment_paid_event_for_payment(
        MessageBusInterface $eventBus,
        ClockInterface $clock,
        PaymentInterface $payment,
        OrderInterface $order,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceInterface $invoice,
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('0000001');

        $dateTime = new \DateTimeImmutable();
        $clock->now()->willReturn($dateTime);

        $event = new OrderPaymentPaid('0000001', $dateTime);

        $invoiceRepository->findOneByOrder($order)->willReturn($invoice);

        $eventBus->dispatch($event)->shouldBeCalled()->willReturn(new Envelope($event));

        $this->__invoke($payment);
    }

    function it_does_not_dispatch_event_when_payment_is_not_related_to_order(
        MessageBusInterface $eventBus,
        ClockInterface $clock,
        PaymentInterface $payment,
    ): void {
        $payment->getOrder()->willReturn(null);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $clock->now()->shouldNotBeCalled();

        $this->__invoke($payment);
    }

    function it_does_not_dispatch_event_when_there_is_no_invoice_related_to_order(
        MessageBusInterface $eventBus,
        ClockInterface $clock,
        PaymentInterface $payment,
        OrderInterface $order,
        InvoiceRepositoryInterface $invoiceRepository,
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('0000001');
        $invoiceRepository->findOneByOrder($order)->willReturn(null);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();
        $clock->now()->shouldNotBeCalled();

        $this->__invoke($payment);
    }
}
