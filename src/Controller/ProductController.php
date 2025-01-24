<?php

// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FileUploader $fileUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
    ) {
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/products', name: 'product_list', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json($products);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/products', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setType($data['type']);
        $product->setQuantity($data['quantity']);

        if ($request->files->has('image')) {
            $imageFile = $request->files->get('image');
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            $imageFile->move($this->getParameter('upload_directory'), $newFilename);
            $product->setImagepath($newFilename);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/products/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(Request $request, Product $product): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        if (isset($data['type'])) {
            $product->setType($data['type']);
        }
        if (isset($data['quantity'])) {
            $product->setQuantity($data['quantity']);
        }

        $this->entityManager->flush();

        return $this->json($product);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(Product $product): JsonResponse
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/products', name: 'product_index')]
    public function webIndex(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/products/create', name: 'product_create_web')]
    public function webCreate(Request $request): Response
    {
        $product = new Product();
        $form = $this->createFormBuilder($product)
            ->add('name')
            ->add('description')
            ->add('type')
            ->add('quantity')
            ->add('imagepath', FileType::class, ['required' => false])
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fileUploader->uploadProductImage($form, $product);
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/products/{id}/edit', name: 'product_edit')]
    public function webEdit(Request $request, $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        $form = $this->createFormBuilder($product)
            ->add('name')
            ->add('description')
            ->add('type')
            ->add('quantity')
            ->add('imagepath', FileType::class, ['required' => false, 'data_class' => null])
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fileUploader->uploadProductImage($form, $product);
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
