<?php

namespace App\Controller;

use App\Entity\CategoryLikes;
use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryLikesController extends AbstractController
{
    #[Route('/categories/{id}/toggle-like', name: 'category_toggle_like', methods: ['POST'])]
    public function toggleLike(Categories $category, Request $request, EntityManagerInterface $em): Response
    {
        // Decode JSON body
        $data = json_decode($request->getContent(), true);
        $type = $data['type'] ?? null;
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'You must be logged in to like a category'], Response::HTTP_UNAUTHORIZED);
        }

        $existingLike = $category->getLikes()->filter(
            fn(CategoryLikes $like) => $like->getUserID() === $user
        )->first();

        // Log the received data for debugging
        error_log('Received data: ' . $request->getContent());

        // Validate the 'type' parameter
        if (!in_array($type, ['safe', 'dangerous'], true)) {
            return $this->json(
                [
                'error' => 'Invalid like type provided.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($existingLike) {
            if ($existingLike->getType() === $type) {
                $category->removeLike($existingLike);
                $em->remove($existingLike);
                $action = 'removed';
            } else {
                $existingLike->setType($type);
                $em->persist($existingLike);
                $action = 'updated';
            }
        } else {
            $like = new CategoryLikes();
            $like->setType($type);
            $like->setCategory($category);
            $like->setUserID($user);
            $category->addLike($like);
            $em->persist($like);
            $action = 'added';
        }

        $em->flush();

        // Update category dangerous score based on likes
        $safeLikes = $category->countSafeLikes();
        $dangerousLikes = $category->countDangerousLikes();
        $category->setDangerous($dangerousLikes - $safeLikes);
        $em->flush();

        return $this->json(
            [
                'message' => 'Like toggled successfully',
                'action' => $action,
                'safeLikes' => $category->countSafeLikes(),
                'dangerousLikes' => $category->countDangerousLikes(),
                'dangerousScore' => $category->getDangerous()
            ]
        );
    }
} 