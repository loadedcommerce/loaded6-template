<?php
/*
  $Id: articles.php, v1.0 2003/12/04 12:00:00 ra Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// RCI code start
echo $cre_RCI->get('global', 'top');
echo $cre_RCI->get('articles', 'top');
// RCI code eof
// added for CDS CDpath support
$CDpath = (isset($_SESSION['CDpath'])) ? '&CDpath=' . $_SESSION['CDpath'] : '';

    if (isset($_SESSION['customer_id'])) {
      $customer_group_array = tep_get_customers_access_group($_SESSION['customer_id']);
    } else {
      $customer_group_array[] = 'G';
    }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="<?php echo CELLPADDING_SUB;?>">
  <?php
  if ($topic_depth == 'nested') {
	if(INSTALLED_VERSION_TYPE == 'B2B')
    {
    $group_access_nested_articles = tep_get_access_sql('t.products_group_access', $customer_group_array);
    }else{
		$group_access_nested_articles = '';
	}
    $topic_query = tep_db_query("select td.topics_name, td.topics_heading_title, td.topics_description from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.topics_id = '" . (int)$current_topic_id . "' and td.topics_id = '" . (int)$current_topic_id . "' and td.language_id = '" . (int)$languages_id . "' " . $group_access_nested_articles);
    $topic = tep_db_fetch_array($topic_query);
    ?>
    <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top" class="pageHeading">
            <?php
            if ( tep_not_null($topic['topics_heading_title']) ) {
              echo $topic['topics_heading_title'];
            } else {
              echo HEADING_TITLE;
            }
            ?>
          </td>
          <td valign="top" class="pageHeading" align="right"></td>
        </tr>
        <?php
        if ( tep_not_null($topic['topics_description']) ) { ?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td align="left" colspan="2" class="main"><?php echo $topic['topics_description']; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <?php
        }
        ?>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <?php
                  if (isset($tPath) && strpos('_', $tPath)) {
                    // check to see if there are deeper topics within the current topic
                    $topic_links = array_reverse($tPath_array);
                    for($i=0, $n=sizeof($topic_links); $i<$n; $i++) {
                      $topics_query = tep_db_query("select count(*) as total from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '" . (int)$topic_links[$i] . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)$languages_id . "'");
                      $topics = tep_db_fetch_array($topics_query);
                      if ($topics['total'] < 1) {
                        // do nothing, go through the loop
                      } else {
                        $topics_query = tep_db_query("select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '" . (int)$topic_links[$i] . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)$languages_id . "' order by sort_order, td.topics_name");
                        break; // we've found the deepest topic the customer is in
                      }
                    }
                  } else {
                    $topics_query = tep_db_query("select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '" . (int)$current_topic_id . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)$languages_id . "' order by sort_order, td.topics_name");
                  }
                  // needed for the new articles module shown below
                  $new_articles_topic_id = $current_topic_id;
                 ?>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td><?php /*include(DIR_WS_MODULES . FILENAME_NEW_ARTICLES); */ ?></td>
            </tr>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <?php
  } elseif ($topic_depth == 'articles' || isset($_GET['authors_id'])) {
    $group_access_author_articles = tep_get_access_sql('a.products_group_access', $customer_group_array);
    // Get the topic name and description from the database
    $topic_query = tep_db_query("select td.topics_name, td.topics_heading_title, td.topics_description from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.topics_id = '" . (int)$current_topic_id . "' and td.topics_id = '" . (int)$current_topic_id . "' and td.language_id = '" . (int)$languages_id . "'");
    $topic = tep_db_fetch_array($topic_query);
    // show the articles of a specified author
    if (isset($_GET['authors_id'])) {
      if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
        // We are asked to show only a specific topic
        $listing_sql = "SELECT a.articles_id, a.authors_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, au.authors_name, td.topics_name, a2t.topics_id
                        from " . TABLE_ARTICLES . " a,
                             " . TABLE_ARTICLES_DESCRIPTION . " ad,
                             " . TABLE_AUTHORS . " au,
                             " . TABLE_ARTICLES_TO_TOPICS . " a2t,
                             " . TABLE_TOPICS_DESCRIPTION . " td
                        WHERE a.articles_status = '1'
                          and (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now()))
                          and au.authors_id = '" . (int)$_GET['authors_id'] . "'
                          and a.authors_id = '" . (int)$_GET['authors_id'] . "'
                          and a.articles_id = a2t.articles_id
                          and ad.articles_id = a2t.articles_id
                          and a2t.topics_id = '" . (int)$_GET['filter_id'] . "'
                          and td.topics_id = '" . (int)$_GET['filter_id'] . "'
                          and ad.language_id = '" . (int)$languages_id . "'
                          and td.language_id = '" . (int)$languages_id . "' " . $group_access_author_articles . "
                        ORDER BY a.articles_date_added desc, ad.articles_name";
      } else {
        // We show them all
        $listing_sql = "SELECT a.articles_id, a.authors_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, au.authors_name, td.topics_name, a2t.topics_id
                        from " . TABLE_ARTICLES . " a,
                             " . TABLE_ARTICLES_DESCRIPTION . " ad,
                             " . TABLE_AUTHORS . " au,
                             " . TABLE_ARTICLES_TO_TOPICS . " a2t,
                             " . TABLE_TOPICS_DESCRIPTION . " td
                        WHERE a.articles_status = '1'
                          and (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now()))
                          and au.authors_id = '" . (int)$_GET['authors_id'] . "'
                          and a.authors_id = '" . (int)$_GET['authors_id'] . "'
                          and a.articles_id = a2t.articles_id
                          and ad.articles_id = a2t.articles_id
                          and a2t.topics_id = td.topics_id
                          and ad.language_id = '" . (int)$languages_id . "'
                          and td.language_id = '" . (int)$languages_id . "' " . $group_access_author_articles . "
                        ORDER BY a.articles_date_added desc, ad.articles_name";
      }
    } else {
      // show the articles in a given category
      if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
        // We are asked to show only specific catgeory
        $listing_sql = "SELECT a.articles_id, a.authors_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, au.authors_name, td.topics_name, a2t.topics_id
                        from " . TABLE_ARTICLES . " a,
                             " . TABLE_ARTICLES_DESCRIPTION . " ad,
                             " . TABLE_AUTHORS . " au,
                             " . TABLE_ARTICLES_TO_TOPICS . " a2t,
                             " . TABLE_TOPICS_DESCRIPTION . " td
                        WHERE a.articles_status = '1'
                          and (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now()))
                          and au.authors_id = '" . (int)$_GET['filter_id'] . "'
                          and a.authors_id = '" . (int)$_GET['filter_id'] . "'
                          and a.articles_id = a2t.articles_id
                          and ad.articles_id = a2t.articles_id
                          and a2t.topics_id = '" . (int)$current_topic_id . "'
                          and td.topics_id = '" . (int)$current_topic_id . "'
                          and ad.language_id = '" . (int)$languages_id . "'
                          and td.language_id = '" . (int)$languages_id . "' " . $group_access_author_articles . "
                        ORDER BY a.articles_date_added desc, ad.articles_name";
      } else {
        // We show them all
        $listing_sql = "SELECT a.articles_id, a.authors_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, au.authors_name, td.topics_name, a2t.topics_id
                        from " . TABLE_ARTICLES . " a,
                             " . TABLE_ARTICLES_DESCRIPTION . " ad,
                             " . TABLE_AUTHORS . " au,
                             " . TABLE_ARTICLES_TO_TOPICS . " a2t,
                             " . TABLE_TOPICS_DESCRIPTION . " td
                        WHERE a.articles_status = '1'
                          and (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now()))
                          and a.authors_id = au.authors_id
                          and a.articles_id = a2t.articles_id
                          and ad.articles_id = a2t.articles_id
                          and a2t.topics_id = '" . (int)$current_topic_id . "'
                          and td.topics_id = '" . (int)$current_topic_id . "'
                          and ad.language_id = '" . (int)$languages_id . "'
                          and td.language_id = '" . (int)$languages_id . "' " . $group_access_author_articles . "
                        ORDER BY a.articles_date_added desc, ad.articles_name";
      }
    }
    ?>
    <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <?php
        if ( tep_not_null($topic['topics_heading_title']) ) {
          $_heading_title = $topic['topics_heading_title'];
        } else {
          $_heading_title =  HEADING_TITLE;
        }
        if (isset($_GET['authors_id'])) {
          $author_query = tep_db_query("select au.authors_name, aui.authors_description, aui.authors_url from " . TABLE_AUTHORS . " au, " . TABLE_AUTHORS_INFO . " aui where au.authors_id = '" . (int)$_GET['authors_id'] . "' and au.authors_id = aui.authors_id and aui.languages_id = '" . (int)$languages_id . "'");
          $authors = tep_db_fetch_array($author_query);
          $author_name = $authors['authors_name'];
          $authors_description = $authors['authors_description'];
          $authors_url = $authors['authors_url'];
          $_author_name =  TEXT_ARTICLES_BY . $author_name;
        }

        if (SHOW_HEADING_TITLE_ORIGINAL=='yes') {
          $header_text = $_heading_title .'&nbsp;&nbsp;' . $_author_name;
          ?>
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading" valign="top"><?php echo $_heading_title; ?></td>
                <td class="pageHeading" align="right" valign="top"><?php echo $_author_name; ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <?php
        } else {
          $header_text =  $_heading_title .'&nbsp;&nbsp;' . $_author_name;
        }
        ?>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top" align="left" class="pageHeading"></td>
              <?php
              // optional Article List Filter
              if (ARTICLE_LIST_FILTER) {
                if (isset($_GET['authors_id'])) {
                  $filterlist_sql = "select distinct t.topics_id as id, td.topics_name as name from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t, " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where a.articles_status = '1' and a.articles_id = a2t.articles_id and a2t.topics_id = t.topics_id and a2t.topics_id = td.topics_id and td.language_id = '" . (int)$languages_id . "' and a.authors_id = '" . (int)$_GET['authors_id'] . "' order by td.topics_name";
                } else {
                  $filterlist_sql= "select distinct au.authors_id as id, au.authors_name as name from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t, " . TABLE_AUTHORS . " au where a.articles_status = '1' and a.authors_id = au.authors_id and a.articles_id = a2t.articles_id and a2t.topics_id = '" . (int)$current_topic_id . "' order by au.authors_name";
                }
                $filterlist_query = tep_db_query($filterlist_sql);
                if (tep_db_num_rows($filterlist_query) > 1) {
                  echo '<td align="right" class="main">' . tep_draw_form('filter', FILENAME_ARTICLES, 'get') . TEXT_SHOW . '&nbsp;';
                  if (isset($_GET['authors_id'])) {
                    echo tep_draw_hidden_field('authors_id', $_GET['authors_id']);
                    $options = array(array('id' => '', 'text' => TEXT_ALL_TOPICS));
                  } else {
                    echo tep_draw_hidden_field('tPath', $tPath);
                    $options = array(array('id' => '', 'text' => TEXT_ALL_AUTHORS));
                  }
                  echo tep_draw_hidden_field('sort', $_GET['sort']);
                  while ($filterlist = tep_db_fetch_array($filterlist_query)) {
                    $options[] = array('id' => $filterlist['id'], 'text' => $filterlist['name']);
                  }
                  echo tep_draw_pull_down_menu('filter_id', $options, (isset($_GET['filter_id']) ? $_GET['filter_id'] : ''), 'onchange="this.form.submit()"');
                  echo '</form></td>' . "\n";
                }
              }
              ?>
            </tr>
          </table></td>
        </tr>
        <?php
        if ( tep_not_null($topic['topics_description']) ) {
          if (MAIN_TABLE_BORDER == 'yes'){
            table_image_border_top(false, false, $header_text);
          }
          ?>
          <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main"><?php echo $topic['topics_description']; ?></td>
              </tr>
              <?php
              if (tep_not_null($authors_description)) {
                ?>
                <tr>
                  <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><?php echo $authors_description; ?></td>
                <tr>
                <?php
              }
              if (tep_not_null($authors_url)) {
                ?>
                <tr>
                  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><?php echo sprintf(TEXT_MORE_INFORMATION, $authors_url); ?></td>
                </tr>
                <?php
              }
              ?>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          <?php
          if (MAIN_TABLE_BORDER == 'yes'){
            table_image_border_bottom();
          }
        }
        ?>
      </table></td>
    </tr>
    <?php
    if (MAIN_TABLE_BORDER == 'yes'){
      table_image_border_top(false, false, '');
    }
    ?>
    <tr>
      <td class="main"><?php
      //include(DIR_WS_MODULES . FILENAME_ARTICLE_LISTING);
         if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_ARTICLE_LISTING)) {
            require(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_ARTICLE_LISTING);
        } else {
            require(DIR_WS_MODULES . FILENAME_ARTICLE_LISTING);
        }
      ?></td>
    </tr>
    <?php
    if (MAIN_TABLE_BORDER == 'yes'){
      table_image_border_bottom();
    }
  } else { // default page
    if (SHOW_HEADING_TITLE_ORIGINAL == 'yes') {
      $header_text = '&nbsp;'
      ?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><h1><?php echo HEADING_TITLE; ?></h1></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_reviews_new.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <?php
    } else {
      $header_text =  HEADING_TITLE;
    }
    if (MAIN_TABLE_BORDER == 'yes'){
      table_image_border_top(false, false, $header_text);
    }
    $expected_query = tep_db_query("select a.articles_id, a.articles_date_added, a.articles_date_available as date_expected, ad.articles_name, ad.articles_head_desc_tag, au.authors_id, au.authors_name, td.topics_id, td.topics_name from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t, " . TABLE_TOPICS_DESCRIPTION . " td, " . TABLE_AUTHORS . " au, " . TABLE_ARTICLES_DESCRIPTION . " ad where to_days(a.articles_date_available) > to_days(now()) and a.articles_id = a2t.articles_id and a2t.topics_id = td.topics_id and a.authors_id = au.authors_id and a.articles_status = '1' and a.articles_id = ad.articles_id and ad.language_id = '" . (int)$languages_id . "' and td.language_id = '" . (int)$languages_id . "' order by date_expected limit " . MAX_DISPLAY_UPCOMING_ARTICLES);
    if (tep_db_num_rows($expected_query) > 0) {
      ?>
      <tr>
        <td>
<?php
        // include(DIR_WS_MODULES . FILENAME_ARTICLES_UPCOMING);
        if ( file_exists(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_ARTICLES_UPCOMING)) {
          require(TEMPLATE_FS_CUSTOM_MODULES . FILENAME_ARTICLES_UPCOMING);
        } else {
          require(DIR_WS_MODULES . FILENAME_ARTICLES_UPCOMING);
        }
?>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php
    }
    ?>
    <tr>
      <td class="pageHeading main "><?php echo '<b>' . TEXT_CURRENT_ARTICLES . '</b>'; ?></td>
    </tr>
    <?php
    $group_access_current_articles = tep_get_access_sql('a.products_group_access', $customer_group_array);
    $articles_all_array = array();
    $articles_all_query_raw = "SELECT a.articles_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, au.authors_id, au.authors_name, td.topics_id, td.topics_name
                               from " . TABLE_ARTICLES . " a,
                                    " . TABLE_AUTHORS . " au,
                                    " . TABLE_ARTICLES_DESCRIPTION . " ad,
                                    " . TABLE_ARTICLES_TO_TOPICS . " a2t,
                                    " . TABLE_TOPICS_DESCRIPTION . " td
                               WHERE a.articles_status = '1'
                                 and (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now()))
                                 and a.authors_id = au.authors_id
                                 and a.articles_id = a2t.articles_id
                                 and a.articles_id = ad.articles_id
                                 and a2t.topics_id = td.topics_id
                                 and ad.language_id = '" . (int)$languages_id . "'
                                 and td.language_id = '" . (int)$languages_id . "' " . $group_access_current_articles . "
                              ORDER BY a.articles_date_added desc, ad.articles_name";

    $articles_all_split = new splitPageResults($articles_all_query_raw, MAX_ARTICLES_PER_PAGE);
    if (($articles_all_split->number_of_rows > 0) && ((ARTICLE_PREV_NEXT_BAR_LOCATION == 'top') || (ARTICLE_PREV_NEXT_BAR_LOCATION == 'both'))) {
      ?>
 <div class="product-listing-module-pagination margin-bottom">
        <div class="pull-left large-margin-bottom page-results"><?php echo $articles_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_ARTICLES); ?></div>
        <div class="pull-right large-margin-bottom no-margin-top">
          <ul class="pagination no-margin-top no-margin-bottom">
           <?php echo  $articles_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>

          </ul>
        </div>
     </div><div class="clear-both"></div>
      <?php
       }
      ?>
    <tr>
      <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <?php
        if ($articles_all_split->number_of_rows > 0) {
          $articles_all_query = tep_db_query($articles_all_split->sql_query);
          ?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <?php
          while ($articles_all = tep_db_fetch_array($articles_all_query)) {
            ?>
  <div class=" bor_rad shadow">
    <?php
      echo  '<div class="box-header small-margin-bottom small-margin-left" style="text-align:left;float:left;padding-right:10px;"><a href="' . tep_href_link(FILENAME_ARTICLE_INFO, 'articles_id=' . $articles_all['articles_id'] . $CDpath) . '"><b>' . $articles_all['articles_name'] . '</b></a> </div>';
	     echo '<div>' .TEXT_BY .   '<a href="' . tep_href_link(FILENAME_ARTICLES, 'authors_id=' . $articles_all['authors_id'] . $CDpath) . '"> ' . $articles_all['authors_name'] . '</a>';
     ?>
       <div style="float:right;">
            <?php
              if (DISPLAY_TOPIC_ARTICLE_LISTING == 'true' && tep_not_null($articles_all['topics_name'])) {
                ?>
                <?php echo TEXT_TOPIC . '&nbsp;<a href="' . tep_href_link(FILENAME_ARTICLES, 'tPath=' . $articles_all['topics_id'] . $CDpath) . '">' . $articles_all['topics_name'] . '</a>'; ?></td>
                <?php
              }
              ?>
              </div>
       </div>
		<div style="clear:both;margin-left:20px;padding-top:5px;">
			  <?php echo clean_html_comments(substr($articles_all['articles_head_desc_tag'],0, MAX_ARTICLE_ABSTRACT_LENGTH)) . ((strlen($articles_all['articles_head_desc_tag']) >= MAX_ARTICLE_ABSTRACT_LENGTH) ? '...' : ''); ?>
	   </div>
	<div style="clear:both;margin-left:20px;">
	      <?php      if (DISPLAY_DATE_ADDED_ARTICLE_LISTING == 'true') {
	              ?>
	              <div>
			 		<?php echo TEXT_DATE_ADDED . ' ' . tep_date_long($articles_all['articles_date_added']); ?>   </div>

	              <?php
	            }
			?>

  </div>
 </div>

            <?php

          } // End of listing loop
        } else {
          ?>
          <tr>
            <td class="main"><?php echo TEXT_NO_ARTICLES; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <?php
        }
        ?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
     <td>
  <div class="clearfix"></div>
  <div class="content-product-listing-div">

<?php if (($articles_all_split->number_of_rows > 0) && ((ARTICLE_PREV_NEXT_BAR_LOCATION == 'bottom') || (ARTICLE_PREV_NEXT_BAR_LOCATION == 'both'))) {?>
      <div class="product-listing-module-pagination margin-bottom">
        <div class="pull-left large-margin-bottom page-results"><?php echo $articles_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_ARTICLES); ?></div>
        <div class="pull-right large-margin-bottom no-margin-top">
          <ul class="pagination no-margin-top no-margin-bottom">
           <?php echo  $articles_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>

          </ul>
        </div>
      </div><div class="clear-both"></div>


<?php
  }
?>
</div>
    </tr>
     </td>

     <?php

    // RCI code start
    echo $cre_RCI->get('articles', 'menu');
    // RCI code eof
    if (MAIN_TABLE_BORDER == 'yes'){
      table_image_border_bottom();
    }
  }
?>
</table>
<?php
// RCI code start
echo $cre_RCI->get('articles', 'bottom');
echo $cre_RCI->get('global', 'bottom');
// RCI code eof
?>

