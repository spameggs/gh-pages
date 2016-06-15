<?php
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');
if ($_GET['listingID']) {
    $reefless->loadClass('Listings');
    $listing_id = (int) $_GET['listingID'];
    if ($aHooks['multiField']) {
        $sql = "SELECT * FROM `" . RL_DBPREFIX . "multi_formats` WHERE 1 ";
        global $multi_formats;
        $mf_tmp = $rlDb->getAll($sql);
        foreach ($mf_tmp as $key => $item) {
            $multi_formats[$item['Key']] = $item;
        }
    }
    $sql = "SELECT `T1`.*, `T2`.`Path`, `T2`.`Type` AS `Listing_type`, `T2`.`Key` AS `Cat_key`, `T2`.`Type` AS `Cat_type`, ";
    $sql .= "`T3`.`Image`, `T3`.`Image_unlim`, `T3`.`Video`, `T3`.`Video_unlim`, CONCAT('categories+name+', `T2`.`Key`) AS `Category_pName` ";
    $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Category_ID` = `T2`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T3` ON `T1`.`Plan_ID` = `T3`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T5` ON `T1`.`Account_ID` = `T5`.`ID` ";
    $sql .= "WHERE `T1`.`ID` = '{$listing_id}' AND `T5`.`Status` = 'active' LIMIT 1";
    $listing_data      = $rlDb->getRow($sql);
    $listing_type      = $rlListingTypes->types[$listing_data['Listing_type']];
    $category_id       = $listing_data['Category_ID'];
    $listing           = $rlListings->getListingDetails($category_id, $listing_data, $listing_type);
    $listing_title     = $rlListings->getListingTitle($category_id, $listing_data, $listing_type['Key']);
    $listing_url       = SEO_BASE . $pages[$listing_type['Page_key']] . '/' . $listing_data['Path'] . '/' . $rlSmarty->str2path($listing_title) . '-l' . $listing_data['ID'] . '.html';
    $photos            = $rlDb->fetch('*', array(
        'Listing_ID' => $listing_id,
        'Status' => 'active'
    ), "AND `Thumbnail` <> '' AND `Photo` <> '' ORDER BY `Position`", $listing_data['Image'], 'listing_photos');
    $photo             = $photos ? RL_FILES . $photos[0]['Photo'] : RL_PLUGINS . 'PdfExport/no-photo.jpg';
    $seller_info       = $rlAccount->getProfile((int) $listing_data['Account_ID']);
    $additional_fields = $seller_info["Fields"];
    $seller_name       = $seller_info['First_name'] || $seller_info['Last_name'] ? $seller_info['First_name'] . ' ' . $seller_info['Last_name'] : $seller_info['Username'];
    $pdf               = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('PDF Export Plugin');
    $pdf->SetAuthor($seller_name);
    $pdf->SetTitle($listing_title);
    $pdf->SetSubject('PDF Listing Export');
    $pdf->SetKeywords('PDF, export, PDF Export');
    $pdf->SetHeaderData('../../templates' . RL_DS . $config['template'] . RL_DS . 'img' . RL_DS . 'logo.png', 35, $lang['pages+title+home'], SEO_BASE);
    $pdf->setHeaderFont(Array(
        PDF_FONT_NAME_MAIN,
        '',
        PDF_FONT_SIZE_MAIN
    ));
    $pdf->setFooterFont(Array(
        PDF_FONT_NAME_DATA,
        '',
        PDF_FONT_SIZE_DATA
    ));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setLanguageArray($l);
    $pdf->SetFont('freeserif', '', 12);
    $pdf->AddPage();
    $pdf->SetTextColor(39, 39, 39);
    $html = '
	<table width="100%">
	<tr>
		<td colspan="2" align="left" height="30px">
			<a style="color: #444444; font-size: 56px;" href="' . $listing_url . '">' . $listing_title . '</a>
		</td>
	</tr>
	<tr>
		<td width="250px">
			<img src="' . $photo . '" alt="' . $listing_title . '" width="230px" border="0" />
		</td>
		<td width="388px">';
    $html .= '<table>
				<tr>
					<td colspan="2" style="background-color: #e5e5e5;">' . $lang['seller_info'] . '</td>
				</tr>
				<tr>
					<td width="100" style="color: #676766;height: 20px;">' . $lang["name"] . ':</td>
					<td width="288px">' . $seller_name . '</td>
				</tr>';
    if ($seller_info["Display_email"]) {
        $html .= '<tr>
					<td style="color: #676766;height: 20px;">' . $lang["mail"] . ':</td>
					<td>' . $seller_info["Mail"] . '</td>
				</tr>';
    }
    foreach ($additional_fields as $key => $additional_value) {
        if (substr_count($additional_value["value"], "http")) {
            $additional_value["value"] = str_replace(RL_URL_HOME, "", $additional_value["value"]);
        }
        $html .= '<tr>
					<td style="color: #676766;height: 20px;">' . $additional_value["name"] . ':</td>
					<td>' . $additional_value["value"] . '</td>
				</tr>';
    }
    $html .= '</table>
		</td>
	</tr>
	<table><br />
	<br />';
    $html .= '<table width="100%">
	<tr>
		<td colspan="2" style="background-color: #e5e5e5;">' . $lang['listing_details'] . '</td>
	</tr>
	';
    foreach ($listing as $key => $value) {
        $count = 0;
        foreach ($value['Fields'] as $key => $fields_count) {
            if (!empty($fields_count))
                $count = $count + 1;
        }
        if ($count > 0) {
            $html .= '<tr>
						<td colspan="2" height="20">
							<font size="16" color="#666666"><b>' . $value["name"] . '</b></font>
						</td>
					</tr>';
            foreach ($value['Fields'] as $key => $fields) {
                $html .= '<tr>
							<td width="100"><b>' . $fields["name"] . ':</b></td><td>' . $fields["value"] . '</td>
						</tr>';
            }
            $html .= '<tr>
				<td colspan="2" height="20"></td>
			</tr>';
        }
    }
    $html .= '</table>';
    if (RL_LANG_DIR == 'rtl') {
        $pdf->setRTL(true);
    }
    $pdf->writeHTML($html, true, false, true, false, 'left');
    $pdf->Output('pdfExport_listing' . $listing_data['ID'] . '.pdf', 'I');
} else {
    $sError = true;
}