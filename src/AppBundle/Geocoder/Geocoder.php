<?php

namespace AppBundle\Geocoder;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;

class Geocoder
{
    protected $em;

    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        $this->em               = $entityManager;
        $this->container        = $container;
        $this->request          = $container->get('request_stack')->getCurrentRequest();
    }

    /**
     * Extract coordinate to address
     *
     * @param $address
     * @return array|null
     */
    public function getCoordinateFromAddress($address){

        /**
         * To prevent request for same address if user refresh this page
         * we pull address and coordinate into session
         */
        if($this->request->getSession()->has('address')
            && $this->request->getSession()->has('coordinate')
            && $this->request->getSession()->get('address') == $address
            && !is_null($this->request->getSession()->get('coordinate'))){

            $coordinate   = $this->request->getSession()->get('coordinate');

        }else{
            $request    = new GeocoderAddressRequest($address);
            $response   = $this->container->get('ivory.google_map.geocoder')->geocode($request);

            if(!empty($address) && $response->getStatus() != 'ZERO_RESULTS'){
                $latitude   = $response->getResults()[0]->getGeometry()->getLocation()->getLatitude();
                $longitude  = $response->getResults()[0]->getGeometry()->getLocation()->getLongitude();
                $coordinate = array($latitude, $longitude);

                $this->request->getSession()->set('coordinate', $coordinate);
                $this->request->getSession()->set('address', $address);

                return $coordinate;
            }
        }

        return !empty($coordinate) ? $coordinate : null;
    }

    /**
     * Return distance in kilometers between two coordinates
     *
     * @param $CoordinatesFrom
     * @param $CoordinatesTo
     * @return float
     */
    public function distanceBetweenCoordinates($CoordinatesFrom, $CoordinatesTo){
        $distance = 111.13384 * (rad2deg(acos((sin(deg2rad($CoordinatesFrom[0]))*sin(deg2rad($CoordinatesTo[0]))) + (cos(deg2rad($CoordinatesFrom[0]))*cos(deg2rad($CoordinatesTo[0]))*cos(deg2rad($CoordinatesFrom[1] - $CoordinatesTo[1]))))));
        return round($distance, 1);
    }


}

