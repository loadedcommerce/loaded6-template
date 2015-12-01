<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
  
  Featured Products Listing Module
*/
?>
<!--D featured_products-->
<?php
 require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_FEATURED_PRODUCTS);

//Eversun mod for sppc and qty price breaks
  if(!isset($_SESSION['sppc_customer_group_id'])) {
  $customer_group_id = 'G';
  } else {
   $customer_group_id = $_SESSION['sppc_customer_group_id'];
  }
     $info_box_contents = array();
 $mainpage_featured2_query = tep_db_query("select distinct
                           p.products_image, 
                           p.products_id,
                           pd.products_name
                          from (" . TABLE_PRODUCTS . " p 
                          left join " . TABLE_PRODUCTS_GROUPS . " pg on p.products_id = pg.products_id and pg.customers_group_id = '" . $customer_group_id . "'),
                              " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                        " . TABLE_FEATURED . " f
                                 where
                                   p.products_status = '1'
                                   and f.status = '1'
                                   and p.products_id = f.products_id
                                   and pd.products_id = f.products_id
                                   and pd.language_id = '" . $languages_id . "'
                                   and p.products_group_access like '%". $customer_group_id."%'
                                   order by rand(),p.products_date_added DESC, pd.products_name limit " . MAX_DISPLAY_FEATURED_PRODUCTS);
   
  $row = 0;
  $col = 0;
  $num = 0;
   $mainpage_featured2_check = tep_db_num_rows($mainpage_featured2_query);
     if ($mainpage_featured2_check > 0){
  while ($mainpage_featured2_products = tep_db_fetch_array($mainpage_featured2_query)) {
   $num ++;
   $mainpage_featured2_products_array[] = array('id' => $mainpage_featured2_products['products_id'],
                                  'name' => $mainpage_featured2_products['products_name'],
                                  'blurb' =>  cre_products_blurb($mainpage_featured2_products['products_id']),
                                  'image' => $mainpage_featured2_products['products_image']   );
  }
     for($i=0; $i<sizeof($mainpage_featured2_products_array); $i++) {
        $num++;
        $pf->loadProduct($mainpage_featured2_products_array[$i]['id'],$languages_id);
        $products_price = str_replace('&nbsp;',' ',$pf->getPriceStringShort());
        

$featured_str  = '';
$featured_str = '
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="contentBoxHeading">
  <tr>
    <td class="contentBoxHeadingLeft">' . tep_image_infobox('content_top_left.png') . '</td>
    <td width="100%" class="contentBoxHeadingCenter">&nbsp;</td>
    <td class="contentBoxHeadingRight">' . tep_image_infobox('content_top_right.png') . '</td>
  </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="contentBox">
  <tr>
    <td class="BoxBorderLeft">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>
    <td valign="top" width="100%">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" style="height:120px;"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $mainpage_featured2_products_array[$i]['id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . $mainpage_featured2_products_array[$i]['image'], $mainpage_featured2_products_array[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'hspace="5" vspace="5" align="left"') . '</a><div  class="pageHeading"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $mainpage_featured2_products_array[$i]['id'], 'NONSSL') . '">' . $mainpage_featured2_products_array[$i]['name'] . '</a></div><br />
      '  . (tep_not_null($mainpage_featured2_products_array[$i]['blurb'] && PRODUCT_BLURB == 'true') ? $mainpage_featured2_products_array[$i]['blurb'] . '<br>' : '') . '</td>
  </tr>
  <tr>
     <td style="background:url(' . DIR_WS_TEMPLATE_IMAGES . 'h-dots.png); height:1px;">' . tep_draw_separator('pixel_trans.gif', '100%', '1') . '</td>
  </tr>
  <tr>
    <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="69" align="center" class="price_mainpage">' . $products_price . '</td>
          <td width="1" align="center">' . tep_image(DIR_WS_TEMPLATE_IMAGES . 'gray-div.png') . '</td>
          <td width="88" align="center">';
if (hide_add_to_cart() == 'false' && group_hide_show_prices() == 'true') {
  $featured_str  .= '<a href="' . tep_href_link(FILENAME_SHOPPING_CART, tep_get_all_get_params(array('action','products_id')) . 'action=buy_now&products_id=' . $mainpage_featured2_products_array[$i]['id']) . '">' . tep_image(DIR_WS_TEMPLATE_IMAGES . 'buy-now.png') . '</a>';
}
$featured_str .= '
</td>
        </tr>
      </table></td>
  </tr>
</table>

</td>
    <td class="BoxBorderRight">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>
  </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="contentBoxFooter">
  <tr>
    <td class="contentBoxFooterLeft">' . tep_image_infobox('content_footer_left.png') . '</td>
    <td width="100%" class="contentBoxFooterCenter">' . tep_draw_separator('pixel_trans.gif', '100%', '1') . '</td>
    <td class="contentBoxFooterRight">' . tep_image_infobox('content_footer_right.png') . '</td>
  </tr>
</table>
';

  $info_box_contents[$row][$col] = array('align' => 'center',
                                         'params' => 'width="50%" valign="top" style="padding-top:12px;"',
                                         'text' => $featured_str);
  $col ++;
  if ($col > 1) {
    $col = 0;
    $row ++;
  }
}
if($num) {
  new productListingBox($info_box_contents);
}
  }
?>
 <!-- featured_products eof -->