<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/posts")
 */
class PostController
{
    /**
     * @Route(name="api_posts_collection_get", methods={"GET"})
     * @param PostRepository $postRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function collection(PostRepository $postRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($postRepository->findAll(), "json", ["groups" => "get"]),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @Route("/{id}", name="api_posts_item_get", methods={"GET"})
     * @param Post $post
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function item(Post $post, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($post, "json", ["groups" => "get"]),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @Route(name="api_posts_collection_post", methods={"POST"})
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function post(
        Post $post,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        $post->setAuthor($entityManager->getRepository(User::class)->findOneBy([]));

        $errors = $validator->validate($post);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($post);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($post, "json", ["groups" => "get"]),
            JsonResponse::HTTP_CREATED,
            ["Location" => $urlGenerator->generate("api_posts_item_get", ["id" => $post->getId()])],
            true
        );
    }

    /**
     * @Route("/{id}", name="api_posts_item_put", methods={"PUT"})
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function put(
        Post $post,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $errors = $validator->validate($post);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{id}", name="api_posts_item_delete", methods={"DELETE"})
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function delete(
        Post $post,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $entityManager->remove($post);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
