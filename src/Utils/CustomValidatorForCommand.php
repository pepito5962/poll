<?php

namespace App\Utils;

use Symfony\Component\Console\Exception\InvalidArgumentException;

class CustomValidatorForCommand {

    /**
     * Validate an email entered by the user in CLI
     *
     * @param string|null $emailEntered
     * @return string
     */
    public function validateEmail(?string $emailEntered): string
    {
        if(empty($emailEntered)){
            throw new InvalidArgumentException("VEUILLEZ SAISIR UN EMAIL");
        }

        if(!filter_var($emailEntered, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException("EMAIL SAISI INVALIDE");
        }

        return $emailEntered;
    }

    /**
     * Validate a password entered by the user in CLI
     *
     * @param string|null $plainPassword
     * @return string
     */
    public function validatePassword(?string $plainPassword): string
    {
        if(empty($plainPassword)){
            throw new InvalidArgumentException("VEUILLEZ SAISIR UN MOT DE PASSE");
        }

        $passwordRegex = '/^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ý0-9]).{12,}$/';

        if(!preg_match($passwordRegex, $plainPassword)){
            throw new InvalidArgumentException("LE PASSWORD DOIT CONTENIR 12 CARACTERE DONT UNE LETTRE MINUSCULE, UNE LETTRE MAJUSCULE, UN CHIFFRE ET UN CARACTÈRE SPÉCIALE");
        }

        return $plainPassword;
    }

    public function validateName(?string $name): string
    {

        if(empty($name)){
            throw new InvalidArgumentException("VEUILLEZ SAISIR UN NOM OU UN PRENOM");
        }

        if(!ctype_alpha($name)){
            throw new InvalidArgumentException("VOTRE NOM OU PRENOM DOIT CONTENIR SEULEMENT DES LETTRES");
        }

        return $name;
    }

}