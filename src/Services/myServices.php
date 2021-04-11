<?php

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class myServices extends AbstractController
{

    /**
     * Check complexity password
     *
     * @param string $password password to verifie
     *
     * @return bool
     */

    public function passwordIsComplex ( string $password ) : bool
    {
        // retrieving values from the configuration file
        $digitPattern = $this->getParameter('digitPattern');
        $lowercasePattern = $this->getParameter('lowercasePattern');
        $uppercasePattern = $this->getParameter('uppercasePattern');
        $specialCharPattern = $this->getParameter('specialCharPattern');
        $minimumPasswordLength = $this->getParameter('minimumPasswordLength');

        return preg_match( $digitPattern , $password ) && /// digit check
            preg_match( $lowercasePattern , $password ) && // lowercase check
            preg_match( $uppercasePattern , $password ) && // uppercase check
            preg_match( $specialCharPattern, $password ) && // special characters check
            strlen( $password ) >= $minimumPasswordLength; // length check
    }
}