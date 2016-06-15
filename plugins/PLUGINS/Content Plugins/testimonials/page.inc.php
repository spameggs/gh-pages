<?php
$reefless->loadClass('Testimonials', null, 'testimonials');
$rlXajax->registerFunction(array(
    'addTestimonial',
    $GLOBALS['rlTestimonials'],
    'ajaxAdd'
));
if (!$_REQUEST['xjxfun']) {
    $rlTestimonials->get();
}