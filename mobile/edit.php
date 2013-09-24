<?php
session_start();
include('../config.inc.php');
include('../version.inc.php');
//Classes and Libraries:
include(COMMON_DIR.'mysql.inc.php');//Tom's sql library interface + db connection settings
include(COMMON_DIR.'user.php');     //Class to represent a site user
include(COMMON_DIR.'screen.php');   //Class to represent a screen in the system
include(COMMON_DIR.'feed.php');     //Class to represent a content feed
include(COMMON_DIR.'field.php');    //Class to represent a field in a template
include(COMMON_DIR.'position.php'); //Class to represent a postion relationship
include(COMMON_DIR.'content.php');  //Class to represent content items
include(COMMON_DIR.'upload.php');   //Helps uploading
include(COMMON_DIR.'group.php');    //Class to represent user groups
include(COMMON_DIR.'dynamic.php');  //Functionality for dynamic content
include(COMMON_DIR.'notification.php');  //Functionality for notifications
include(COMMON_DIR.'newsfeed.php');  //Functionality for notifications
include(COMMON_DIR.'template.php');  //Class to represent a template
include(COMMON_DIR.'image.inc.php'); //Image library, used for resizing images
error_reporting (0);

  $db_host = 'mysql.comune.carpi.mo.it';
  $db_login = 'concerto';
  $db_password = 'qelpdc';
  $db_database = 'concerto';
  $link = mysql_pconnect($db_host, $db_login, $db_password);
  $db_selected = mysql_select_db($db_database, $link);

if($_SESSION['login'] == "connesso"){  
  $luogo = $_REQUEST['luogo'];
  $x = $_REQUEST['x'];
  $y = $_REQUEST['y'];
  $gruppo = $_REQUEST['gruppo'];
  $mac = $_REQUEST['mac'];
  $id = $_REQUEST['id'];
  
  $schermo = new Screen($id);
  $schermo->location = $luogo;
  $schermo->width = $x;
  $schermo->height = $y;
  $schermo->group_id = $gruppo;
  $schermo->mac_inhex = $mac;
  if($schermo->set_properties()){
      header( "Location: show2.php?id=".$id ); 
  }
}else
  include('login.php');
?>
