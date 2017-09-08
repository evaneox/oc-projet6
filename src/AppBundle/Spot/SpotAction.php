<?php

namespace AppBundle\Spot;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Spot;
use AppBundle\Entity\User;
use AppBundle\Entity\Contribution;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;
use Cocur\Slugify\Slugify;


class SpotAction
{
    protected   $em;
    private     $maxDistance;
    private     $maxEditionTime;
    private     $maxPicturesBeforeDelete;
    private     $images;
    private     $user;

    public function __construct(EntityManager $entityManager, ContainerInterface $container, $maxDistance, $maxEditionTime, $maxPicturesBeforeDelete)
    {
        $this->em                           = $entityManager;
        $this->container                    = $container;
        $this->maxDistance                  = (int) $maxDistance;
        $this->maxEditionTime               = (int) $maxEditionTime;
        $this->maxPicturesBeforeDelete      = (int) $maxPicturesBeforeDelete;
        $this->user                         = $container->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * Creating a new spot
     *
     * @param Spot $spot
     * @return mixed
     */
    public function create(Spot $spot)
    {
        // We should extract coordinate to this address
        $coordinate = $this->container->get('app.geocoder')->getCoordinateFromAddress($spot->getAddress());

        // Complete Spot entity with missing informations
        $spot->setLatitude($coordinate[0]);
        $spot->setLongitude($coordinate[1]);
        $spot->setAuthor($this->user);

        $slugify = new Slugify();
        $spot->setSlug($slugify->slugify($spot->getAddress()));

        // Saving Spot entity in BDD
        $this->em->persist($spot);
        $this->em->flush();

        $this->addAContribution($spot);

        return $spot;
    }

    /**
     * Updating spot
     *
     * @param Spot $spot
     */
    public function update(Spot $spot){

        if(!is_null($spot)){

            $this->images = $spot->getImages();

            // Register newest pictures
            foreach($this->images as $image) {
                $image->setSpot($spot);
                $this->em->persist($image);
            }

            $this->preUpdate($spot);

            // Update Spot entity in BDD
            $this->em->persist($spot);
            $this->em->flush();

            if(sizeof($this->images) > 0){
                $this->addAContribution($spot);
            }

            // Delete oldest pictures
            if(sizeof($this->images) > 0){
                $this->autoPurgePicture($spot);
            }

        }
    }

    /**
     * Get spots results
     *
     * @param $address
     * @return null
     */
    public function getResults($address){

        $spotsResult = array();

        // We should extract coordinate to this address
        $coordinate = $this->container->get('app.geocoder')->getCoordinateFromAddress($address);

        if(is_null($coordinate)){
            return null;

        }else{

            // Get all spots
            $spots = $this->em->getRepository('AppBundle:Spot')->findAll();

            // For each spot we calculate distance between searching address and spot address
            foreach($spots as $spot){

                $distanceBetweenCoordinate = $this->container->get('app.geocoder')->distanceBetweenCoordinates(
                    $coordinate,
                    [$spot->getLatitude(), $spot->getLongitude()]
                );

                // We push this spot to result only if distance in not too far
                if($distanceBetweenCoordinate <= $this->maxDistance ){
                    $spot->setDistance($distanceBetweenCoordinate);
                    array_push($spotsResult, $spot);
                }
            }

            // The result is sorted by distance from the nearest to the most distant
            usort($spotsResult, function ($spotA, $spotB) {
                return $spotA->getDistance() > $spotB->getDistance();
            });

            return $spotsResult;
        }

    }

    /**
     * Giving wrinting access for current user to prevent multiple editing in same time
     *
     * @param Spot $spot
     */
    public function updateCurrentEditor(Spot $spot)
    {

        // We register new edition only if spot is not lock
        if($this->isWritable($spot)){
            $spot->setlastEditor($this->user);
            $spot->setLastEditorStartTime();

            // Update Spot entity in BDD
            $this->em->persist($spot);
            $this->em->flush();
        }
    }

    /**
     * Check if users can edit this spot
     *
     * @param Spot $spot
     * @return bool
     */
    public function isWritable(Spot $spot){

        $currentUserId = $this->container->get('security.token_storage')->getToken()->getUser();

        if($currentUserId !== $spot->getLastEditor()){

            if($spot->getTimeElapseSinceLastEdition() >= $this->maxEditionTime){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * Remove oldest pictures after new uploading
     *
     * @param $spot
     */
    public function autoPurgePicture($spot)
    {
        if (!is_null($spot)) {

            while ($this->em->getRepository('AppBundle:Image')->getCountImageFomSpot($spot->getId()) > $this->maxPicturesBeforeDelete) {
                $olderImage = $this->em->getRepository('AppBundle:Image')->getOlderPictureFromSpot($spot->getId());
                if (!is_null($olderImage)) {
                    $this->em->remove($olderImage);
                    $this->em->flush();
                }
            }

        }
    }

    /**
     * registering new contribution to spot
     *
     * @param $spot
     */
    public function addAContribution($spot){

        if(!is_null($spot)){
            $contribution = new Contribution();
            $contribution->setSpot($spot);
            $contribution->setContributor($this->user);
            $this->em->persist($contribution);
            $this->em->flush();
        }
    }

    /**
     * Called before update spot entity
     *
     * @param $spot
     */
    public function preUpdate($spot){

        // Check if user as changed spot informations
        $uow = $this->em->getUnitOfWork();
        $uow->computeChangeSets();

        if ($uow->isEntityScheduled($spot)) {
            $this->addAContribution($spot);
        }
    }

    /**
     * Delete a spot
     *
     * @param $id
     */
    public function delete($id)
    {
        if(!empty($id)){
            $spot = $this->em->getRepository('AppBundle:Spot')->findOneBy(array('id' => $id));

            // Be sure this spot exist before remove it
            if(!is_null($spot)){
                $this->em->remove($spot);
                $this->em->flush();
            }
        }
    }


}
