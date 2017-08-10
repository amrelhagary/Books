<?php
namespace BookBundle\Util;

class Helper
{
    public static function formatErrorMessage($errors) :string
    {
        $msg = "";
        foreach ($errors as $err){
            $msg  .= $err->getMessage();
        }
        return $msg;
    }
}