<?php
namespace App\Service;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\DriverException;
use App\Service\Exception\AdServiceException;

class AdService
{
    private $adRepository;
    private $adManager;

    public function __construct(AdRepository $adRepository,
                                EntityManagerInterface $adManager) 
    {
        $this->adRepository = $adRepository;
        $this->adManager = $adManager;
    }

    public function searchAll()
    {
        try {
            $ads = $this->adRepository->findAll();
                 
            return $ads;
        } 
        catch (DriverException $e) {
            throw new AdServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function persist(?Ad $ad){
        try {
            $this->adManager->persist($ad);
            $this->adManager->flush();
        }
        catch (DriverException $e) {
            throw new AdServiceException("Un problème technique est survenu", $e->getCode());
        }
    }

    public function searchById(int $id)
    {
        try {
            $ad = $this->adRepository->find($id);
            return $ad;
        } catch(DriverException $e){
            throw new AdServiceException("Un problème est technique est servenu. Veuilllez réessayer ultérieurement.", $e->getCode());
        }
    }

    public function delete($id)
    {
        try {
            $ad = $this->adRepository->find($id);
            $this->adManager->remove($ad);
            $this->adManager->flush();
        } 
        catch (DriverException $e) {
            throw new AdServiceException("Un problème technique est survenu", $e->getCode());
        }
    }
}