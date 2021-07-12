<?php
namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\DriverException;
use App\Service\Exception\ImageServiceException;

class ImageService
{
    private $imageRepository;
    private $imageManager;

    public function __construct(ImageRepository $imageRepository,
                                EntityManagerInterface $imageManager) 
    {
        $this->imageRepository = $imageRepository;
        $this->imageManager = $imageManager;
    }

    public function searchAll()
    {
        try {
            $images = $this->imageRepository->findAll();
                 
            return $images;
        } 
        catch (DriverException $e) {
            throw new ImageServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function delete(Image $id)
    {
        try {
            $image = $this->imageRepository->find($id);
            $this->imageManager->remove($image);
            $this->imageManager->flush();
        } 
        catch (DriverException $e) {
            throw new ImageServiceException("L'image n'a pas pu être supprimée", $e->getCode());
        }
    }

    public function persist(?Image $image){
        try {
            $this->imageManager->persist($image);
            $this->imageManager->flush();
        }
        catch (DriverException $e) {
            throw new ImageServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function searchById(int $id)
    {
        try {
            $image = $this->imageRepository->find($id);
            return $image;
        } catch(DriverException $e){
            throw new ImageServiceException("Un problème est technique est servenu. Veuilllez réessayer ultérieurement.", $e->getCode());
        }
    }
}