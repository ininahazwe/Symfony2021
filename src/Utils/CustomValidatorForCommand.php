<?php

namespace App\Utils;

use Symfony\Component\Console\Exception\InvalidArgumentException;

class CustomValidatorForCommand
{

    /**
     * Validates an email entered by the user in the CLI.
     *
     * @param string|null $emailEntered
     * @return string
     */
    public function validateEmail(?string $emailEntered): string
    {
        if(empty($emailEntered)){
            throw new InvalidArgumentException('VEUILLEZ SAISIR UN EMAIL.');
        }
        if(!filter_var($emailEntered, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('EMAIL SAISI INVALIDE');
        }
        [, $domain] = explode('@', $emailEntered);

        if(!checkdnsrr($domain)){
            throw new InvalidArgumentException('EMAIL SAISI INVALID');
        }
        return $emailEntered;
    }

    /**
     * Validates a password entered by the user in the CLI.
     *
     * @param string|null $plainPassword
     * @return string
     */
    public function validatePassword(?string $plainPassword): string
    {
        if(empty($plainPassword))
        {
            throw new InvalidArgumentException('VEUILLEZ SAISIR UN MOT DE PASSE.');
        }
        $passwordRegex = '/^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ý0-9]).{12,}$/';

        if(!preg_match($passwordRegex, $plainPassword)){
            throw new InvalidArgumentException('LE PASSWORD DOIT CONTENIR 12 CARACTÈRES AU MINIMUM DANS UN ORDRE ALÉATOIRE DONT: 1 LETTRE MINUSCULE, 1 LETTRE MAJUSCULE, 1 CHIFFRE, ET 1 CARACTÈRE SPECIAL.');
        }
        return $plainPassword;
    }

    public function checkEmailForUserDelete(?string $emailEntered): string
    {
        if(empty($emailEntered)){
            throw new InvalidArgumentException('VEUILLEZ SAISIR UN EMAIL.');
        }

        if(!filter_var($emailEntered, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('EMAIL SAISI INVALID.');
        }

        return $emailEntered;
    }
}