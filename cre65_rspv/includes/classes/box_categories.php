<?php
/*
  $Id: box_categories.php $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Released under the GNU General Public License

  Proivdes the logic to populate the info box by the same name
*/
class box_categories_rspv {
  public $row_count = 0;
  public $categories_string = '';
  private $tree = array();

  public function __construct() {
    global $cPath, $cPath_array, $languages_id;
    
    $categories_query_raw = "SELECT c.categories_id, cd.categories_name, c.parent_id
                             FROM " . TABLE_CATEGORIES . " c,
                                  " . TABLE_CATEGORIES_DESCRIPTION . " cd
                             WHERE c.parent_id = 0
                               and c.categories_id = cd.categories_id
                               and cd.language_id= " . (int)$languages_id . "
                             ORDER BY sort_order, cd.categories_name";
    $categories_query = tep_db_query($categories_query_raw);
    
    while ($categories = tep_db_fetch_array($categories_query)) {
      $this->tree[$categories['categories_id']] = array('name' => $categories['categories_name'],
                                                  'parent' => $categories['parent_id'],
                                                  'level' => 0,
                                                  'path' => $categories['categories_id'],
                                                  'next_id' => false);
      if (isset($parent_id)) {
        $this->tree[$parent_id]['next_id'] = $categories['categories_id'];
      }
      $parent_id = $categories['categories_id'];
      if ( ! isset($first_element)) {
        $first_element = $categories['categories_id'];
      }
    }
    
    if (tep_not_null($cPath)) {
      $new_path = '';
      reset($cPath_array);
      while (list($key, $value) = each($cPath_array)) {
        unset($parent_id);
        unset($first_id);
        $categories_query = tep_db_query("SELECT c.categories_id, cd.categories_name, c.parent_id
                                          FROM " . TABLE_CATEGORIES . " c,
                                               " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                          WHERE c.parent_id = " . (int)$value . "
                                            and c.categories_id = cd.categories_id
                                            and cd.language_id = " . (int)$languages_id . "
                                          ORDER BY sort_order, cd.categories_name");
        if (tep_db_num_rows($categories_query)) {
          $new_path .= $value;
          while ($row = tep_db_fetch_array($categories_query)) {
            $this->tree[$row['categories_id']] = array('name' => $row['categories_name'],
                                                 'parent' => $row['parent_id'],
                                                 'level' => $key+1,
                                                 'path' => $new_path . '_' . $row['categories_id'],
                                                 'next_id' => false);
            if (isset($parent_id)) {
              $this->tree[$parent_id]['next_id'] = $row['categories_id'];
            }
            $parent_id = $row['categories_id'];
            if ( ! isset($first_id)) {
              $first_id = $row['categories_id'];
            }
            $last_id = $row['categories_id'];
          }
          $this->tree[$last_id]['next_id'] = $this->tree[$value]['next_id'];
          $this->tree[$value]['next_id'] = $first_id;
          $new_path .= '_';
        } else {
          break;
        }
      }
    }
    $this->build_categories_string($first_element);
  } // end of __construct

  private function build_categories_string($counter) {
    global $cPath_array, $column_location;
    // end background variables
    $this->categories_string .= '<ul class="box-information_pages-ul list-unstyled list-indent-large">';
    $style_padding = "0";
    for ($i=0; $i<$this->tree[$counter]['level']; $i++) {
      //$this->categories_string .= "&nbsp; &nbsp;";
      $style_padding += 10;
    }
    $css_indent = '';
    if($style_padding > 0)
    	$css_indent = ' style="padding-left:'.$style_padding.'px"';
    $this->categories_string .= '<li'. $css_indent .'>';
    $this->categories_string .= '<a href="';
    if ($this->tree[$counter]['parent'] == 0) {
      $cPath_new = 'cPath=' . $counter;
    } else {
      $cPath_new = 'cPath=' . $this->tree[$counter]['path'];
    }
    $this->categories_string .= tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">';
    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $this->categories_string .= '<b>';
    }
    // display category name
    $this->categories_string .= tep_db_decoder($this->tree[$counter]['name']);
    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $this->categories_string .= '</b>';
    }
    $this->categories_string .= '</a>';
    if (SHOW_COUNTS == 'true') {
      $products_in_category = tep_count_products_in_category($counter);
      if ($products_in_category > 0) {
        $this->categories_string .= '&nbsp;<span class="category_count">(' . $products_in_category . ')</span>';
      }
    }
    $this->categories_string .= '</li></ul>' . "\n";

    if ($this->tree[$counter]['next_id'] != false) {
      $this->build_categories_string($this->tree[$counter]['next_id']);
    }

  } //end of build_categories_string

} // end of class
?>