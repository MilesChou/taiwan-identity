<?php

namespace Validentity;

class Taiwan implements ValidentityInterface
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

    private static $genderChars = [
        '1',
        '2',
        'A',
        'B',
        'C',
        'D',
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

    public function check($id)
    {
        $id = $this->normalize($id);

        // Check identity pattern
        if (!preg_match('/(^[A-Z][A-D1-2]\d{8})$/', $id)) {
            return false;
        }

        return $this->checkIdentity($id);
    }

    public function generate()
    {
        $locationChar = array_rand(self::$charMapping);
        $genderChar = self::$genderChars[array_rand(self::$genderChars)];

        $fakeId = $locationChar . $genderChar . mt_rand(1000000, 9999999);

        $fakeIdNumber = $this->transferIdentityToNumber($fakeId);

        $sum = $this->calculateSum($fakeIdNumber);

        $sub = $sum % 10;

        if (0 === $sub) {
            return $fakeId . '0';
        }

        return $fakeId . (string)(10 - $sub);
    }

    public function normalize($id)
    {
        if (!is_string($id)) {
            $type = gettype($id);
            throw new \InvalidArgumentException("Excepted string type, given is $type");
        }

        return strtoupper($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    private function checkIdentity($id)
    {
        $checksum = $this->getChecksum($id);

        $idNumber = $this->transferIdentityToNumber($id);

        $sum = $this->calculateSum($idNumber);

        return $this->checksum($sum, $checksum);
    }

    /**
     * @param string $id
     * @return int
     */
    private function calculateSum($id)
    {
        return $this->isLocal($id)
            ? $this->calculateSumForLocal($id)
            : $this->calculateSumForForeign($id);
    }

    /**
     * @param string $id
     * @return int
     */
    private function calculateSumForLocal($id)
    {
        $splitId = str_split($id);

        return array_sum(array_map(function ($split, $weight) {
            return $split * $weight;
        }, $splitId, array_keys($splitId)));
    }

    /**
     * @param string $id
     * @return int
     */
    private function calculateSumForForeign($id)
    {
        $splitId = str_split($id);

        return array_sum(array_map(function ($split, $index) {
            return ($split * self::$weights[$index]) % 10;
        }, $splitId, array_keys($splitId)));
    }

    /**
     * @param int $sum
     * @param int $checksum
     * @return bool
     */
    private function checksum($sum, $checksum)
    {
        $sub = $sum % 10;

        if (0 === $sub) {
            return $sub === $checksum;
        }

        return 10 - $sub === $checksum;
    }

    /**
     * @param string $id
     * @return int
     */
    private function getChecksum($id)
    {
        return (int)$id[strlen($id) - 1];
    }

    /**
     * @param string $id
     * @return bool
     */
    private function isLocal($id)
    {
        return in_array($id[1], ['1', '2'], true);
    }

    /**
     * @param string $id
     * @return string
     */
    private function transferIdentityToNumber($id)
    {
        return $this->isLocal($id)
            ? self::$charMapping[$id[0]] . mb_substr($id, 1, 8)
            : self::$charMapping[$id[0]] . self::$charMapping[$id[1]][1] . mb_substr($id, 2, 7);
    }
}
