<?php

namespace Validentity\Concerns\Validators;

trait TaiwanValidatorCommon
{
    /**
     * @var array
     */
    private static $charMapping = [
        'A' => '10',
        'B' => '11',
        'C' => '12',
        'D' => '13',
        'E' => '14',
        'F' => '15',
        'G' => '16',
        'H' => '17',
        'I' => '34',
        'J' => '18',
        'K' => '19',
        'L' => '20',
        'M' => '21',
        'N' => '22',
        'O' => '35',
        'P' => '23',
        'Q' => '24',
        'R' => '25',
        'S' => '26',
        'T' => '27',
        'U' => '28',
        'V' => '29',
        'W' => '32',
        'X' => '30',
        'Y' => '31',
        'Z' => '33',
    ];

    /**
     * @var array
     */
    private static $weights = [
        1,
        9,
        8,
        7,
        6,
        5,
        4,
        3,
        2,
        1,
        1,
    ];

    /**
     * @param int $sum
     * @return int
     */
    private function generateChecksum($sum)
    {
        $sub = $sum % 10;

        if (0 === $sub) {
            return 0;
        }

        return 10 - $sub;
    }

    /**
     * Checksum is the last numeric char
     *
     * @param string $id
     * @return int
     */
    private function getChecksum($id)
    {
        return (int)$id[mb_strlen($id) - 1];
    }
}
