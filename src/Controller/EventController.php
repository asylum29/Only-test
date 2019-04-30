<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    const EVENTS_PER_PAGE = 8;

    /**
     * @Route("/", name="event_index", methods={"GET"})
     */
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $page = $request->get('page', 1);

        $query = $eventRepository
            ->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery();

        $adapter = new DoctrineORMAdapter($query);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(self::EVENTS_PER_PAGE);

        $page = $page > $pager->getNbPages() ? $pager->getNbPages() : $page;

        $pager->setCurrentPage($page);
        $query
            ->setFirstResult(($page - 1) * self::EVENTS_PER_PAGE)
            ->setMaxResults(self::EVENTS_PER_PAGE);

        $returnUrl = $this->generateUrl('event_index', ['page' => $page]);

        return $this->render('event/index.html.twig', [
            'events' => $query->getResult(),
            'pager'  => $pager,
            'return' => $returnUrl,
        ]);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Request $request, Event $event): Response
    {
        $returnUrl = $request->get('return', $this->generateUrl('event_index'));

        return $this->render('event/show.html.twig', [
            'event'  => $event,
            'return' => $returnUrl,
        ]);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
        $returnUrl = $request->get('return', $this->generateUrl('event_index'));

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreatedAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirect($returnUrl);
        }

        return $this->render('event/new.html.twig', [
            'event'  => $event,
            'form'   => $form->createView(),
            'return' => $returnUrl,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Event $event): Response
    {
        $returnUrl = $request->get('return', $this->generateUrl('event_index'));

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($returnUrl);;
        }

        return $this->render('event/edit.html.twig', [
            'event'  => $event,
            'form'   => $form->createView(),
            'return' => $returnUrl,
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Event $event): Response
    {
        $returnUrl = $request->get('return', $this->generateUrl('event_index'));

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirect($returnUrl);
    }
}
