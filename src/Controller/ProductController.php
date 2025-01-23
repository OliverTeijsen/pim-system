<?php

// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $entityManager;

    private FileUploader $fileUploader;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
    }

    #[Route('/products', name: 'product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // In ProductController.php

    #[Route('/products/create', name: 'product_create')]
    public function create(Request $request): Response
    {
        $product = new Product();

        // Create the form
        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('type', TextType::class)
            ->add('quantity', NumberType::class)
            ->add('imagepath', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Product'])
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

    #[Route('/products/edit/{id}', name: 'product_edit')]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('type', TextType::class)
            ->add('quantity', NumberType::class)
            ->add('imagepath', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Update Product'])
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

    #[Route('/products/delete/{id}', name: 'product_delete')]
    public function delete(Product $product): Response
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->redirectToRoute('product_index');
    }
}
