<?php
$alphabet        = "0123456789abcdefghijklmnopqrstuvwxyz";
$allowed_symbols = "23456789abcdeghkmnpqsuvxyz";
$fontsdir        = RL_LIBS . 'fonts';
$length          = $rlConfig->getConfig('security_code_length');
if ($length > 10) {
    $length = 10;
}
$width                 = 110;
$height                = 55;
$fluctuation_amplitude = 10;
$no_spaces             = true;
$show_credits          = false;
$foreground_color      = array(
    mt_rand(50, 100),
    mt_rand(50, 100),
    mt_rand(50, 100)
);
$background_color      = array(
    mt_rand(180, 255),
    mt_rand(180, 255),
    mt_rand(180, 255)
);
$jpeg_quality          = 90;
?>