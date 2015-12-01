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
echo $cre_RCI->get('productinfo', 'top');
// RCI code eof
$product_subproducts_check = tep_has_product_subproducts((int)$_GET['products_id']);
echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action', 'products_id', 'id')) . 'action=add_product' . '&' . $params), 'post', 'enctype="multipart/form-data" onsubmit = "return func_chk_subproducts('.(($product_subproducts_check)?1:0).');"'); ?>
<div class="row">
<?php
  if ($messageStack->size('cart_quantity') > 0) {
?>
      <div class="message-stack-container alert alert-success">
        <?php echo $messageStack->output('cart_quantity'); ?>
      </div>
<?php
  }

  // added for CDS CDpath support
  $params = (isset($_SESSION['CDpath'])) ? 'CDpath=' . $_SESSION['CDpath'] : '';
  if ($product_check['total'] < 1) {
    ?>
<div class="col-sm-12 col-lg-12">

      <?php  echo TEXT_PRODUCT_NOT_FOUND; ?>
    </div>
     <div class="col-sm-12 col-lg-12 text-right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, $params) . '">' . tep_template_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></div>
    <?php
  } else {
    $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image, p.products_image_med, p.products_image_lrg, p.products_image_sm_1, p.products_image_xl_1, p.products_image_sm_2, p.products_image_xl_2, p.products_image_sm_3, p.products_image_xl_3, p.products_image_sm_4, p.products_image_xl_4, p.products_image_sm_5, p.products_image_xl_5, p.products_image_sm_6, p.products_image_xl_6, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
    $product_info = tep_db_fetch_array($product_info_query);
    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$product_info['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
    /*if (tep_not_null($product_info['products_model'])) {
      $products_name = '<h1>' . $product_info['products_name'] . '</h1>&nbsp;<span class="smallText">[' . $product_info['products_model'] . ']</span>';
    } else {
      $products_name = '<h1>' . $product_info['products_name'] . '</h1>';
    }*/
    $products_name = '<h1>' . $product_info['products_name'] . '</h1>';
    if ($product_has_sub > '0'){ // if product has sub products
      $products_price ='';// if you like to show some thing in place of price add here
    } else {
      $pf->loadProduct($product_info['products_id'],$languages_id);
      $products_price = $pf->getPriceStringShort();
    } // end sub product check
    if (SHOW_HEADING_TITLE_ORIGINAL=='yes') {
      $header_text = '';
      ?>
      <div class="col-sm-12 col-lg-12">
      	<h1 class="no-padding-top"><?php echo $products_name; ?></h1>
      <?php
        // RCI code start
        echo $cre_RCI->get('productinfo', 'underpriceheading');
        // RCI code eof
      ?>
<hr>
</div>
<div class="col-sm-5 col-lg-5">
              <?php
                if (tep_not_null($product_info['products_image']) || tep_not_null($product_info['products_image_med'])) {
                  // BOF MaxiDVD: Modified For Ultimate Images Pack!
                  if ($product_info['products_image_med']!='') {
                      $new_image = $product_info['products_image_med'];
                      $image_width = MEDIUM_IMAGE_WIDTH;
                      $image_height = MEDIUM_IMAGE_HEIGHT;
                   } else {
                      $new_image = $product_info['products_image'];
                      $image_width = MEDIUM_IMAGE_WIDTH;
                      $image_height = MEDIUM_IMAGE_HEIGHT;
                   }
                    echo '<a data-toggle="modal" href="#popup-image-modal" class="">' . tep_image(DIR_WS_IMAGES . $new_image, $product_info['products_name'], $image_width, $image_height, 'class="img-responsive"') . '</a><br><p class="text-center no-margin-top no-margin-bottom"><a data-toggle="modal" href="#popup-image-modal" class="btn normal">Click To Enlarge</a></p>    <div class="modal fade" id="popup-image-modal">
							  <div class="modal-dialog">
								<div class="modal-content">
								  <div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title">'.$product_info['products_name'] .'</h4>
								  </div>
								  <div class="modal-body">'.tep_image(DIR_WS_IMAGES . $new_image, $product_info['products_name'], $image_width, $image_height, 'class="img-responsive"').'
								  </div>
								  <div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">close</button>
								  </div>
								</div>
							  </div>
							</div>
						';

                   // EOF MaxiDVD: Modified For Ultimate Images Pack!
                   if (isset($_SESSION['affiliate_id'])) {
                     echo '<br><br><a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_BUILD, 'individual_banner_id=' . $product_info['products_id'] . '&' . $params) .'" target="_self">' . tep_template_image_button('button_affiliate_build_a_link.gif', LINK_ALT) . ' </a>';
                   }
                 }  // end if image
               ?>

</div>
		<div class="col-sm-7 col-lg-7">
				   <?php if ($product_has_sub > '0') { } else { ?>
					<div class="col-sm-6 col-lg-6"><b><?php echo TEXT_PRICE;?></b></div>
					<div class="col-sm-6 col-lg-6"><?php echo $products_price; ?></div>
					<div class="clearfix"></div>
				  <?php } if (tep_not_null($product_info['products_model'])) { ?>

					 <div class="col-sm-6 col-lg-6"><b><?php echo TEXT_MODEL;?></b></div>
					 <div class="col-sm-6 col-lg-6"><b><?php echo $product_info['products_model']; ?></b></div>
					<div class="clearfix"></div>
				  <?php }
				  if ($product_info['manufacturers_id'] != 0) {
				  ?>
					 <div class="col-sm-6 col-lg-6"><b><?php echo TEXT_MANUFACTURER;?></b></div>
					 <div class="col-sm-6 col-lg-6"><b><?php echo tep_get_manufacturers_name($product_info['manufacturers_id']); ?></b></div>
					<div class="clearfix"></div>
				  <?php } ?>
                  <?php
                  $hide_add_to_cart = hide_add_to_cart();
                    if ($hide_add_to_cart == 'false' && group_hide_show_prices() == 'true') {
                      if ($product_has_sub > '0') {
                      } else {
                  ?>
                     <label class="content-products-info-qty-label"><?php echo TEXT_ENTER_QUANTITY,':'; ?></label>
                    <?php echo  tep_draw_input_field('cart_quantity', '1', 'size="6" maxlength="6" id="Qty1" onkeyup="document.getElementById(\'Qty2\').value = document.getElementById(\'Qty1\').value;"  ');?>

                  <?php
                      }
                    }
                  ?>
<br>
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

</div>
<div class="clearfix"></div>
<div class="col-sm-12 col-lg-12">
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
                        if ($valid_to_checkout == true && !$product_subproducts_check) {
                         // echo ((PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Multi' || (PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Single' && PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'Drop Down') || $product_has_sub == 0) ? tep_template_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART,'align="middle"') : '');
                        }
                      }
                    ?>
</div>
<div class="col-sm-6 col-lg-6 hide-on-mobile">
</div>
<div class="col-sm-6 col-lg-6">
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

</div>
<div class="col-sm-6 col-lg-6 hide-on-mobile">
</div>
<div class="col-sm-6 col-lg-6 small-padding-top">
<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params() . $params) . '">' . tep_template_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS,'align="middle"') . '</a>'; ?>
</div>

                      <div id="pointsRewards" class="col-sm-12 col-lg-12">
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
     <?php
    } else {
      $header_text =  $products_name .'<div>' . $products_price .'</div>';
    }
