<?php
header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: store, no-cache, max-age=3600, must-revalidate");
$listing_id     = (int) $_GET['id'];
$loan_amount    = $rlValid->str2money($_GET['amount']);
$loan_term      = $_GET['term'];
$loan_term_mode = $_GET['term_mode'];
$loan_rate      = (int) $_GET['rate'];
$price_mode     = (int) $_GET['mode'];
$month          = $_GET['date_month'];
$year           = $_GET['date_year'];
if (!$listing_id) {
    $errors[] = $lang['loanMortgage_listing_unavailable'];
}
if (!$errors) {
    $sql = "SELECT `T1`.*, `T2`.`Path`, `T2`.`Type` AS `Listing_type`, `T2`.`Key` AS `Cat_key`, `T2`.`Type` AS `Cat_type`, ";
    $sql .= "`T3`.`Image`, `T3`.`Image_unlim`, `T3`.`Video`, `T3`.`Video_unlim`, CONCAT('categories+name+', `T2`.`Key`) AS `Category_pName`, ";
    $sql .= "IF ( UNIX_TIMESTAMP(DATE_ADD(`T1`.`Pay_date`, INTERVAL `T3`.`Listing_period` DAY)) <= UNIX_TIMESTAMP(NOW()) AND `T3`.`Listing_period` > 0, 1, 0) AS `Listing_expired` ";
    $sql .= "FROM `" . RL_DBPREFIX . "listings` AS `T1` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "categories` AS `T2` ON `T1`.`Category_ID` = `T2`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "listing_plans` AS `T3` ON `T1`.`Plan_ID` = `T3`.`ID` ";
    $sql .= "LEFT JOIN `" . RL_DBPREFIX . "accounts` AS `T5` ON `T1`.`Account_ID` = `T5`.`ID` ";
    $sql .= "WHERE `T1`.`ID` = '{$listing_id}' AND `T5`.`Status` = 'active' ";
    $rlHook->load('listingDetailsSql', $sql);
    $sql .= "LIMIT 1";
    $listing_data = $rlDb->getRow($sql);
    if ($listing_data['Listing_expired']) {
        $errors[] = $lang['error_listing_expired'];
    }
    if (!$listing_data) {
        $errors[] = $lang['loanMortgage_listing_unavailable'];
    }
    if (!$errors) {
        $reefless->loadClass('Listings');
        $reefless->loadClass('Common');
        $reefless->loadClass('Account');
        $listing_type = $rlListingTypes->types[$listing_data['Listing_type']];
        $rlSmarty->assign_by_ref('listing_type', $listing_type);
        if ($listing_type['Photo'] && $listing_data['Main_photo']) {
            $main_photo = $rlDb->getOne('Photo', "`Listing_ID` = {$listing_id} AND `Thumbnail` = '{$listing_data['Main_photo']}'", 'listing_photos');
            $rlSmarty->assign_by_ref('main_photo', $main_photo);
        }
        $listing_title = $rlListings->getListingTitle($listing_data['Category_ID'], $listing_data, $listing_type['Key']);
        $rlSmarty->assign_by_ref('listing_title', $listing_title);
        $short_form_fields = $rlListings->getFormFields($listing_data['Category_ID'], 'short_forms', $listing_type['Key']);
        foreach ($short_form_fields as $fKey => $fValue) {
            $listing_short[$fKey]['name'] = $fValue['name'];
            if ($field['Condition'] == 'isUrl' || $field['Condition'] == 'isEmail') {
                $listing_short[$fKey]['value'] = $listing_data[$fKey];
            } else {
                $listing_short[$fKey]['value'] = $rlCommon->adaptValue($fValue, $listing_data[$fKey], 'listing', $value['ID']);
            }
        }
        unset($short_form_fields);
        $rlSmarty->assign_by_ref('listing_short', $listing_short);
        $sql = "SELECT * FROM `" . RL_DBPREFIX . "accounts` ";
        $sql .= "WHERE `ID` = {$listing_data['Account_ID']} ";
        $seller_data     = $rlDb->getRow($sql);
        $account_type_id = $rlDb->getOne('ID', "`Key` = '{$seller_data['Type']}'", 'account_types');
        $seller_fields   = $rlAccount->getFormFields($account_type_id);
        foreach ($seller_fields as $fKey => $fValue) {
            $seller_short[$fKey]['name'] = $lang[$fValue['pName']];
            if ($field['Condition'] == 'isUrl' || $field['Condition'] == 'isEmail') {
                $seller_short[$fKey]['value'] = $seller_data[$fKey];
            } else {
                $seller_short[$fKey]['value'] = $rlCommon->adaptValue($fValue, $seller_data[$fKey], 'account', $value['ID']);
            }
        }
        unset($seller_fields);
        $rlSmarty->assign_by_ref('seller_short', $seller_short);
        $currency_exp = explode('|', $listing_data[$config['loanMortgage_price_field']]);
        if ($price_mode == 'converter') {
            $currency_exp[1] = $_GET['currency'];
        }
        $currency = $lang['data_formats+name+' . $currency_exp[1]] ? $lang['data_formats+name+' . $currency_exp[1]] : $currency_exp[1];
        $rlSmarty->assign_by_ref('currency_exp', $currency_exp);
        $set_amount = $config['system_currency_position'] == 'before' ? $currency . ' ' . $loan_amount : $loan_amount . ' ' . $currency;
        $loan_terms = array(
            array(
                'name' => $lang['loanMortgage_loan_amount'],
                'value' => $set_amount
            ),
            array(
                'name' => $lang['loanMortgage_loan_term'],
                'value' => $loan_term . ' ' . $lang['loanMortgage_' . $loan_term_mode . 's']
            ),
            array(
                'name' => $lang['loanMortgage_interest_rate'],
                'value' => $loan_rate . '%'
            ),
            array(
                'name' => $lang['loanMortgage_first_pmt_date'],
                'value' => $month . ' ' . $year
            )
        );
        $rlSmarty->assign_by_ref('loan_terms', $loan_terms);
    }
}