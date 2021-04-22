<?php

namespace App\Utils;

use Symfony\Component\Console\Exception\RuntimeException;

trait DateTimeImmutableTrait
{
    /**
     * Generate a random DateTimeImmutable object and related date string between a start and an end date.
     *
     * @param string $start Date string with format 'd/m/Y'
     * @param string $end Date string with format 'd/m/Y'
     * @return array {dateObject: \DateTimeImmutable, dateString: string} String with "d-m-Y"
     */

    private function generateRandomDateBetweenRange(string $start, string $end): array
    {
        $startDate = \DateTime::createFromFormat('d/m/Y', $start);
        $endDate = \DateTime::createFromFormat('d/m/Y', $end);

        if(!$startDate || !$endDate) {
            throw new RuntimeException( "La date saisie doit être sous le format 'd/m/Y' pour les deux dates.");
        }

        $randomTimestamp = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());
        $dateTimeImmutable = (new \DateTimeImmutable())->setTimestamp($randomTimestamp);

        return [
            'dateObject' => $dateTimeImmutable,
            'dateString' => $dateTimeImmutable->format('d-m-Y')
        ];
    }
}