?>
  <div  class="col-sm-12 col-lg-12">
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <?php
            echo '<tr><td class="main" valign="top"><p>' .  cre_clean_product_description($product_info['products_description']) . '</p>';
            echo tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td></tr>';
            if (defined('MODULE_ADDONS_TABS_STATUS') && MODULE_ADDONS_TABS_STATUS == 'True') {
                $product_tabs_query = tep_db_query("select products_tab_2, products_tab_3, products_tab_4 from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_info['products_id'] . "'");
                $product_tab = tep_db_fetch_array($product_tabs_query);
                if(tep_not_null($product_tab['products_tab_2'])){
                    echo '<tr><td class="main" valign="top">' . (tep_not_null(TEXT_PRODUCTS_TAB_2_TITLE ) ? '<strong>' . TEXT_PRODUCTS_TAB_2_TITLE  . '</strong>': '') . '<p>' .  cre_clean_product_description($product_tab['products_tab_2']) . '</p></td></tr>';
                }
                if(tep_not_null($product_tab['products_tab_3'])){
                    echo '<tr><td class="main" valign="top">' . (tep_not_null(TEXT_PRODUCTS_TAB_3_TITLE ) ? '<strong>' . TEXT_PRODUCTS_TAB_3_TITLE  . '</strong>': '') . '<p>' .  cre_clean_product_description($product_tab['products_tab_3']) . '</p></td></tr>';
                }
                if(tep_not_null($product_tab['products_tab_4'])){
                    echo '<tr><td class="main" valign="top">' . (tep_not_null(TEXT_PRODUCTS_TAB_4_TITLE ) ? '<strong>' . TEXT_PRODUCTS_TAB_4_TITLE  . '</strong>': '') . '<p>' .  cre_clean_product_description($product_tab['products_tab_4']) . '</p></td></tr>';
                }
            }

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
      </table>
</div>
  <div class="col-sm-12 col-lg-12">
    <?php
    if (ULTIMATE_ADDITIONAL_IMAGES == 'enable') {
         if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . 'additional_images.php')) {
            require(TEMPLATE_FS_CUSTOM_MODULES . 'additional_images.php');
        } else {
            require(DIR_WS_MODULES . 'additional_images.php');
        }
    }
    $reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_info['products_id'] . "'");
    $reviews = tep_db_fetch_array($reviews_query);
    if ($reviews['count'] > 0) {
      ?>
        <div><?php echo TEXT_CURRENT_REVIEWS . ' ' . $reviews['count']; ?></div>
      <?php
    }

    include(DIR_WS_MODULES . 'product_info/extra_products_fields.php');

    if (tep_not_null($product_info['products_url'])) {
      ?>
         <div><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&amp;goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)); ?></div>
      <?php
    }
    if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
      ?>
        <div><?php echo sprintf(TEXT_DATE_AVAILABLE, tep_date_long($product_info['products_date_available'])); ?></div>
      <?php
    } else {
      ?>
		 <div><?php echo sprintf(TEXT_DATE_ADDED, tep_date_long($product_info['products_date_added'])); ?></div>
      <?php
    }
    // sub product start
    if (STOCK_ALLOW_CHECKOUT =='false') {
      $allowcriteria = "";
    }

    $select_order_by = '';
    switch (defined('SUB_PRODUCTS_SORT_ORDER')? strtoupper(SUB_PRODUCTS_SORT_ORDER) : '') {
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
      if (defined('PRODUCT_INFO_SUB_PRODUCT_DISPLAY') && PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'In Listing') {
        include(DIR_WS_MODULES . 'product_info/sub_products_listing.php');
      } else if ( PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'Drop Down'){
        include(DIR_WS_MODULES . 'product_info/sub_products_dropdown.php');
      }
    }
    if ((PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Multi') ||
        (PRODUCT_INFO_SUB_PRODUCT_PURCHASE == 'Single' && PRODUCT_INFO_SUB_PRODUCT_DISPLAY == 'Drop Down') ||
        ($product_has_sub == 0)
      ){
      if ($hide_add_to_cart == 'false' && group_hide_show_prices() == 'true') {
        if ($valid_to_checkout == true) {
          echo '<div class="text-right small-padding-top">' . tep_template_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, 'align="middle"') . '</div>' ;
        }
      }
    }?>
    </div>
<div class="col-sm-12 col-lg-12">
    <?php
    // sub product_eof
    if (SHOW_PRICE_BREAK_TABLE == 'true') {
      //include(DIR_WS_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE);
      if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE)) {
        require(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE);
      } else {
        require(DIR_WS_MODULES . FILENAME_PRODUCT_QUANTITY_TABLE);
      }
    }
    if ( (USE_CACHE == 'true') && !SID) {
      echo tep_cache_also_purchased(3600);
      // include(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);
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
  echo $cre_RCI->get('productinfo', 'bottom');
 ?>
</div>
 <div class="clearfix"></div>
</div>
</div>

<?php
    if(PRODUCT_INFO_SUB_PRODUCT_DISPLAY != 'Single Purchase'){
?>
</form>
<?php
    }
// RCI code start
echo $cre_RCI->get('global', 'bottom');
?>
