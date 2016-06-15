<?php
$url = SEO_BASE;
$url .= $config['mod_rewrite'] ? $pages['paypal_pro'] . '.html' : '?page=' . $pages['paypal_pro'];
echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=' . $url . '">';
exit;