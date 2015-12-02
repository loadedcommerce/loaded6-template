<?php
/*
  $Id: product_info.tpl.php,v 1.2.0.0 2008/01/22 13:41:11 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
// RCI code start
echo $cre_RCI->get('global', 'top');
echo $cre_RCI->get('productinfotabs', 'top');
// RCI code eof
echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action', 'products_id', 'id')) . 'action=add_product' . '&' . $params), 'post', 'enctype="multipart/form-data"'); ?>

<table border="0" width="100%" cellspacing="0" cellpadding="<?php echo CELLPADDING_SUB;?>">
<?php
  if ($messageStack->size('cart_quantity') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('cart_quantity'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }

  // added for CDS CDpath support
  $params = (isset($_SESSION['CDpath'])) ? 'CDpath=' . $_SESSION['CDpath'] : '';
  if ($product_check['total'] < 1) {
    ?>
  <tr>
    <td><?php  new infoBox(array(array('text' => TEXT_PRODUCT_NOT_FOUND))); ?></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>
  <tr>
    <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
        <tr class="infoBoxContents">
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, $params) . '">' . tep_template_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <?php
  } else {
    $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image, p.products_image_med, p.products_image_lrg, p.products_image_sm_1, p.products_image_xl_1, p.products_image_sm_2, p.products_image_xl_2, p.products_image_sm_3, p.products_image_xl_3, p.products_image_sm_4, p.products_image_xl_4, p.products_image_sm_5, p.products_image_xl_5, p.products_image_sm_6, p.products_image_xl_6, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
    $product_info = tep_db_fetch_array($product_info_query);
    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$product_info['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
   /*if (tep_not_null($product_info['products_model'])) {
      $products_name = '<h1>' . $product_info['products_name'] . '</h1>&nbsp;<span class="smallText">[' . $product_info['products_model'] . ']</span>';
    } else {
      $products_name = '<h1 class="pageHeading">' . $product_info['products_name'] . '</h1>';
    }*/
    $products_name = '<h1 class="pageHeading">' . $product_info['products_name'] . '</h1>';
    if ($product_has_sub > '0'){ // if product has sub products
      $products_price ='';// if you like to show some thing in place of price add here
    } else {
      $pf->loadProduct($product_info['products_id'],$languages_id);
      $products_price = $pf->getPriceStringShort();
    } // end sub product check
      ?>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td><?php echo $products_name; ?></td>
          <td class="pageHeading" align="right">&nbsp;<?php //echo $products_price; ?></td>
        </tr>
        <tr>
          <td colspan="2"><?php
              if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
                echo '<span class="main">' . sprintf(TEXT_DATE_AVAILABLE . '<br>', tep_date_long($product_info['products_date_available'])) . '</span>';
              }
              ?>
          </td>
        </tr>
      </table></td>
  </tr>
  <?php
    // RCI code start
    echo $cre_RCI->get('productinfotabs', 'underpriceheading');
    // RCI code eof
  ?>
  <tr>
    <td>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top">
        <tr>
          <td class="main">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
              <tr>
                <td>
                <?php
                  if (tep_not_null($product_info['products_image']) || tep_not_null($product_info['products_image_med'])) {
                    if ($product_info['products_image_med']!='') {
                      $new_image = $product_info['products_image_med'];
                      $image_width = MEDIUM_IMAGE_WIDTH;
                      $image_height = MEDIUM_IMAGE_HEIGHT;
                    } else {
                      $new_image = $product_info['products_image'];
                      $image_width = SMALL_IMAGE_WIDTH;
                      $image_height = SMALL_IMAGE_HEIGHT;
                    }
                    echo '<center><a href="' . tep_href_link(DIR_WS_IMAGES . $product_info['products_image_lrg']) . '" rel="prettyPhoto[Product]" title="' .$product_info['products_name']. '">' . tep_image(DIR_WS_IMAGES . $new_image, $product_info['products_name'], $image_width, $image_height, 'hspace="5" vspace="5"') . '</a><br><span class="smallText">' . TEXT_CLICK_TO_ENLARGE . '</span></center>';
                    if (isset($_SESSION['affiliate_id'])) {
                      echo '<br><br><a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_BUILD, 'individual_banner_id=' . $product_info['products_id'] . '&' . $params) .'" target="_self">' . tep_template_image_button('button_affiliate_build_a_link.gif', LINK_ALT) . ' </a>';
                    }
                  }
                ?>
                </td>
                <td valign="top">
                <table width="100%" border="0" cellspacing="4" cellpadding="0">
                   <?php if ($product_has_sub > '0') { } else { ?>
                  <tr>
                    <td class="main"><b><?php echo TEXT_PRICE;?></b></td>
                    <td class="pageHeading"><?php echo $products_price; ?></td>
                  </tr>
                  <?php } if (tep_not_null($product_info['products_model'])) { ?>
                  <tr>
                    <td class="main"><b><?php echo TEXT_MODEL;?></b></td>
                    <td class="main"><b><?php echo $product_info['products_model']; ?></b></td>
                  </tr>
                  <?php }
                  if ($product_info['manufacturers_id'] != 0) {
                  ?>
                  <tr>
                    <td class="main"><b><?php echo TEXT_MANUFACTURER;?></b></td>
                    <td class="main"><b><?php echo tep_get_manufacturers_name($product_info['manufacturers_id']); ?></b></td>
                  </tr>
                  <?php } ?>
                  <tr>
                  <?php
                    $hide_add_to_cart = hide_add_to_cart();
                    if ($hide_add_to_cart == 'false' && group_hide_show_prices() == 'true') {
                      if ($product_has_sub > '0') {
                        echo '<td class="main">&nbsp;</td>';
                      } else {
                  ?>
                    <td class="main"><?php echo TEXT_ENTER_QUANTITY . ':&nbsp;&nbsp;' . tep_draw_input_field('cart_quantity', '1', 'size="4" maxlength="4" id="Qty1" onkeyup="document.getElementById(\'Qty2\').value = document.getElementById(\'Qty1\').value;"  ');?></td>
                  <?php
                      }
                    }
                  ?>
                    <td class="main">
                    <?php
                      $valid_to_checkout= true;
                      if (STOCK_CHECK == 'true') {
                        $stock_check = tep_check_stock((int)$_GET['products_id'], 1);
                        if (tep_not_null($stock_check) && (STOCK_ALLOW_CHECKOUT == 'false')) {
                          $valid_to_checkout = false;
                        }
                      }
                      if ($hide_add_to_cart == 'false' && group_hide_show_prices() == 'true') {
                        echo tep_draw_hidden_field('products_id', $product_info['products_id']);
                        if ($valid_to_checkout == true) {
                          echo ( (PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Multi' ||
                                 (PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Single' && PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'Drop Down')
                               ) ? tep_template_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART,'align="middle"') : '');
                        }
                      }
                    ?>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <!-- AddThis Button BEGIN -->
                      <div class="addthis_toolbox addthis_default_style ">
                      <a class="addthis_button_preferred_1"></a>
                      <a class="addthis_button_preferred_2"></a>
                      <a class="addthis_button_preferred_3"></a>
                      <a class="addthis_button_preferred_4"></a>
                      <a class="addthis_button_compact"></a>
                      <a class="addthis_counter addthis_bubble_style"></a>
                      </div>
                      <script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
                      <script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4d9b61612caae177"></script>
                      <!-- AddThis Button END -->
                    </td>
                    <td>
                    <?php
                      if ($product_check['total'] > 0) {
                        if (DESIGN_BUTTON_WISHLIST == 'true') {
                          echo '<script type="text/javascript"><!--' . "\n";
                          echo 'function addwishlist() {' . "\n";
                          echo 'document.cart_quantity.action=\'' . str_replace('&amp;', '&', tep_href_link(FILENAME_PRODUCT_INFO, 'action=add_wishlist' . '&' . $params)) . '\';' . "\n";
                          echo 'document.cart_quantity.submit();' . "\n";
                          echo '}' . "\n";
                          echo '--></script>' . "\n";
                          echo '<a href="javascript:addwishlist()">' . tep_template_image_button('button_add_wishlist.gif', IMAGE_BUTTON_ADD_WISHLIST,'align="middle"') . '</a>';
                        }
                      } // if products_check
                    ?>
                    </td>
                  </tr>
                  <!-- Points & Rewards START -->
                  <tr>
                    <td colspan="2">
                      <br /><div id="pointsRewards">
                      <?php
                        if (defined(MODULE_ADDONS_POINTS_STATUS) && MODULE_ADDONS_POINTS_STATUS == 'True') {
                          $productTaxClass_query = tep_db_query("SELECT products_tax_class_id, products_price FROM " . TABLE_PRODUCTS . " where products_id = '" . $_GET['products_id'] . "'");
                          $productTaxClass = tep_db_fetch_array($productTaxClass_query);
                          $hasSpecialPrice = $pf->hasSpecialPrice();
                          if ($hasSpecialPrice == 1) {
                            $specialPrice = tep_db_fetch_array(tep_db_query("SELECT specials_new_products_price FROM " . TABLE_SPECIALS . " where products_id = '" . $_GET['products_id'] . "'"));
                            $products_price_points = tep_display_points($specialPrice['specials_new_products_price'], tep_get_tax_rate($productTaxClass['products_tax_class_id']));
                          } else {
                            $products_price_points = tep_display_points($productTaxClass['products_price'], tep_get_tax_rate($productTaxClass['products_tax_class_id']));
                          }
                          $products_points = tep_calc_products_price_points($products_price_points);
                          $products_points_value = tep_calc_price_pvalue($products_points);
                          $q_cID_reverse = array_reverse(explode("_", $cPath));
                          $q_cID = $q_cID_reverse[0];
                          $cat_ids = explode(",", RESTRICTION_PATH);
                          $p_ids = explode(",", RESTRICTION_PID);
                          $m_ids = explode(",", RESTRICTION_MODEL);
                          if (!in_array($q_cID, $cat_ids)) {
                            if (!in_array($product_info['products_id'], $p_ids)) {
                              if (!in_array($product_info['products_model'], $m_ids)) {
                                if (USE_POINTS_FOR_SPECIALS == 'true' || $new_price == false) {
                                  if ((USE_REDEEM_SYSTEM == 'true') && (tep_not_null(USE_POINTS_FOR_REVIEWS))) {
                                    echo '<table border="0" cellspacing="0" cellpadding="0">
                                            <tr class="infoBoxContents">
                                              <td><strong>' . TEXT_PRODUCT_POINTS_HEADING . '</strong></td>
                                            </tr>
                                          </table>
                                          <table border="0" cellspacing="1" cellpadding="2" class="infoBox">
                                            <tr class="infoBoxContents">
                                              <td>' . sprintf(TEXT_PRODUCT_POINTS , number_format($products_points, POINTS_DECIMAL_PLACES), $currencies->format($products_points_value)) . '<br />' . REVIEW_HELP_LINK . '</td>
                                            </tr>
                                          </table>';
                                  } else {
                                    echo '<table border="0" cellspacing="0" cellpadding="0">
                                            <tr class="infoBoxContents">
                                              <td><strong>' . TEXT_PRODUCT_POINTS_HEADING . '</strong></td>
                                            </tr>
                                          </table>
                                          <table border="0" cellspacing="1" cellpadding="2" class="infoBox">
                                            <tr class="infoBoxContents">
                                              <td>' . sprintf(TEXT_PRODUCT_POINTS , number_format($products_points, POINTS_DECIMAL_PLACES), $currencies->format($products_points_value)) . '<br />' . PRODUCTINFO_REVIEW_HELP_LINK . '</td>
                                            </tr>
                                          </table>';
                                  }
                                } else {
                                  echo '<table border="0" cellspacing="0" cellpadding="0">
                                            <tr class="infoBoxContents">
                                              <td><strong>' . TEXT_PRODUCT_POINTS_HEADING . '</strong></td>
                                            </tr>
                                          </table>
                                          <table border="0" cellspacing="1" cellpadding="2" class="infoBox">
                                          <tr class="infoBoxContents">
                                            <td>' . TEXT_PRODUCT_NO_POINTS . '<br />' . REVIEW_HELP_LINK . '
                                          </tr>
                                        </table>
                                      </td>';
                                }
                              }
                            }
                          }
                        }
                      ?>
                      </div>
                    </td>
                  </tr>
                  <!-- Points & Rewards END -->
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
      <td class="main"><!-- tabs start -->
      <!-- tab1 -->
      <div class="tab-pane" id="product_info_tabs">
        <script type="text/javascript">tp1 = new WebFXTabPane( document.getElementById( "product_info_tabs" ) );</script>
        <div class="tab-page" id="product_info">
          <h2 class="tab"><?php echo TEXT_TAB_PRODUCT_INFO;?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_info" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:100%">
            <tr>
              <td class="main">
              <?php
        echo '<p>' .  cre_clean_product_description($product_info['products_description']) . '</p>';
    if (tep_not_null($product_info['products_url'])) {
         echo '<br>' . sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&amp;goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)) . '</br>';
         }
         ?>
         </td>
         </tr>
            <!-- attributes -->
            <?php
              $products_id_tmp = $product_info['products_id'];
            if(tep_subproducts_parent($products_id_tmp)){
              $products_id_query = tep_subproducts_parent($products_id_tmp);
            } else {
              $products_id_query = $products_id_tmp;
            }
            if($product_has_sub > '0') {
              if ((defined('PRODUCT_INFO_SUB_PRODUCT_ATTRIBUTES') && PRODUCT_INFO_SUB_PRODUCT_ATTRIBUTES == 'False')) {
                // 2.a) PRODUCT_INFO_SUB_PRODUCT_ATTRIBUTES = False
                //        -- Show attributes to main product only
                $load_attributes_for = $products_id_query;
                //$load_attributes_for = $products_id_query;
                include(DIR_WS_MODULES . 'product_info/product_attributes.php');
              } else {
                // 2.b) PRODUCT_INFO_SUB_PRODUCT_ATTRIBUTES = True
                //        -- Show attributes to sub product only
              }
            } else {
              // show attributes for parent only
              $load_attributes_for = $products_id_query;
              include(DIR_WS_MODULES . 'product_info/product_attributes.php');
            }
            ?>
            <!-- attributes eof -->
            <!-- sub products -->
            <?php
    // sub product start
    if (STOCK_ALLOW_CHECKOUT =='false') {
      $allowcriteria = "";
    }
    // get sort order
    $csort_order = tep_db_fetch_array(tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'SUB_PRODUCTS_SORT_ORDER'"));
    $select_order_by = '';
    switch (strtoupper($csort_order['configuration_value'])) {
      case 'MODEL':
        $select_order_by .= 'sp.products_model';
        break;
      case 'NAME':
        $select_order_by .= 'spd.products_name';
        break;
      case 'PRICE':
        $select_order_by .= 'sp.products_price';
        break;
      case 'QUANTITY':
        $select_order_by .= 'sp.products_quantity';
        break;
      case 'WEIGHT':
        $select_order_by .= 'sp.products_weight';
        break;
      case 'SORT ORDER':
        $select_order_by .= 'sp.sort_order';
        break;
      case 'LAST ADDED':
        $select_order_by .= 'sp.products_date_added';
        break;
      default:
        $select_order_by .= 'sp.products_model';
        break;
    }
	if(INSTALLED_VERSION_TYPE == 'B2B' || INSTALLED_VERSION_TYPE == 'Pro'){
	  $blurb = ' spd.products_blurb,';
	}else
	{
		 $blurb = '';
	}

    $sub_products_query = tep_db_query("select sp.products_id, sp.products_quantity, sp.products_price, sp.products_tax_class_id, sp.products_image, spd.products_name, ".$blurb." sp.products_model from " . TABLE_PRODUCTS . " sp, " . TABLE_PRODUCTS_DESCRIPTION . " spd where sp.products_quantity > 0 and sp.products_parent_id = " . (int)$product_info['products_id'] . " and spd.products_id = sp.products_id and spd.language_id = " . (int)$languages_id . " order by " . $select_order_by);
    if ( tep_db_num_rows($sub_products_query) > 0 ) {
        if(defined('PRODUCT_INFO_SUB_PRODUCT_DISPLAY') && PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'In Listing'){
            include(DIR_WS_MODULES . 'product_info/sub_products_listing.php');
        } else if ( PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'Drop Down'){
            include(DIR_WS_MODULES . 'product_info/sub_products_dropdown.php');
        }
    }
    // sub product_eof
    ?>
            <!-- sub products eof -->
          </table>
        </div>
        <!-- Tab 1 eof -->
<?php
// Products Tabs Module Start
    if (defined('MODULE_ADDONS_TABS_STATUS') && MODULE_ADDONS_TABS_STATUS == 'True') {
    $product_tabs_query = tep_db_query("select products_tab_2, products_tab_3, products_tab_4 from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_info['products_id'] . "'");
    $product_tab = tep_db_fetch_array($product_tabs_query);
    if(tep_not_null($product_tab['products_tab_2'])){
?>
        <div class="tab-page" id="product_tab_2">
          <h2 class="tab"><?php echo tep_not_null(TEXT_PRODUCTS_TAB_2_TITLE ) ? TEXT_PRODUCTS_TAB_2_TITLE : ' &nbsp; '; ?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_tab_2" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:auto">
            <tr>
              <td class="main" valign="top"><?php echo cre_clean_product_description($product_tab['products_tab_2']); ?></td>
            </tr>
          </table>
        </div>
<?php
}
    if(tep_not_null($product_tab['products_tab_3'])){
?>
        <div class="tab-page" id="product_tab_3">
          <h2 class="tab"><?php echo tep_not_null(TEXT_PRODUCTS_TAB_3_TITLE ) ? TEXT_PRODUCTS_TAB_3_TITLE : ' &nbsp; '; ?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_tab_3" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:auto">
            <tr>
              <td class="main" valign="top"><?php echo cre_clean_product_description($product_tab['products_tab_3']); ?></td>
            </tr>
          </table>
        </div>
<?php
}
    if(tep_not_null($product_tab['products_tab_4'])){
?>
        <div class="tab-page" id="product_tab_4">
          <h2 class="tab"><?php echo tep_not_null(TEXT_PRODUCTS_TAB_4_TITLE ) ? TEXT_PRODUCTS_TAB_4_TITLE : ' &nbsp; '; ?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_tab_4" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:auto">
            <tr>
              <td class="main" valign="top"><?php echo cre_clean_product_description($product_tab['products_tab_4']); ?></td>
            </tr>
          </table>
        </div>
<?php
    }
}
// Products Tabs Module EOF
?>
        <!-- Tab 2 -->
        <?php
        // Extra Products Fields are checked and presented
        // We need this instead of module, module can't hide tab if no records :-(
        $extra_fields_query = tep_db_query("SELECT pef.products_extra_fields_status as status, pef.products_extra_fields_name as name, ptf.products_extra_fields_value as value
                                            FROM ". TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS ." ptf,
                                                 ". TABLE_PRODUCTS_EXTRA_FIELDS ." pef
                                            WHERE ptf.products_id='".(int)$product_info['products_id']."'
                                              and ptf.products_extra_fields_value <> ''
                                              and ptf.products_extra_fields_id = pef.products_extra_fields_id
                                              and (pef.languages_id='0' or pef.languages_id='".$languages_id."')
                                            ORDER BY products_extra_fields_order");
        if ( tep_db_num_rows($extra_fields_query) > 0 ) {
        ?>
        <div class="tab-page" id="product_extra_fields">
          <h2 class="tab"><?php echo TEXT_TAB_PRODUCT_EXTRA_FIELDS;?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_extra_fields" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0">
            <?php
          while ($extra_fields = tep_db_fetch_array($extra_fields_query)) {
            if (! $extra_fields['status'])  continue;  // show only enabled extra field
            ?>
            <tr>
              <td class="main" valign="top"><b><?php echo $extra_fields['name']; ?>:&nbsp;</b></td>
              <td class="main" valign="top"><?php echo $extra_fields['value']; ?></td>
            </tr>
            <?php
          }
          ?>
          </table>
        </div>
        <?php
        }
        ?>
        <!-- Tab 2 eof -->
        <!-- Tab 3 -->
        <?php
        if (ULTIMATE_ADDITIONAL_IMAGES == 'enable') {
        if ( ($product_info['products_image_sm_1'] != '') || ($product_info['products_image_xl_1'] != '') ||
        ($product_info['products_image_sm_2'] != '') || ($product_info['products_image_xl_2'] != '') ||
        ($product_info['products_image_sm_3'] != '') || ($product_info['products_image_xl_3'] != '') ||
        ($product_info['products_image_sm_4'] != '') || ($product_info['products_image_xl_4'] != '') ||
        ($product_info['products_image_sm_5'] != '') || ($product_info['products_image_xl_5'] != '') ||
        ($product_info['products_image_sm_6'] != '') || ($product_info['products_image_xl_6'] != '') ) {
        ?>
        <div class="tab-page" id="product_extra_images">
          <h2 class="tab"><?php echo TEXT_TAB_PRODUCT_EXTRA_IMAGES;?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_extra_images" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:auto">
            <?php
          //include(DIR_WS_MODULES . 'additional_images.php');
         if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . 'additional_images.php')) {
            require(TEMPLATE_FS_CUSTOM_MODULES . 'additional_images.php');
        } else {
            require(DIR_WS_MODULES . 'additional_images.php');
        }
           ?>
          </table>
        </div>
        <?php
        } }
        ?>
        <!-- Tab 3 eof -->
        <!-- Tab 4 -->
        <?php
        $product_manufacturer_query = tep_db_query("select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "'), " . TABLE_PRODUCTS . " p  where p.products_id = '" . (int)$product_info['products_id'] . "' and p.manufacturers_id = m.manufacturers_id");
        if (tep_db_num_rows($product_manufacturer_query)) {
        ?>
        <div class="tab-page" id="product_manufacturer">
          <h2 class="tab"><?php echo TEXT_TAB_PRODUCT_MANUFACTURER;?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_manufacturer" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:100%">
            <tr>
              <td style="padding-left:8px;"><?php
        while ($manufacturer = tep_db_fetch_array($product_manufacturer_query)) {
        if (tep_not_null($manufacturer['manufacturers_image']))
        echo tep_image(DIR_WS_IMAGES . $manufacturer['manufacturers_image'], $manufacturer['manufacturers_name']) . '<br> <br>';
        echo '<strong>' . BOX_HEADING_MANUFACTURER_INFO . '</strong><br>';
        if (tep_not_null($manufacturer['manufacturers_url'])) {
          echo '<span class="main">&bull; <a href="' . tep_href_link(FILENAME_REDIRECT, 'action=manufacturer&manufacturers_id=' . $manufacturer['manufacturers_id']) . '" target="_blank">' . sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $manufacturer['manufacturers_name']) . '</a></span><br>';
        }
        echo '<span class="main">&bull; <a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $manufacturer['manufacturers_id']) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</span><br>';
        }
        ?>     </td>
            </tr>
          </table>
        </div>
        <?php
        }
        ?>
        <!-- Tab 4 eof -->
        <!-- Tab 5 -->
        <?php
        $reviews_query = tep_db_query("select r.reviews_id, rd.reviews_text as reviews_text, r.reviews_rating, r.date_added, p.products_id, pd.products_name, p.products_image, r.customers_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and rd.languages_id = '" . (int)$languages_id . "' order by rand(), r.reviews_id DESC limit " . PRODUCT_INFO_TAB_NUM_REVIEWS);
        if (tep_db_num_rows($reviews_query) > 0) {
            include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_REVIEWS);
        ?>
        <div class="tab-page" id="product_reviews">
          <h2 class="tab"><?php echo TEXT_TAB_PRODUCT_REVIEWS;?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_reviews" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:100%">
            <?php
           while ($reviews = tep_db_fetch_array($reviews_query)) {
           ?>
            <tr>
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main"><?php echo '<span class="smallText">' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) . '</span>'; ?></td>
                    <td class="smallText" align="right"><?php echo sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($reviews['date_added'])); ?></td>
                  </tr>
                  <tr>
                    <td colspan="2" valign="top" class="main"><?php echo tep_break_string(tep_output_string_protected($reviews['reviews_text']), 60, '-<br>') . ((strlen($reviews['reviews_text']) >= 100) ? '..' : '') . '<br><br><i>' . sprintf(TEXT_REVIEW_RATING, tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])), sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])) . '</i>'; ?></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td class="main"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
            <?php
        }
        ?>
            <tr>
              <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '5'); ?></td>
            </tr>
            <tr>
              <td class="main"><?php  if(tep_db_num_rows($reviews_query) == PRODUCT_INFO_TAB_NUM_REVIEWS ) { echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params() . $params) . '">' . tep_template_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS) . '</a>'; } ?></td>
            </tr>
          </table>
        </div>
        <?php
        }
        ?>
        <!-- Tab 5 eof -->
        <!-- Tab 6 -->
        <div class="tab-page" id="product_extra_info">
          <h2 class="tab"><?php echo TEXT_TAB_PRODUCT_EXTRA_INFO;?></h2>
          <script type="text/javascript">tp1.addTabPage( document.getElementById( "product_extra_info" ) );</script>
          <table cellpadding="0" cellspacing="0" border="0" style="width:100%">
            <tr>
              <td style="padding-left:8px;"><?php
                 if (tep_not_null($product_info['products_url'])) {
                     echo '<span class="main">' . sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT,  'action=url&amp;goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)) . '</span><br>';
                 }

                 echo '<span class="main"><strong>' . BOX_HEADING_REVIEWS . '</strong></span><br>';
                 $reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . (int)$_GET['products_id'] . "'");
                 $reviews = tep_db_fetch_array($reviews_query);
                 if ($reviews['count'] > 0) {
                   echo '<span class="main">' . TEXT_CURRENT_REVIEWS . ' ' . $reviews['count'];
                   echo '<span class="main"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . (int)$_GET['products_id']) . '">' . BOX_REVIEWS_READ_REVIEW . '</a></span><br>';
                 } else {
                   echo '<span class="main">' . BOX_REVIEWS_NO_REVIEWS . '</span><br>';
                 }
                 echo '<span class="main"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . (int)$_GET['products_id']) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a></span><br>';
                 echo '<br><br>';
                 echo '<span class="main"><strong>' . TAB_EXTRA_INFORMATIONS . '</strong></span><br>';
                 if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
                   echo '<span class="main">' . sprintf(TEXT_DATE_AVAILABLE, tep_date_long($product_info['products_date_available'])) . '</span>';
                 } else {
                   echo '<span class="main">' . sprintf(TEXT_DATE_ADDED, tep_date_long($product_info['products_date_added'])) . '</span>';
                 }
                 if (isset($_SESSION['customer_id'])) {
                   $check_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . (int)$_GET['products_id'] . "' and customers_id = '" . (int)$customer_id . "'");
                   $check = tep_db_fetch_array($check_query);
                   $notification_exists = (($check['count'] > 0) ? true : false);
                 } else {
                   $notification_exists = false;
                 }
                 if ($notification_exists == true) {
                   echo '<br><span class="main"><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=notify_remove', $request_type) . '">' . sprintf(BOX_NOTIFICATIONS_NOTIFY_REMOVE, tep_get_products_name((int)$_GET['products_id'])) .'</a></span><br>';
                 } else {
                   echo '<br><span class="main"><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=notify', $request_type) . '">' . sprintf(BOX_NOTIFICATIONS_NOTIFY, tep_get_products_name((int)$_GET['products_id'])) .'</a></span><br>';
                 }
                 echo '<span class="main"><a href="' . tep_href_link(FILENAME_TELL_A_FRIEND, 'products_id=' . (int)$_GET['products_id'], 'NONSSL') . '">' . BOX_TELL_A_FRIEND_TEXT . '</a></span><br>';
                 ?>
              </td>
            </tr>
          </table>
        </div>
        <!-- Tab 6 eof -->
      <?php
        //automoate loading tab files
        $tab_id = '';
        $tab_title = '';
        for ($i=0; $i<sizeof($tab_file_array); $i++) {
          $file = $tab_file_array[$i];
          $tab_id = str_replace('.php', '', $file);
          //if file exists include language file or generate the tab name with file name
          if (file_exists(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/product_info/tabs/' . $file)){
            include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/product_info/tabs/' . $file);
            $tab_title = TEXT_PRODUCT_INFO_TAB_TITLE;
          } else {
            $tab_name = explode('_', $tab_id);
            $tab_title = '';
            for ($n=0; $n<sizeof($tab_name); $n++){
              if ($n !=0 ){
                $tab_title .= ucfirst($tab_name[$n]) . ' ';
              }
            }
          }

          include_once($product_tabs_directory . $file);

        }
      ?>
      </div>
      <!-- Tabs eof -->
      <?php
       if ( (PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Multi') ||
            (PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Single' && PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'Drop Down')
          ) {
         if ($hide_add_to_cart == 'false' && group_hide_show_prices() == 'true') {
           if ($valid_to_checkout == true) {
             echo '<br><div style="float:right">' . tep_template_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, 'align="middle"') . '</div>' ;
           }
         }
       }
       ?>
    </td>
  </tr>
  <?php
    if (SHOW_PRICE_BREAK_TABLE == 'true') {
    ?>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <?php
      //include_once(DIR_WS_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE);
         if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE)) {
            require(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE);
        } else {
            require(DIR_WS_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE);
        }

    }
    if ( (USE_CACHE == 'true') && !SID) {
      echo tep_cache_also_purchased(3600);
//      include(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);
         if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_XSELL_PRODUCTS)) {
            require(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_XSELL_PRODUCTS);
        } else {
            require(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);
        }
    } else {
      //include(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS_BUYNOW);
         if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_XSELL_PRODUCTS_BUYNOW)) {
            require(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_XSELL_PRODUCTS_BUYNOW);
        } else {
            require(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS_BUYNOW);
        }

      //include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
         if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS)) {
            require(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
        } else {
            require(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
        }
    }
  }
  // product info page bottom
  echo $cre_RCI->get('productinfotabs', 'bottom');
  ?>  </table>
    </td>
  </tr>
</table>
</form>
<?php
// RCI code start
echo $cre_RCI->get('global', 'bottom');
?>