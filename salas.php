<?php

use Beta\Microsoft\Graph\Model\Calendar;
use Beta\Microsoft\Graph\Model\Document;

require 'vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

session_start();
if (isset($_SESSION['token'])) {
  $accessToken = $_SESSION['token'];
}
if(isset($_REQUEST['code'])){
  $_SESSION['code']=$_REQUEST['code'];
} else{
  try{
    header("Location:" .'salas.php'. '/callback?code='.$_SESSION['code']);
     
  } catch (Exception $e){
    close_session();
  }
}

// Initialize the OAuth client
global $oauthClient;
$oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
  'clientId'                => ('a49b63ab-844e-48f5-9ffa-54a439feaf0a'),
  'clientSecret'            => ('Ppe8Q~_T_GSJrhF3wMRqqew0eGe_JeFtTpiZ~biQ'),
  'redirectUri'             => ('http://localhost/calendarios/salas.php/callback'),
  'urlAuthorize'            => ('https://login.microsoftonline.com/common') . ('/oauth2/v2.0/authorize'),
  'urlAccessToken'          => ('https://login.microsoftonline.com/common') . ('/oauth2/v2.0/token'),
  'urlResourceOwnerDetails' => '',
  'scopes'                  => ('openid profile offline_access user.read mailboxsettings.read calendars.readwrite')
]);


if (isset($_REQUEST['code']) && !isset($_SESSION['token'])) {
  try {
    global $accessToken;
    $accessToken = $oauthClient->getAccessToken('authorization_code', [
      'code' => $_REQUEST['code']
    ]);
    $refresh_token=json_encode($accessToken);
    $refresh_token=((array)json_decode($refresh_token))['refresh_token'];
    $_SESSION['refresh_token']=$refresh_token;
    $_SESSION['token'] = $accessToken;
  } catch (Exception $e) {
    close_session();
  }
} elseif (!isset($accessToken)) {
  close_session();
}

function getGraph($accessToken)
{
  try {
    $graph = new Graph();
    $graph->setAccessToken($accessToken);
    return $graph;
  } catch (Exception $e) {
    unset($_SESSION['token']);
    header("Location:" . '../inisecio.php');
    die();
  }
}

function cons_get($accessToken, $getEventsUrl)
{
  try {
    global $graph;
    $graph = getGraph($accessToken);
    $_SESSION['graph'] = $graph;
    $time = 'SA Pacific Standard Time';
    $events = $graph->createRequest('GET', $getEventsUrl)
      ->addHeaders(array(
        'Prefer' => 'outlook.timezone="' . $time . '"'
      ))
      ->setReturnType(Model\Event::class)
      ->execute();
    $response = ((array)($events));
    return $response;
  } catch (Exception $e) {
    close_session();
  }
}
function get_calendars($accessToken)
{
  $calendars = cons_get($accessToken, 'https://graph.microsoft.com/v1.0/me/calendarGroups/AAMkADVmMGRkNjI2LWJiOTYtNDcwOC1hODdhLTMyYWVmYzAwZTQ3YQBGAAAAAADq-N749Fd6QLA6AZtqkPZYBwBvfdw-pazgQrgXvDXj_SaEAAAAAAEGAABvfdw-pazgQrgXvDXj_SaEAAAMwQm2AAA=/calendars?$select=id,name');
  $salas = array();
  foreach ($calendars as $calendar) {
    $calendar = array_values((array)$calendar)[0];
    array_push($salas, $calendar);
  }
  return $salas;
}

if (isset($accessToken)) {
  global $accessToken;
  $salas = get_calendars($accessToken);
  $response = cons_get($accessToken, '/me');
  $response = (reset($response));
}

if (isset($_POST['btn'])) {
  close_session();
}

function refresh_token(){
  global $oauthClient;
    $_SESSION['token'] = $oauthClient->getAccessToken('refresh_token', [
      'refresh_token' => $_SESSION['refresh_token']
    ]);
}

function close_session()
{
  unset($_SESSION['token']);
  unset($_SESSION['graph']);
  unset($_SESSION['code']);
  header("Location:" . '../inisecio.php');
  die();
}


?>


<!DOCTYPE html>
<html id="main_container" lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <title>Calendario S</title>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="../salas.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


</head>

<body style="background-color:#2674b4 ;">
  <div style="background-color:#2674b4 ;">

    <div class="container text-center ">
      <? if (isset($_REQUEST[['code']])) { ?>
        <div class="card mt-3" id="change_room">
          <div id="header" style="text-align: left ; position: sticky; top: 0px;  z-index:2" class="card-header bg-light ">
            <div class="row">
              <div class="col-sm-10">
                <h6>Bienvenido <?php echo $response['mail'] ?> </h6>
                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="select">

                  <option selected>Seleccione alguna sala</option>
                  <?php foreach ($salas as $value) {
                  ?>
                    <option value=" <?php echo $value['id']; ?>">
                      <?php echo $value['name']; ?>

                    </option>
                  <?php } ?>

                </select>
              </div>
              <form method="POST" class="col-sm-2">
                <button class="btn btn-danger  " name="btn"> Cerrar sesion</button>
              </form>
            </div>
          </div>
          <div class="card-body">

            <div class="row bg-white " style=" position: sticky; top: 0px; z-index:3">
              <div class="col-sm-4 ">
                <div class="row">
                  <div class="col-sm-4">
                  <img src="../images/xgear-red.png" id="conf" width="35">
                    <img class="pt-3 pb-3" src="../images/max.png" id="max" width="60">
                    
                  </div>
                  <div class="col-sm-8">
                    <img id="state" src="../images/room-free.png" alt="" width="110">
                  </div>
                </div>
              </div>
              <div class="col-sm-4 d-flex justify-content-center align-self-center">
                <h1 style="font-size: 80px" id="clock">hora</h1>
              </div>
              <div class="col-sm-4">
                <div style="  position: relative; display: inline-block; text-align: center;">
                  <img src="../images/calender_red.png" alt="" width="130">
                  <div class="sticky-top" style="  position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%);">
                    <h1 class="text-light " id="mes"></h1>

                    <h1 style="font-size: 80px" id="dia"></h1>
                  </div>
                </div>
              </div>
            </div>
            <div style="position:relative; z-index:1;">
              <div class="card mb-3" id="card-x">
                <div class="card-body">
                  <h5 class="card-title" id="card-title-x"></h5>
                  <div class="card-text" id="card-text-x">
                    <h6 id="name-x"></h6>
                  </div>
                </div>
              </div>
            </div>
            <div class=" ml-3 mr-3" id="meet" style="position:relative; z-index:1;">

            </div>

          <? } ?>
          </div>
        </div>
</body>

</html>