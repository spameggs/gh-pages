<?php
class rlSMSCoin extends reefless
{
    function rlSMSCoin()
    {
    }
    function generate($number = 8)
    {
        $laters = range('a', 'z');
        $laters = array_merge($laters, range('A', 'Z'));
        for ($i = 0; $i < $number; $i++) {
            $step = rand(1, 2);
            if ($step == 1) {
                $out .= rand(0, 9);
            } elseif ($step == 2) {
                $index = rand(0, count($laters) - 1);
                $out .= $laters[$index];
            }
        }
        return $out;
    }
    function writeLog($line = false)
    {
        if (!empty($line)) {
            $file = fopen(RL_PLUGINS_URL . 'smsCoin/' . 'error.log', 'a');
            if ($file) {
                $line = $line . "\n";
                fwrite($file, $line);
                fclose($file);
            }
        }
    }
    function generateNumber($number = 8)
    {
        $arr = array(
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            0
        );
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $rnumber .= $arr[$index];
        }
        return $rnumber;
    }
}