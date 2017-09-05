<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AddressCheck extends Constraint
{
    public $message                 = "address.wrong";
    public $messageAlreadyExist     = "address.exist";

    public function validatedBy()
    {
        return 'app.address.check';
    }

}