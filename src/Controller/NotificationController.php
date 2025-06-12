<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'app_notifications', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findUnreadByUser($this->getUser());
        
        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/count', name: 'app_notifications_count', methods: ['GET'])]
    public function count(NotificationRepository $notificationRepository): JsonResponse
    {
        $count = $notificationRepository->countUnreadByUser($this->getUser());
        
        return $this->json([
            'count' => $count
        ]);
    }

    #[Route('/mark-as-read/{id}', name: 'app_notifications_mark_read', methods: ['POST'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot mark this notification as read');
        }

        $notification->setIsRead(true);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/mark-all-as-read', name: 'app_notifications_mark_all_read', methods: ['POST'])]
    public function markAllAsRead(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $notifications = $notificationRepository->findUnreadByUser($this->getUser());
        
        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }
        
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
} 