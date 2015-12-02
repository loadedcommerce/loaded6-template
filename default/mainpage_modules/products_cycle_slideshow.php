<?php 
  if (defined('MODULE_ADDONS_PCSLIDESHOW_STATUS') && MODULE_ADDONS_PCSLIDESHOW_STATUS == 'True') {
    $pcs_folder = DIR_WS_IMAGES . 'pcs_images/';
    $dir_writable = substr(sprintf('%o', fileperms($pcs_folder)), -4) == "0777" ? "true" : "false";
    if ($dir_writable != 'true') {
      $info_box_contents = array();
      $info_box_contents[] = array('text' => BOX_HEADING_PRODUCTS_SLIDESHOW);
      new contentBoxHeading($info_box_contents, $column_location);

      $info_box_contents = array();
      $info_box_contents[] = array('text' => '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">' .
                                                  '<tr>' .
                                                      '<td valign="top" align="center"><center>' .
                                                          '<div class="ProductsCycleSlideshowWrapper">' .
                                                              '<div id="PCS1" style="color:red; font-weight:bolder; font-size:12px;">
                                                                 Please make the "images/pcs_images/" folder writeable by changing the permissions to "0777" or "777".
                                                               </div>' .
                                                              '<div id="PCS1Output" class="main"></div>' .
                                                              '<div id="PCS1Pager" class="PCSPager"></div>' .
                                                          '</div>' .
                                                      '</td></center>' .
                                                  '</tr>' .
                                                '</table>');
      new contentBox($info_box_contents, true, true);

      if (TEMPLATE_INCLUDE_CONTENT_FOOTER =='true'){ 
          $info_box_contents = array();
          $info_box_contents[] = array('align' => 'left',
                                       'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                                      );
          new contentBoxFooter($info_box_contents);
      }
    } else {
      function pcs_href_image($src_path) {
        $strRet = DIR_WS_IMAGES . 'pcs_images/' . basename($src_path) . '_' . MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT . '_' . MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH . '_' . MODULE_ADDONS_PCSLIDESHOW_IMAGE_QUALITY . '.jpg'; #This will be the filename of the resized image.
        if (!file_exists($strRet)) { #Create the file if it does not exist
          #check to see if source file exists
          if (!file_exists($src_path)) {
            return 'error1';
          #check to see if source file is readable
          } elseif (!is_readable($src_path)) {
            return 'error2';
          }
          #check if gif
          if (stristr(strtolower($src_path),'.gif')) $oldImage = ImageCreateFromGif($src_path);
          #check if jpg
          elseif (stristr(strtolower($src_path),'.jpg') || stristr(strtolower($src_path),'.jpeg')) $oldImage = ImageCreateFromJpeg($src_path);
          #check if png
          elseif (stristr(strtolower($src_path),'.png')) $oldImage = ImageCreateFromPng($src_path);
          #unknown file format
          else return 'error3';
      
          #Create the new image    
          if (function_exists("ImageCreateTrueColor")){
            $newImage = ImageCreateTrueColor(MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH,MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT);
          } else {
            $newImage = ImageCreate(MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH,MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT);
          }
          $backgroundColor = imagecolorallocate($newImage, 255, 255, 255);
          imagefill($newImage, 0, 0, $backgroundColor);
          
          #calculate the rezised image's dimmensions
          if (imagesx($oldImage) > MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH || imagesy($oldImage) > MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT) { #Resize image
            if (imagesx($oldImage)/MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH > imagesy($oldImage)/MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT) { #Width is leading in beeing to large
              $newWidth = (int)MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH;
              $newHeight = (int)MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH/imagesx($oldImage)*imagesy($oldImage);
            } else { #Height is leading in beeing to large
              $newHeight = (int)MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT;
              $newWidth = (int)MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT/imagesy($oldImage)*imagesx($oldImage);
            }    
          } else {#Don't rezise image
            $newWidth = imagesx($oldImage);
            $newHeight = imagesy($oldImage);
          }
          #Copy the old image onto the new image
          ImageCopyResampled($newImage, $oldImage, MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_WIDTH/2-$newWidth/2, MODULE_ADDONS_PCSLIDESHOW_MAX_IMAGE_HEIGHT/2-$newHeight/2, 0, 0, $newWidth, $newHeight, imagesx($oldImage), imagesy($oldImage));
          imagejpeg($newImage,$strRet,MODULE_ADDONS_PCSLIDESHOW_IMAGE_QUALITY); #save the image
          imagedestroy($oldImage); #Free Memory
          imagedestroy($newImage); #Free memory
        }
        return $strRet;
      }

      $new_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_image_med, p.products_image_lrg, p.products_tax_class_id, pd.products_name, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_added desc limit " . MODULE_ADDONS_PCSLIDESHOW_MAX_DISPLAY_NEW_PRODUCTS);
      while ($new_product = tep_db_fetch_array($new_products_query)) {
        if ($new_product['products_image_lrg'] != '') {
          $new_product_image = $new_product['products_image_lrg']; 
        } else if ($new_product['products_image_med'] != '') {
          $new_product_image = $new_product['products_image_med']; 
        } else {
          $new_product_image = $new_product['products_image']; 
        }
        $new_str .= '<div class="PCSChild" alt="'.  htmlspecialchars(html_entity_decode('<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_product['products_id']) . '"><b>'.BOX_HEADING_WHATS_NEW.'</b>&nbsp;&nbsp;-&nbsp;&nbsp;'.$new_product['products_name'].'<br />'.$currencies->display_price($new_product['products_price'], tep_get_tax_rate($new_product['products_tax_class_id'])).'</a>'))  .'"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_product['products_id']) . '"><img src="'.pcs_href_image(DIR_WS_IMAGES.$new_product_image).'" alt="'.$new_product['products_name'].'" ></a></div>';
      }
      $special_products_query = tep_db_query("select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_image, p.products_image_med, p.products_image_lrg, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' order by rand() limit " . MODULE_ADDONS_PCSLIDESHOW_MAX_DISPLAY_SPECIALS);
      while ($special_product = tep_db_fetch_array($special_products_query)) {
        if ($special_product['products_image_lrg'] != '') {
          $special_product_image = $special_product['products_image_lrg']; 
        } else if ($special_product['products_image_med'] != '') {
          $special_product_image = $special_product['products_image_med']; 
        } else {
          $special_product_image = $special_product['products_image']; 
        }
        $spec_str .= '<div class="PCSChild" alt="'.  htmlspecialchars(html_entity_decode('<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $special_product['products_id']) . '"><b>'.BOX_HEADING_SPECIALS.'&nbsp;&nbsp;-&nbsp;&nbsp;</b>'.$special_product['products_name'].'<br /><s>'.$currencies->display_price($special_product['products_price'], tep_get_tax_rate($special_product['products_tax_class_id'])).'</s> <span class="productSpecialPrice">'.$currencies->display_price($special_product['specials_new_products_price'], tep_get_tax_rate($special_product['products_tax_class_id'])).'</span></a>'))  .'"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $special_product['products_id']) . '"><img src="'.pcs_href_image(DIR_WS_IMAGES.$special_product_image).'" alt="'.$special_product['products_name'].'"></a></div>';
      }
      $best_sellers_query  = tep_db_query("select distinct p.products_id, p.products_image, p.products_image_med, p.products_image_lrg, p.products_tax_class_id, pd.products_name, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_ordered desc, pd.products_name limit " . MODULE_ADDONS_PCSLIDESHOW_MAX_DISPLAY_BESTSELLERS);
      while ($best_seller = tep_db_fetch_array($best_sellers_query)) {
        if ($best_seller['products_image_lrg'] != '') {
          $best_seller_image = $best_seller['products_image_lrg']; 
        } else if ($best_seller['products_image_med'] != '') {
          $best_seller_image = $best_seller['products_image_med']; 
        } else {
          $best_seller_image = $best_seller['products_image']; 
        }
        $best_str .=  '<div class="PCSChild" alt="'.  htmlspecialchars(html_entity_decode('<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_seller['products_id']) . '"><b>'.BOX_HEADING_BESTSELLERS.'&nbsp;&nbsp;-&nbsp;&nbsp;</b>'.$best_seller['products_name'].'<br />'.$currencies->display_price($best_seller['products_price'], tep_get_tax_rate($best_seller['products_tax_class_id'])).'</a>'))  .'"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_seller['products_id']) . '"><img src="'.pcs_href_image(DIR_WS_IMAGES.$best_seller_image).'" alt="'.$best_seller['products_name'].'"></a></div>';
      }
      
      if (tep_not_null($new_products_query) || tep_not_null($special_products_query) || tep_not_null($best_sellers_query)) {

        $info_box_contents = array();
        $info_box_contents[] = array('text' => BOX_HEADING_PRODUCTS_SLIDESHOW);
        new contentBoxHeading($info_box_contents, $column_location);

        $info_box_contents = array();
        $info_box_contents[] = array('text' => '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">' .
                                                    '<tr>' .
                                                        '<td valign="top" align="center"><center>' .
                                                            '<div class="ProductsCycleSlideshowWrapper">' .
                                                                '<div id="PCS1" class="ProductsCycleSlideshow">' .
                                                                $new_str .
                                                                $spec_str .
                                                                $best_str .
                                                                '</div>' .
                                                                '<div id="PCS1Output" class="main"></div>' .
                                                                '<div id="PCS1Pager" class="PCSPager"></div>' .
                                                            '</div>' .
                                                        '</td></center>' .
                                                    '</tr>' .
                                                  '</table>');
        new contentBox($info_box_contents, true, true);

        if (TEMPLATE_INCLUDE_CONTENT_FOOTER =='true'){ 
            $info_box_contents = array();
            $info_box_contents[] = array('align' => 'left',
                                         'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                                        );
            new contentBoxFooter($info_box_contents);
        }
      }
    }
  }
?>