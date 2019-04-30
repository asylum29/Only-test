<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\IpAddress;
use App\Repository\IpAddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/rating")
 */
class RatingApiController extends AbstractController
{
    /**
     * @Route("/{id}", name="rating_change", methods={"POST"})
     */
    public function change(Request $request, Event $event, IpAddressRepository $ipAddressRepository): Response
    {
        $state = $request->get('state', 0);

        $clientIp = $request->getClientIp();
        $entityManager = $this->getDoctrine()->getManager();

        $ipAddress = $ipAddressRepository->findOneBy(['event' => $event, 'address' => $clientIp]);
        if (null != $ipAddress) {
            $entityManager->remove($ipAddress);
            $entityManager->flush();
        }

        if (!empty($state)) {
            $ipAddress = new IpAddress();
            $ipAddress->setUp($state > 0);
            $ipAddress->setAddress($clientIp);
            $ipAddress->setEvent($event);
            $entityManager->persist($ipAddress);
            $entityManager->flush();
        }

        return $this->json(['rating' => $event->getRating()]);
    }
}
