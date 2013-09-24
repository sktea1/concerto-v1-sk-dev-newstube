<?php
  session_start();
include('../config.inc.php');
//The version number, and other non-user globals:
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
  
   $sql = 'SELECT feed_content.feed_id as feed_id, COUNT(content.id) as cnt '.
          'FROM feed_content '.
          'LEFT JOIN content ON feed_content.content_id = content.id '.
          'WHERE feed_content.moderation_flag IS NULL '.
          'GROUP BY feed_content.feed_id;';
   $res = sql_query($sql);
  $sql1 = "SELECT * from user WHERE name = '".$_SESSION['utente']."'";
  $res1 = sql_query($sql1);
  $ut = sql_row_keyed($res1,0);
  $utente = new User($ut['username']);
  
   $more_waiting = 0;
   $mod_feeds = 0;
   for($i = 0;$row = sql_row_keyed($res,$i);++$i){
      $count = $row['cnt'];
      $feed = new Feed($row['feed_id']);
      if($feed->user_priv($utente, 'moderate',true)) {
         $mod_feeds=$row['cnt'];
      } else {
         $more_waiting += $row['cnt'];
      }
   }
  
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html>  
  <head>  
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">  
    <meta name="generator" content="PSPad editor, www.pspad.com">  
    <title>NewsTube
    </title>  
    <link href="mobile.css" type="text/css" rel="stylesheet"/>  
    <style type="text/css">
A:link {text-decoration: none; color: white;}
A:visited {text-decoration: none; color: white;}
A:active {text-decoration: none; color: white;}
A:hover {text-decoration: none; color: white;}

A.sc:link {text-decoration: none; color: black;}
A.sc:visited {text-decoration: none; color: black;}
A.sc:active {text-decoration: none; color: black;}
A.sc:hover {text-decoration: none; color: black;}

A.app:link {text-decoration: none; color: green;}
A.app:visited {text-decoration: none; color: green;}
A.app:active {text-decoration: none; color: green;}
A.app:hover {text-decoration: none; color: green;}

A.neg:link {text-decoration: none; color: red;}
A.neg:visited {text-decoration: none; color: red;}
A.neg:active {text-decoration: none; color: red;}
A.neg:hover {text-decoration: none; color: red;}
</style>
  </head>  
  <body>     
    <div class="logo">  
      <a href="dashboard.php"><img src="images/conc_bluebg.gif"></a>  
    </div>  
    <div class="menu">  
      <span><a href="add.php">Aggiungi</a>
      </span>&nbsp;&nbsp;     
      <span style=" background-color: #99CCFF">&nbsp;<a href="mod.php">Modera</a>
  <?
  if($ut['admin_privileges'] == 1 && $more_waiting > 0)
      echo "(".$more_waiting.")";
  elseif($mod_feeds > 0)
      echo "(".$mod_feeds.")";
  ?>&nbsp;</span>&nbsp;&nbsp;   
      <span>
        <a href="schermi.php">Schermi</a>
      </span>  
    </div>  
    <div align=center>  
<?
    $approve = $_REQUEST['ok'];
    $id = $_REQUEST['id'];
    $fid = $_REQUEST['fid'];
    //echo "$approve <br> $id <br> $fid";
    if($approve == "si"){
        $query = "SELECT * FROM user WHERE name = '".$_SESSION['utente']."'";
        $res = sql_query($query);
        $utente = sql_row_keyed($res, 0);
        //echo $utente['username'];
        $user = new User($utente['username']);
        $feed_group = new Feed($fid);
        $query2 = "SELECT content.*, feed_content.feed_id, feed_content.duration as 'tempo' FROM `content`, feed_content WHERE content.id = feed_content.content_id and id ='".$id."' and feed_id ='".$fid."'";
        $res2 = sql_query($query2);
        $feed = sql_row_keyed($res2, 0);
        $return_code = $feed_group->content_mod($feed['id'], 1, $user, $feed['tempo'], null);
        if($return_code == true)
            header( "Location: mod.php" );     
    }else {
?>
<form id="deny" name="deny" method="post" action="deny.php">
<br>
Motivo del rifiuto<br>
<select name="motivo" id="motivo">
<option value="1">Il tuo contenuto non &egrave; applicabile al mio feed</option>   
<option value="2">Il tuo contenuto &egrave; troppo difficile da leggere</option>   
<option value="3">Il tuo contenuto &egrave; ridondante</option>   
<option value="4">Il tuo contenuto &egrave; inappropriato</option> 
</select><br><br>
Messaggio addizionale da inviare al mittente:<br>
<textarea name="motadd" id="motadd" rows="4" cols="40"></textarea> <br><br>
<input type=hidden name="id" value="<?= $id; ?>">
<input type=hidden name="feed_id" value="<?= $fid; ?>">
<input type=submit value="Rifiuta"> 
<?    
    }
?>

    </div>  
  </body>  
</html>
<?
 }else
    include('login.php');
?>