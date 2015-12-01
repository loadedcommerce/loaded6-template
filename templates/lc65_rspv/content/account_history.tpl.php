<?php
/*
  $Id: sss_accounthistory_.php,v 1.0.0.0 2008/05/13 13:41:11 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
// RCI code start
echo $cre_RCI->get('global', 'top');
echo $cre_RCI->get('accounthistory', 'top');
?>
<div class="row">
  <div class="col-sm-12 col-lg-12">
  <div class="">
    <h1 class="no-margin-top"><?php echo HEADING_TITLE; ?></h1>



        <table id="content-account-history-table" class="table table-striped table-hover">
          <thead>
            <tr>
              <th><?php echo SMALL_IMAGE_BUTTON_VIEW; ?></th>
              <th><?php echo MENU_TEXT_NUM; ?></th>
              <th><?php echo MENU_TEXT_SHIP; ?></th>
              <th><?php echo MENU_TEXT_DATE; ?></th>
              <th><?php echo MENU_TEXT_STATUS; ?></th>
              <th><?php echo MENU_TEXT_ITM; ?></th>
              <th><?php echo MENU_TEXT_GT; ?></th>
            </tr>
          </thead>
          <tbody>


      <?php
      $orders_total = tep_count_customer_orders();
      if ($orders_total > 0) {
        $history_query_raw = "select o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$_SESSION['customer_id'] . "' and o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' order by orders_id DESC";
        $history_split = new splitPageResults($history_query_raw, MAX_DISPLAY_ORDER_HISTORY);
        $history_query = tep_db_query($history_split->sql_query);
        while ($history = tep_db_fetch_array($history_query)) {
          $products_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$history['orders_id'] . "'");
          $products = tep_db_fetch_array($products_query);
          if (tep_not_null($history['delivery_name'])) {
            $order_type = TEXT_ORDER_SHIPPED_TO;
            $order_name = $history['delivery_name'];
          } else {
            $order_type = TEXT_ORDER_BILLED_TO;
            $order_name = $history['billing_name'];
          }
          ?>
          <tr>
             <td><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&amp;' : '') . 'order_id=' . $history['orders_id'], 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'search.png'). '</a>'; ?></td>
             <td><?php echo  $history['orders_id']; ?></td>
             <td><?php echo $history['delivery_name']; ?></td>
             <td><?php echo '<b>'  . tep_date_long($history['date_purchased']) . '</b>' ; ?></td>
             <td><?php echo '<b>'  . $history['orders_status_name'].'</b> '; ?></td>
             <td><?php echo $products['count']; ?></td>
             <td><?php echo $history['order_total']; ?></td>


          </tr>




 <?php
        }
      } else {
        ?>
		<?php echo TEXT_NO_PURCHASES; ?>
        <?php
      }
      ?> </tbody></table>


  <?php
  if (MAIN_TABLE_BORDER == 'yes'){
    table_image_border_bottom();
  }
  // RCI accounthistory menu
  echo $cre_RCI->get('accounthistory', 'middle');
  if ($orders_total > 0) {
    ?>
    <?php
  }
  ?>
</div>
    <div class="btn-set small-margin-top clearfix">
      <a href ="<?php echo tep_href_link(FILENAME_DEFAULT,'', 'SSL'); ?>"><button class="pull-right btn btn-lg btn-primary" type="submit"><?php echo MENU_TEXT_GO_SHOPPING; ?></button></a>
      <a href ="<?php echo tep_href_link(FILENAME_ACCOUNT,'', 'SSL'); ?>"><button class="pull-left btn btn-lg btn-default" type="submit"><?php echo MENU_TEXT_BACK; ?></button></a>
    </div>

 </div>
</div>
<?php
// RCI bottom
echo $cre_RCI->get('accounthistory', 'bottom');
echo $cre_RCI->get('global', 'bottom');
?>
<?php
/*
  $Id: sss_accounthistory_.php,v 1.0.0.0 2008/05/13 13:41:11 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
// RCI code start
/*echo $cre_RCI->get('global', 'top');
echo $cre_RCI->get('accounthistory', 'top');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="<?php echo CELLPADDING_SUB; ?>">
  <?php
  if (SHOW_HEADING_TITLE_ORIGINAL == 'yes') {
    $header_text = '&nbsp;'
    ?>
    <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
          <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_history.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
        </tr>
      </table></td>
    </tr>
    <?php
  } else {
    $header_text = HEADING_TITLE;
  }
  if (MAIN_TABLE_BORDER == 'yes'){
    table_image_border_top(false, false, $header_text);
  }
  ?>
  <tr>
    <td>
      <?php
      $orders_total = tep_count_customer_orders();
      if ($orders_total > 0) {
        $history_query_raw = "select o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$_SESSION['customer_id'] . "' and o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' order by orders_id DESC";
        $history_split = new splitPageResults($history_query_raw, MAX_DISPLAY_ORDER_HISTORY);
        $history_query = tep_db_query($history_split->sql_query);
        while ($history = tep_db_fetch_array($history_query)) {
          $products_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$history['orders_id'] . "'");
          $products = tep_db_fetch_array($products_query);
          if (tep_not_null($history['delivery_name'])) {
            $order_type = TEXT_ORDER_SHIPPED_TO;
            $order_name = $history['delivery_name'];
          } else {
            $order_type = TEXT_ORDER_BILLED_TO;
            $order_name = $history['billing_name'];
          }
          ?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main"><?php echo '<b>' . TEXT_ORDER_NUMBER . '</b> ' . $history['orders_id']; ?></td>
              <td class="main" align="right"><?php echo '<b>' . TEXT_ORDER_STATUS . '</b> ' . $history['orders_status_name']; ?></td>
            </tr>
          </table>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
            <tr class="infoBoxContents">
              <td><table border="0" width="100%" cellspacing="2" cellpadding="4">
                <tr>
                  <td class="main" width="50%" valign="top"><?php echo '<b>' . TEXT_ORDER_DATE . '</b> ' . tep_date_long($history['date_purchased']) . '<br><b>' . $order_type . '</b> ' . tep_output_string_protected($order_name); ?></td>
                  <td class="main" width="30%" valign="top"><?php echo '<b>' . TEXT_ORDER_PRODUCTS . '</b> ' . $products['count'] . '<br><b>' . TEXT_ORDER_COST . '</b> ' . strip_tags($history['order_total']); ?></td>
                  <td class="main" width="20%"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&amp;' : '') . 'order_id=' . $history['orders_id'], 'SSL') . '">' . tep_template_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW) . '</a>'; ?></td>
                </tr>
              </table></td>
            </tr>
          </table>
          <?php
        }
      } else {
        ?>
        <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="2" cellpadding="4">
              <tr>
                <td class="main"><?php echo TEXT_NO_PURCHASES; ?></td>
              </tr>
            </table></td>
          </tr>
        </table>
        <?php
      }
      ?>
    </td>
  </tr>
  <?php
  if (MAIN_TABLE_BORDER == 'yes'){
    table_image_border_bottom();
  }
  // RCI accounthistory menu
  echo $cre_RCI->get('accounthistory', 'middle');
  if ($orders_total > 0) {
    ?>
    <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td class="smallText" valign="top"><?php echo $history_split->display_count(TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
          <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $history_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
        </tr>
      </table></td>
    </tr>
    <?php
  }
  ?>
  <tr>
    <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
      <tr class="infoBoxContents">
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_template_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php
// RCI bottom
echo $cre_RCI->get('accounthistory', 'bottom');
echo $cre_RCI->get('global', 'bottom');
   */?>