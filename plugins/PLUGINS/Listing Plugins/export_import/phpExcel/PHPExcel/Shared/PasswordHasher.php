<?php
class PHPExcel_Shared_PasswordHasher
{
    public static function hashPassword($pPassword = '')
    {
        $password = 0x0000;
        $charPos  = 1;
        $chars    = preg_split('//', $pPassword, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            $value        = ord($char) << $charPos++;
            $rotated_bits = $value >> 15;
            $value &= 0x7fff;
            $password ^= ($value | $rotated_bits);
        }
        $password ^= strlen($pPassword);
        $password ^= 0xCE4B;
        return (strtoupper(dechex($password)));
    }
}