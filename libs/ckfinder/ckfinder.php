<?php
if ( version_compare( phpversion(), '5', '<' ) )
	require_once 'core/ckfinder_php4.php' ;
else
	require_once 'core/ckfinder_php5.php' ;
