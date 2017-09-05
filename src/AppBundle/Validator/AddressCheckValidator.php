<?php

namespace AppBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;


class AddressCheckValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em           = $em;
        $this->container    = $container;
        $this->request      = $container->get('request_stack')->getCurrentRequest();
    }

    public function validate($address, Constraint $constraint)
    {
        /**
         * Only check for creating spot
         */
        if($this->request->get('_route') == 'spot.create'){
            //We check if address is valid
            $requete = $add = new GeocoderAddressRequest($address);
            $response = $this->container->get('ivory.google_map.geocoder')->geocode($add);

            if(empty($address) || $response->getStatus() == 'ZERO_RESULTS'){
                $this->context->buildViolation($constraint->message)->addViolation();
            }

            //We check if another spot have same address
            if($this->em->getRepository('AppBundle:Spot')->findByAddress($address) != null){
                $this->context->buildViolation($constraint->messageAlreadyExist)->addViolation();
            }
        }

    }
}
