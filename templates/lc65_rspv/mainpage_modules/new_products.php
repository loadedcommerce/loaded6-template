<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- D mainpages_modules.new_products.php//-->
<?php

//Eversun mod for sppc and qty price breaks
  if(!isset($_SESSION['sppc_customer_group_id'])) {
  $customer_group_id = 'G';
  } else {
   $customer_group_id = $_SESSION['sppc_customer_group_id'];
  }
  $info_box_contents = array();
  $info_box_contents='<h3 class="no-margin-top">'. sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')) .'</h3>';


      $new_products_query = tep_db_query("select distinct
                          p.products_id,
                          p.products_price,
                          p.manufacturers_id,
                          pd.products_name,
                          p.products_tax_class_id,
                          if (isnull(pg.customers_group_price), p.products_price, pg.customers_group_price) as products_price,
                          p.products_date_added,
                           p.products_image
                          from ((" . TABLE_PRODUCTS . " p
      left join " . TABLE_PRODUCTS_GROUPS . " pg on p.products_id = pg.products_id and pg.customers_group_id = '" . $customer_group_id . "')
      left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id),
      " . TABLE_PRODUCTS_DESCRIPTION . " pd
      where
       p.products_status = '1'
       and pd.products_id = p.products_id
       and pd.language_id = '" . $languages_id . "'
       and p.products_group_access like '%". $customer_group_id."%'
       and DATE_SUB(CURDATE(),INTERVAL " .NEW_PRODUCT_INTERVAL ." DAY) <= p.products_date_added
                                   and p.products_group_access like '%". $customer_group_id."%'
       order by rand(), p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);




  $row = 0;
  $col = 0;
  $num = 0;
  while ($new_products = tep_db_fetch_array($new_products_query)) {

    $num ++;
      if ($num == 1) {
 // new contentBoxHeading($info_box_contents, tep_href_link(FILENAME_PRODUCTS_NEW));
//  echo $info_box_contents;
      }

  $pf->loadProduct($new_products['products_id'],$languages_id);
        $products_price_s = $pf->getPriceStringShort();


    echo '<div class="product-listing-module-container"><div class="product-listing-module-items"><div class="col-sm-4 col-lg-4 with-padding"><div class="thumbnail align-center large-padding-top" style="height: 410px;"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $new_products['products_image'], $new_products['products_name'], 200) . '</a><br><h3 style="line-height:1.1;"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . $new_products['products_name'] . '</a></h3><p class="">' . cre_products_blurb($new_products['products_id']) . '</p><div class="row pricing-row"><div class="col-sm-6 col-lg-6"> <p class="lead small-margin-bottom">' . $products_price_s . '</p></div><div class="col-sm-5 col-lg-5 no-margin-left buy-btn-div"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action','cPath','products_id')) . 'action=buy_now&amp;products_id=' . $new_products['products_id']) . '" style="text-decoration:none"><button type="button" class="content-new-products-add-button btn btn-success btn-block">Buy Now</button></a></div></div></div></div></div></div>';


    $col ++;
    if ($col > 2) {
      $col = 0;
      $row ++;
    }
  }

  if($num) {
 // new contentBox($info_box_contents, true, true);
 }
?>
<!-- D new_products_eof //-->
