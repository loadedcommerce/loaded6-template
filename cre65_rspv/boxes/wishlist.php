<?php
/*
  $Id: wishlist.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
if (basename($PHP_SELF) != FILENAME_WISHLIST_SEND && basename($PHP_SELF) != FILENAME_WISHLIST) {
  $wishlist = new box_wishlist();
?>
    <!-- wishlist //-->
     <div class="well">
      <div class="box-header small-margin-bottom small-margin-left"><?php echo  BOX_HEADING_WISHLIST ; ?></div>

        <script type="text/javascript"><!--
          function popupWindowWishlist(url) {
            window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=425,height=475,screenX=150,screenY=150,top=150,left=150')
          }
        //--></script>
        <?php
          if (count($wishlist->rows) < 1) {
            foreach ($wishlist->rows as $product_id) {
              $products = $pf->loadProduct($product_id, $languages_id);
              echo                         '' . "\n" .
                                           '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'cPath=' . tep_get_product_path($products['products_id']) . '&products_id=' . $products['products_id'], 'NONSSL') . '">' . $products['products_name'] . '</a>' . "\n" .
                                           '' . "\n" .
                                           '' . "\n" .
                                           '<b><a href="' . tep_href_link(FILENAME_WISHLIST, tep_get_all_get_params(array('action','cPath','products_id')) . 'action=buy_now&products_id=' . $products['products_id'] . '&cPath=' . tep_get_product_path($products['products_id']), 'NONSSL') . '">' . BOX_TEXT_MOVE_TO_CART . '</a>&nbsp;|' . "\n" .
                                           '<a href="' . tep_href_link(FILENAME_WISHLIST, tep_get_all_get_params(array('action')) . 'action=remove_wishlist&pid=' . $products['products_id'], 'NONSSL') . '">' . BOX_TEXT_DELETE . '</a></b>' . "\n" .  tep_draw_separator('pixel_black.gif', '100%', '1') . tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '5') . '' .
                                           '' . "\n";
            }
          } else {
            echo  BOX_WISHLIST_EMPTY . "\n";
          }
          echo  '<a href="' . tep_href_link(FILENAME_WISHLIST, '','NONSSL') . '"><u> ' . BOX_HEADING_CUSTOMER_WISHLIST . '</u> [+]</a>' . "\n";
          echo  '<br><a href="' . tep_href_link(FILENAME_WISHLIST_HELP, '','NONSSL') . '"><u> ' . BOX_HEADING_CUSTOMER_WISHLIST_HELP . '</u> [?]</a>' . "\n"; // Normal link
		  echo $customer_wishlist_string ;
        ?>

</div>
<?php
}
?>
<!-- wishlist eof//-->