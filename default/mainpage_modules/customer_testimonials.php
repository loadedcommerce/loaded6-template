<?php
/*
  $Id: customer_testimonials.php,v 1.3 2007/09/03 meastro Exp $

  Contribution Central, Custom CRE Loaded Programming
  http://www.contributioncentral.com
  Copyright (c) 2007 Contribution Central

  Released under the GNU General Public License
*/
  include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CUSTOMER_TESTIMONIALS);
  if ($random_testimonial = tep_random_select("select * FROM " . TABLE_CUSTOMER_TESTIMONIALS . " WHERE status = 1 ORDER BY rand() LIMIT 1")) {
?>
         <tr>
           <td>
           <?php
             $info_box_contents = array();
             $info_box_contents[] = array('align' => 'left',
                                          'text'  => BOX_HEADING_CUSTOMER_TESTIMONIALS);

             new contentBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_CUSTOMER_TESTIMONIALS));

             $testimonial = substr($random_testimonial['testimonials_html_text'], 0, MODULE_ADDONS_CTM_MAINPAGE_TRUNCATE);

             $info_box_contents = array();
             $info_box_contents[] = array('align' => 'left',
                                          'text' => '<table cellpadding="5"><tr><td valign="top"><b><center>' . $testimonial_titulo . '</center></b><br>' . strip_tags($testimonial) . '...<br><br><b><a href="' . tep_href_link(FILENAME_CUSTOMER_TESTIMONIALS, tep_get_all_get_params(array('language', 'currency')) .'&testimonial_id=' . $random_testimonial['testimonials_id']) . '">' . TEXT_READ_MORE . ' ' . tep_image(DIR_WS_IMAGES . 'star_arrow.gif') . '</b></a><br><br><table align="right" border="0" cellspacing="0" cellpadding="0"><tr align="right"><td align="right" class="infoBoxContents">' .  '<b>'.$random_testimonial['testimonials_name'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td></tr></table></td></tr></table>'
                                          );
             new contentBox($info_box_contents, true, true);
             
             if (TEMPLATE_INCLUDE_FOOTER =='true'){
               $info_box_contents = array();
               $info_box_contents[] = array('align' => 'left',
                                            'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                                            );
               new contentBoxFooter($info_box_contents);
             } 
           ?>
           </td>
         </tr>
<?php
  }
?>