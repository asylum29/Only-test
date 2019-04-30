<?php

namespace App\Twig;

use App\Entity\Event;
use App\Entity\IpAddress;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EventRatingStateExtension extends AbstractExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('eventRatingState', array($this, 'eventRatingState')),
        );
    }

    public function eventRatingState(Event $event)
    {
        $result = 0;

        $clientIp = $this->requestStack->getCurrentRequest()->getClientIp();
        /** @var IpAddress $ipAddress */
        $ipAddress = $event
            ->getIpAddresses()
            ->filter(function(IpAddress $ipAddress) use ($clientIp) {
                return $ipAddress->getAddress() == $clientIp;
            })
            ->first()
        ;
        if (null != $ipAddress) {
            $result = $ipAddress->getUp() ? 1 : -1;
        }

        return $result;
    }
}
