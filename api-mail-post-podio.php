<?php

require_once 'Podio/PodioAPI.php';

function sendPodio($data)
{
  $PODIO_CLIENTID = get_option('PODIO_CLIENTID');
  $PODIO_CLIENTSECRET = get_option('PODIO_CLIENTSECRET');
  $PODIO_APPID = get_option('PODIO_APPID1');
  $PODIO_APPSECRET = get_option('PODIO_APPSECRET1');
  $PODIO_SPACEID = get_option('PODIO_SPACEID');

  $contact_name = $data['merges']['FNAME'] . ' ' . $data['merges']['LNAME'];
  $contact_email = $data['email'];

  $item_fields =  array(
    "source" => 1 );

  if (!empty($data['merges']['FB_ID']))
  {
    $contact_facebook = $data['merges']['FB_ID'];

    $item_fields["facebook-id"] = $contact_facebook;
  }
  else
  {
    $contact_facebook = "";
  }

  amrPodio::authenticate($PODIO_CLIENTID, $PODIO_CLIENTSECRET, $PODIO_APPID, $PODIO_APPSECRET);

  return amrPodio::createContactItem($PODIO_APPID, $PODIO_SPACEID, $contact_name, $contact_email, $contact_facebook, $item_fields, "contact");

}

class amrFacebook
{
  public static function get_fb_img($fbId)
  {
    try
    {
      $url = 'http://graph.facebook.com/' . $fbId . '/picture?type=large';
      $headers = get_headers($url,1);

      $profileimage = $headers['Location']; 

      $ext = pathinfo($profileimage, PATHINFO_EXTENSION);
      $filename = sys_get_temp_dir() . "/" . $fbId . "." . $ext;

      if (file_exists($filename)) 
      {
        return $filename;
      } else 
      {

        $ch = curl_init($profileimage);
        $fp = fopen( $filename, "wb");
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, wie z. B. Gecko) Chrome/13.0.782.215 Safari/525.13." );
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $filename;
      }
    }  catch (Exception $e) {
      return null;
    }
  }
}

class amrPodio
{

  public static function authenticate($PODIO_CLIENTID, $PODIO_CLIENTSECRET, $PODIO_APPID, $PODIO_APPTOKEN)
  {
    Podio::setup($PODIO_CLIENTID, $PODIO_CLIENTSECRET);
    Podio::authenticate_with_app($PODIO_APPID, $PODIO_APPTOKEN);
  }

  public static function createContactItem($appid, $spaceid, $contact_name, $contact_email, $contact_facebook, &$item_fields, $contact_target_tag)
  {
    try
    {
      $contact_fields_index = array("name"=>$contact_name, "mail"=>array($contact_email));
      $contact_fields = $contact_fields_index;

      if (!empty($contact_facebook))
      {
        $filename = amrFacebook::get_fb_img($contact_facebook);
        if ($filename)
        {
          $fid = PodioFile::upload ($filename, $contact_facebook . ".jpg");
          $contact_fields["avatar"] = ($fid->file_id);
        }
      }

      $existingContacts = PodioContact::get_for_app( $appid, $attributes = $contact_fields_index);

      if (count($existingContacts)>0)
      {
        $first =  $existingContacts[0];
        $ep_profile_id = $first->profile_id;

        PodioContact::update( $ep_profile_id, $contact_fields );

      } else
      {
        $ep_profile_id = PodioContact::create( $spaceid, $contact_fields);
      }

      $item_fields[$contact_target_tag] = $ep_profile_id;

      PodioItem::create( $appid,  array('fields' => $item_fields));
      return null;

    } catch (PodioError $e) {

      return self::createErrorTask($appid, 
        $spaceid, 
        $contact_name, 
        "There was an error. Podio responded with the error type " . $e->body['error'] ." and the mesage " . $e->body['error_description'] . "."
        );

    } catch (Exception $e) {

      return self::createErrorTask($appid, 
        $spaceid, 
        $contact_name, 
        "There was a general exception: " . $$e->getMessage()
        );
    }
  }

  public static function createErrorTask($appid, $spaceid, $contact_name, $description)
  {
    $title = "API Error creating Podio Item";

    if (!empty($contact_name))
      $title = $title . " for " . $contact_name;

    $err = self::createTask($appid, $spaceid, $title, $description);

    if ($err)
      $description = $description . "     " . $err;

    return $description;
  }

  public static function createTask($appid, $spaceid, $title, $description)
  {
    try  
    {
      $task = PodioTask::create_for( "app", $appid, $attributes = array( "text" => $title,
        "private" => false,
        "description" => $description,
        "status" => "active",
        "space_id" => $spaceid), $options = array() );

      return null;
    }  
    catch (PodioError $te) 
    {
      return "There was an error. The API responded with the error type " . $te->body['error'] ." and the mesage " . $te->body['error_description'] . ".";
    }
  }
}

?>
