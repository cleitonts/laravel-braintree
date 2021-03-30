<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

/**
 * Trait Utils
 * @package App\Http\Controllers
 */
trait Utils
{
    /**
     * validate an array and return error
     * @param array $arr
     * @return false|string
     */
    private static function fastValidade(array $arr, $data)
    {
        $validator = Validator::make($data, $arr);

        if ($validator->fails()) {
            return json_encode([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        return false;
    }
}
