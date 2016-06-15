<?php
class rlListingsCarousel extends reefless
{
    var $rlLang;
    var $rlValid;
    function rlListingsCarousel()
    {
        global $rlLang, $rlValid;
        $this->rlLang =& $rlLang;
        $this->rlValid =& $rlValid;
    }
    function updateCarouselBlock()
    {
        global $rlDb;
        $box     = $rlDb->getAll("SELECT * FROM `" . RL_DBPREFIX . "listings_carousel` WHERE `Status` = 'active' ");
        $content = 'global $rlSmarty, $carousel_options;';
        $content .= 'if ( !$_REQUEST["xjxfun"] ){unset($_SESSION["carousel"]);}';
        if ($box) {
            $content .= '$carousel_options = array(';
            foreach ($box as $key => $item) {
                $block_ids = explode(',', $item['Block_IDs']);
                foreach ($block_ids as $keyId => $itemId) {
                    if ($itemId) {
                        $content .= (int) $itemId . '=> array( ';
                        $content .= '"Direction" => "' . $item['Direction'] . '",';
                        $content .= '"Number" => "' . $item['Number'] . '",';
                        $content .= '"Delay" => "' . $item['Delay'] . '",';
                        $content .= '"Per_slide" => "' . $item['Per_slide'] . '",';
                        $content .= '"Visible" => "' . $item['Visible'] . '",';
                        $content .= '"Round" => "' . $item['Round'] . '"';
                        $content .= '),';
                    }
                }
            }
            $content = substr($content, 0, -1);
            $content .= ');';
        }
        $content .= '$rlSmarty -> assign("carousel_options", $carousel_options);';
        if ($rlDb->query("UPDATE  `" . RL_DBPREFIX . "hooks` SET `Code` = '{$content}' WHERE `Name` = 'init' AND `Plugin` = 'listings_carousel'  LIMIT 1 ;")) {
            return true;
        }
    }
    function changeContentBlock($contents = false)
    {
        global $rlSmarty, $rlListings, $carousel_options, $blocks;
        if (!$_REQUEST['xjxfun']) {
            $_SESSION['carousel']['all_ids'] = $rlListings->selectedIDs;
        }
        foreach ($blocks as $sKey => $sVal) {
            foreach ($contents as $key => $val) {
                if ($sVal['ID'] == $key) {
                    if ($blocks[$sKey]['Type'] != 'smarty') {
                        $option = $carousel_options[$blocks[$sKey]['ID']];
                        preg_match('/getListings\(\s"([\w,]*)",\s"(\w*)",\s"([0-9]*)",\s"([0-9]*?)"\s\);/', $sVal['Content'], $matches);
                        $content_block            = 'global $rlSmarty, $reefless;
							$reefless -> loadClass("ListingsBox", null, "listings_box");
							global $rlListingsBox;
							$listings_box = $rlListingsBox -> getListings( "' . $matches[1] . '", "' . $matches[2] . '", ' . $option['Visible'] . ', "' . $matches[4] . '" );
							foreach($listings_box as $key => $val)
							{
								$ids[] = $val["ID"];
								$_SESSION["carousel"]["all_ids"][] = $val["ID"];
							}
							$_SESSION["carousel"]["' . $sVal['Key'] . '"] = $ids;
							$rlSmarty -> assign_by_ref( "listings", $listings_box );
							$rlSmarty -> assign( "type", "listings" );
							$rlSmarty -> display( RL_PLUGINS . "listings_carousel" . RL_DS . "carousel.block.tpl" );';
                        $blocks[$sKey]['Content'] = $content_block;
                        $blocks[$sKey]['options'] = "listing_box|" . $blocks[$sKey]['Key'] . "|" . $matches[1] . "|" . $matches[2] . "|" . $matches[4];
                    } else {
                        preg_match("/listings=(.*)\s+type='(\w+)'(\s+field='(\w+)')?(\s+value='(\w+)')?/", $sVal['Content'], $matches);
                        $blocks[$sKey]['Content'] = '{include file=$smarty.const.RL_PLUGINS|cat:"listings_carousel"|cat:$smarty.const.RL_DS|cat:"carousel.block.tpl" listings=' . $matches[1] . ' type="' . $matches[2] . '"}';
                        $blocks[$sKey]['options'] = "featured|" . $blocks[$sKey]['Key'] . "|" . $matches[2] . "|" . $matches[4] . "|" . $matches[6];
                    }
                }
            }
        }
        $rlSmarty->assign_by_ref("blocks", $blocks);
    }
    function ajaxLoadListings($id = false, $limit = 1, $options = false, $number = false, $priceTag = false)
    {
        global $_response, $rlListings, $rlListingTypes, $rlSmarty, $reefless, $config, $pages, $lang;
        if (!$id) {
            return $_response;
        }
        $options = explode('|', $options);
        if ($number - $limit < 0) {
            $limit = $number;
        }
        $listing_types = $rlListingTypes->types;
        if ($options[0] == 'featured') {
            $rlListings->selectedIDs = $_SESSION['carousel']['all_ids'];
            $listings                = $rlListings->getFeatured($options[2], $limit, $options[3], $options[4]);
        } else {
            $reefless->loadClass('ListingsBox', null, 'listings_box');
            if ($options[4]) {
                $rlListings->selectedIDs = $_SESSION['carousel']['all_ids'];
            } else {
                $rlListings->selectedIDs = $_SESSION['carousel'][$options[1]];
            }
            $listings = $GLOBALS["rlListingsBox"]->getListings($options[2], $options[3], $limit, '1');
        }
        if ($listings) {
            foreach ($listings as $key => $listing) {
                if ($options[0] != 'featured') {
                    $_SESSION['carousel'][$options[1]][] = $listing['ID'];
                }
                if (!in_array($listing['ID'], $_SESSION['carousel']['all_ids'])) {
                    $_SESSION['carousel']['all_ids'][] = $listing['ID'];
                }
                $listing_type = $listing_types[$listing['Listing_type']];
                $li           = '<li class="item"><div class="content">';
                if ($listing_type['Photo']) {
                    $width = ' style="width: ' . $config['pg_upload_thumbnail_width'] . 'px;"';
                    if ($listing_type['Page']) {
                        $link = '<a ';
                        if ($config['featured_new_window']) {
                            $link .= 'target="_blank" ';
                        }
                        $link .= 'href="' . SEO_BASE;
                        if ($config['mod_rewrite']) {
                            $link .= $pages[$listing_type['Page_key']] . '/' . $listing['Path'] . '/' . $rlSmarty->str2path($listing['listing_title']) . '-' . $listing['ID'] . '.html';
                        } else {
                            $link .= '?page=' . $pages[$listing_type['Page_key']] . '&amp;id=' . $listing['ID'];
                        }
                        $link .= '" >';
                        $li .= $link;
                    }
                    $image = empty($listing['Main_photo']) ? RL_TPL_BASE . 'img/no-picture.jpg' : RL_FILES_URL . $listing['Main_photo'];
                    $li .= '<img alt="' . $listing['listing_title'] . '" title="' . $listing['listing_title'] . '" src="' . $image . '" style="width: ' . $config['pg_upload_thumbnail_width'] . 'px;height: ' . $config['pg_upload_thumbnail_height'] . 'px;" />';
                    if ($listing_type['Page']) {
                        $li .= '</a>';
                    }
                }
                $li .= '<ul>';
                $ct = 1;
                foreach ($listing['fields'] as $lKey => $item) {
                    if (!empty($item['value']) && $item['Details_page']) {
                        $class = $ct == 1 ? 'first' : '';
                        $class .= $priceTag && $item['Key'] == 'price' ? ' price_tag' : '';
                        if ($priceTag && $item['Key'] == 'sale_rent') {
                            $class .= ' sale-rent';
                            $class .= $item['value'] == $lang['listing_fields+name+sale_rent_1'] ? ' type-sale' : ' type-rent';
                        }
                        $li .= '<li id="flf_' . $listing['ID'] . '_' . $item['Key'] . '" class="' . $class . '" ' . $width . '>';
                        if ($ct == 1 || $priceTag && $item['Key'] == 'price') {
                            $li .= $link ? $link : '<b>';
                            $li .= $item['value'];
                            $li .= $item['Key'] == 'price' ? '<span></span>' : '';
                            $li .= $link ? '</a>' : '</b>';
                        } else {
                            $li .= $item['value'];
                        }
                        if ($priceTag && $item['Key'] == 'sale_rent') {
                            $li .= '<span></span><span></span>';
                        }
                        $li .= '</li>';
                        $ct = 2;
                    }
                }
                $li .= '</ul></div></li>';
                $li = $this->rlValid->xSql($li);
                $new_li .= "$('#carousel_" . $options[1] . " ul.featured>li').eq(" . $id . ").after('" . $li . "');";
                $id++;
            }
        }
        if (!$listings) {
            $conf = 'rlCarousel["carousel_' . $options[1] . '"] = 0';
        } else {
            $conf = 'rlCarousel["carousel_' . $options[1] . '"] = ' . ($number - count($listings));
        }
        $_response->script($conf);
        if ($new_li)
            $_response->script($new_li);
        $_response->script("$('#carousel_" . $options[1] . "').data('carousel')._afterLoadAjax();");
        return $_response;
    }
    function ajaxDeleteCarouselBox($id = false)
    {
        global $_response;
        $id = (int) $id;
        if ($this->checkSessionExpire() === false) {
            $_response->redirect(RL_URL_HOME . ADMIN . '/index.php?action=session_expired');
            return $_response;
        }
        if (!$id) {
            return $_response;
        }
        $this->query("DELETE FROM `" . RL_DBPREFIX . "listings_carousel` WHERE `ID` = '{$id}' LIMIT 1");
        $this->updateCarouselBlock();
        $_response->script("
				listingsCarousel.reload();
				printMessage('notice', '{$GLOBALS['lang']['block_deleted']}')
			");
        return $_response;
    }
}