<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Config;

class ConfigExtension extends AbstractExtension
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_config', [$this, 'app_config']),
        ];
    }

    function app_config($route)
    {
        $logoConfig = $this->em->getRepository(Config::class)->findAll();
        $result= [];
        foreach ($logoConfig as $cf) {
            $result[$cf->getTheKey()] = $cf->getTheValue();
        }
        
        return $result[$route];
    }

}