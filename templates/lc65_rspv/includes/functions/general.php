<?php
if(!function_exists('hide_add_to_cart')){
  function hide_add_to_cart() {
    if (defined('HIDE_ADD_TO_CART')) {
      switch(HIDE_ADD_TO_CART) {
        case 'Guest' :
          if(!isset($_SESSION['customer_id']) || (int)$_SESSION['customer_id'] == 0) {
            return 'true';
          }
        break;
        case 'Guest and Retail':
          if((!isset($_SESSION['customer_id']) || (int)$_SESSION['customer_id'] == 0 ) || (isset($_SESSION['customer_id']) && (int)$_SESSION['customer_id'] > 0 && (int)$_SESSION['sppc_customer_group_id'] == 0)) {
            return 'true';
          }
        break;
        case 'Off':
        default:
          return 'false';
      }
    }
    return 'false';

  }
}
if(!function_exists('group_hide_show_prices')){
  function group_hide_show_prices() {
	if(INSTALLED_VERSION_TYPE == 'B2B')	{
		if ( !isset($_SESSION['customer_id']) || (int)$_SESSION['customer_id'] == 0) {
		  return 'true';
		} else {
		  $hide_show_query = tep_db_query("SELECT group_hide_show_prices FROM customers_groups cg, customers c WHERE cg.customers_group_id = c.customers_group_id AND c.customers_id = '". $_SESSION['customer_id']."'");
		  $hide_show_prices = tep_db_fetch_array($hide_show_query);
		  switch($hide_show_prices['group_hide_show_prices']) {
			case 0:
			  return 'false';
			break;
			case 1:
			  return 'true';
			break;
		  }
		}
	}
	else
		return 'true';
  }
}
function cre_site_branding_rspv($show = '') {
  global $languages_id;    
    
  $store_info = '';
    
  $site_branding_query = tep_db_query("SELECT *
                                       FROM " . TABLE_BRANDING_DESCRIPTION . "
                                       WHERE language_id = " . $languages_id);
  $site_brand_info = tep_db_fetch_array($site_branding_query);
  
  $affiliate_branding = array();
  if (isset($_SESSION['affiliate_ref'])) {
    $affiliate_branding_query = tep_db_query("SELECT affiliate_template, affiliate_cobrand_image, affiliate_cobrand_name, affiliate_cobrand_slogan, affiliate_cobrand_url, affiliate_cobrand_support_email, affiliate_cobrand_support_phone
                                              FROM " . TABLE_AFFILIATE . "
                                              WHERE affiliate_id = " . $_SESSION['affiliate_ref']);
    $affiliate_branding = tep_db_fetch_array($affiliate_branding_query);
  }
  
  if (tep_not_null($affiliate_branding['affiliate_cobrand_url'])) {
    $brand_url = $affiliate_branding['affiliate_cobrand_url'];
  } elseif (tep_not_null($site_brand_info['store_brand_homepage'])) {
    $brand_url = $site_brand_info['store_brand_homepage'];
  } else {
    $brand_url = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
  }
  
  switch($show){
    case 'storeurl':
      $store_info = '<a href="' . $brand_url . '">' . str_replace('http://','',$brand_url) . '</a>';
      break;

    case 'phone':
      if (tep_not_null($affiliate_branding['affiliate_cobrand_support_phone'])) {
        $store_info = $affiliate_branding['affiliate_cobrand_support_phone'];
      } elseif (tep_not_null($site_brand_info['store_brand_support_phone'])) {
        $store_info = $site_brand_info['store_brand_support_phone'];
      }
      break;

    case 'email':
      if (tep_not_null($affiliate_branding['affiliate_cobrand_support_email'])) {
        $branding_mailto = $affiliate_branding['affiliate_cobrand_support_email'];
      } elseif (tep_not_null($site_brand_info['store_brand_support_email'])) {
        $branding_mailto = $site_brand_info['store_brand_support_email'];
      } else {
        $branding_mailto = STORE_OWNER_EMAIL_ADDRESS;
      }
      $branding_mailto = str_replace('@','&#64;',$branding_mailto); //let's fight spam. Not strong as javascript, but works...!
      $store_info = '<a href="mailto&#x3A;' . $branding_mailto . '">' . $branding_mailto . '</a>';
      break;

    case 'logo':
      if (tep_not_null($affiliate_branding['affiliate_cobrand_image']) && file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'affiliate_cobrand/' . $affiliate_branding['affiliate_cobrand_image'])) {
        $store_info = '<a href="'. $brand_url . '">' . tep_image(DIR_WS_IMAGES . 'affiliate_cobrand/' . $affiliate_branding['affiliate_cobrand_image']) . '</a>';
      } elseif (tep_not_null($affiliate_branding['affiliate_cobrand_name']))  {
        $store_info = '<a href="'. $brand_url . '" class="branding_name">' . $affiliate_branding['affiliate_cobrand_name'] . '</a>';
      } elseif (tep_not_null($site_brand_info['store_brand_image']) && file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logo/' . $site_brand_info['store_brand_image'])) {
        $store_info = '<a href="' . $brand_url . '">' . tep_image(DIR_WS_IMAGES . 'logo/' . $site_brand_info['store_brand_image'], '', '', '', 'class="small-margin-right img-logo-responsive"') . '</a>';
      } elseif (tep_not_null($site_brand_info['store_brand_name'])) {
        $store_info = '<a class="branding_name" href="' . $brand_url . '">' . $site_brand_info['store_brand_name'] . '</a>';
      } else {
        $store_info = '<a class="branding_name" href="' . $brand_url . '">' . STORE_NAME .'</a>';
      }
      break;

    case 'logourl':
      if (tep_not_null($affiliate_branding['affiliate_cobrand_image']) && file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'affiliate_cobrand/' . $affiliate_branding['affiliate_cobrand_image'])) {
        $store_info = (($_SERVER['SERVER_PORT'] == 443)?HTTPS_SERVER:HTTP_SERVER).'/'.DIR_WS_IMAGES . 'affiliate_cobrand/' . $affiliate_branding['affiliate_cobrand_image'];
      } elseif (tep_not_null($site_brand_info['store_brand_image']) && file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logo/' . $site_brand_info['store_brand_image'])) {
        $store_info = (($_SERVER['SERVER_PORT'] == 443)?HTTPS_SERVER:HTTP_SERVER).'/'.DIR_WS_IMAGES . 'logo/' . $site_brand_info['store_brand_image'];
      } else {
        $store_info = (($_SERVER['SERVER_PORT'] == 443)?HTTPS_SERVER:HTTP_SERVER).'/'.DIR_WS_IMAGES.STORE_LOGO;
      }
      break;

    case 'slogan':
      if (tep_not_null($affiliate_branding['affiliate_cobrand_slogan'])) {
        $store_info = '<p class="slogan clear-both small-margin-top-neg hide-on-mobile">' . $affiliate_branding['affiliate_cobrand_slogan'] . '</p>';
      } elseif (tep_not_null($site_brand_info['store_brand_slogan']) ) {
        $store_info = '<p class="slogan clear-both small-margin-top-neg hide-on-mobile">' . $site_brand_info['store_brand_slogan'] . '</p>';
      }
      break;

    default:
      $store_info = '<a class="store_name" href="' . tep_href_link(FILENAME_DEFAULT) . '">' . STORE_NAME .'</a>';
      break;
        
  }//end switch
  return $store_info;
 }

?>