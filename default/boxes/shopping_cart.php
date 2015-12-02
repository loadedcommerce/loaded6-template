<?php
/*
  $Id: shopping_cart.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
//declare and intilize variables
$products = '';
$cart_contents_string = '';
$new_products_id_in_cart = '';
?>
<!-- shopping_cart //-->
<div class="well" >
  <ul class="box-shopping-cart list-unstyled list-indent-large">
    <li class="box-header small-margin-bottom"><?php     echo  BOX_HEADING_SHOPPING_CART ; ?></li>




<script type="text/javascript"><!--
function couponpopupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
    <?php

    if ($cart->count_contents() > 0) {
      $cart_contents_string = '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
      $products = $cart->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        $cart_contents_string .= '<tr><td align="right" valign="top" class="infoBoxContents">';
        if (isset($_SESSION['new_products_id_in_cart'])  && ($_SESSION['new_products_id_in_cart'] == $products[$i]['id'])) {
          $cart_contents_string .= '<span class="newItemInCart">';
        } else {
          $cart_contents_string .= '<span class="infoBoxContents">';
        }
        $db_sql = "select products_parent_id from " . TABLE_PRODUCTS . " where products_id = " . (int)$products[$i]['id'];
        $products_parent_id = tep_db_fetch_array(tep_db_query($db_sql));
        if ((int)$products_parent_id['products_parent_id'] != 0) {
          $cart_contents_string .= $products[$i]['quantity'] . '&nbsp;x&nbsp;</span></td><td valign="top" class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_parent_id['products_parent_id']) . '">';
        } else {
          $cart_contents_string .= $products[$i]['quantity'] . '&nbsp;x&nbsp;</span></td><td valign="top" class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">';
        }
        if (isset($_SESSION['new_products_id_in_cart'])  && ($_SESSION['new_products_id_in_cart'] == $products[$i]['id'])) {
          $cart_contents_string .= '<span class="newItemInCart">';
        } else {
          $cart_contents_string .= '<span class="infoBoxContents">';
        }
        $cart_contents_string .= $products[$i]['name'] . '</span></a></td></tr>';
        if (isset($_SESSION['new_products_id_in_cart'])  && ($_SESSION['new_products_id_in_cart'] == $products[$i]['id'])) {
          unset($_SESSION['new_products_id_in_cart']);
        }
      }
      $cart_contents_string .= '</table>';
    } else {
      $cart_contents_string .= BOX_SHOPPING_CART_EMPTY;
    }
   echo  $cart_contents_string;
    if ($cart->count_contents() > 0) {
      $sub_total = $cart->show_total();
      if ($sub_total == 0) {
        $sub_total = 'Free';
      } else {
        $sub_total = $currencies->format($cart->show_total());
      }
      echo $sub_total;
      // RCI insert offset
      $offset_amount = 0;
      $final_total = $currencies->format($cart->show_total() + $offset_amount);
      $returned_rci = $cre_RCI->get('shoppingcart', 'infoboxoffsettotal');
      if (($returned_rci != NULL) && $offset_amount != 0) {
        $info_box_contents[] = array('text' => $returned_rci);
      }
    }
    if ( isset($_SESSION['customer_id']) ) {
      $gv_query = tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . $_SESSION['customer_id'] . "'");
      $gv_result = tep_db_fetch_array($gv_query);
      if ($gv_result['amount'] > 0 ) {
        echo VOUCHER_BALANCE . '' . $currencies->format($gv_result['amount']) . '';
        echo '<a href="'. tep_href_link(FILENAME_GV_SEND) . '">' . BOX_SEND_TO_FRIEND . '</a>';
      }
    }
    if (isset($_SESSION['gv_id']) ) {
      $gv_query = tep_db_query("select coupon_amount from " . TABLE_COUPONS . " where coupon_id = '" . $_SESSION['gv_id'] . "'");
      $coupon = tep_db_fetch_array($gv_query);
echo  VOUCHER_REDEEMED . '' . $currencies->format($coupon['coupon_amount']) . '';
    }
    if (isset($_SESSION['cc_id']) && tep_not_null($_SESSION['cc_id'])) {
      $cart_coupon_query = tep_db_query("select coupon_code, coupon_type from " . TABLE_COUPONS . " where coupon_id = '" . (int)$_SESSION['cc_id'] . "'");
      $cart_coupon_info = tep_db_fetch_array($cart_coupon_query);
      $info_box_contents[] = array('align' => 'left','text' => tep_draw_separator());
      $info_box_contents[] = array('align' => 'left','text' => CART_COUPON . ' ' . $cart_coupon_info['coupon_code'] . ' <a href="javascript:couponpopupWindow(\'' . tep_href_link(FILENAME_POPUP_COUPON_HELP, 'cID=' . $_SESSION['cc_id']) . '\')">' . tep_image(DIR_WS_ICONS . 'warning.gif', CART_COUPON_INFO) . '</a>');
      if($cart_coupon_info['coupon_type'] == 'F') {
        $info_box_contents[] = array('align' => 'center','text' => 'Free Shipping');
      }
    }
  //  new $infobox_template($info_box_contents, true, true, ((isset($column_location) && $column_location !='') ? $column_location : '') );
   ?>

  </ul>
</div>
<script>
$(document).ready(function() {
  $(".box-shopping-cart li").each(function(){
    $(this).find('a').attr('style', 'display:inline;');
  });
   $(".box-shopping-cart li:last-child").addClass('margin-top align-right');
});
</script>


<!-- shopping_cart_eof //-->