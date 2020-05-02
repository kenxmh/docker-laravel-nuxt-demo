<?php
namespace App\Services;

use Vinkla\Hashids\Facades\Hashids;

class HashService {

    public static function getObjectId($uuid)
    {
        $decodedArray = Hashids::decode($uuid);
        return $decodedArray[1] ?? null;
    }
}