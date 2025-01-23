<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private string $targetDirectory;

    public function __construct(string $targetDirectory = 'uploads/images')
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function uploadProductImage(FormInterface $form, Product $product): void
    {
        $imageFile = $form->get('imagepath')->getData();
        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            try {
                $imageFile->move($this->targetDirectory, $newFilename);
                $product->setImagepath($newFilename);
            } catch (FileException $e) {
                // error handling
            }
        }
    }
}