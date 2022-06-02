<?php 
require 'vendor/autoload.php';
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
session_start();
if (isset($_SESSION['token'])) {
   global $accessToken;
   $accessToken = $_SESSION['token'];
  }
  if (isset($_SESSION['graph'])) {
    global $graph;
    $graph= $_SESSION['graph'];
  }
   if (isset($_SESSION['refresh_token'])) {
    global $refresh_token;
    $refresh_token= $_SESSION['refresh_token'];
   }
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


  if (isset($_GET['data']) && isset($_GET['next']) && isset($_GET['calendar'])) {
    
    $data = $_GET['data'];
    $next = $_GET['next'];
    $id = $_GET['calendar'];
    $response=get_events($data,$next,$id);
    echo(json_encode($response));
  }


function cons_get( $getEventsUrl)
{
  try {
    global $graph;
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
    
    global $refresh_token;
    global $oauthClient;
    $_SESSION['token'] = $oauthClient->getAccessToken('refresh_token', [
      'refresh_token' => $refresh_token
    ]);
    $_SESSION['graph']=getGraph($_SESSION['token'] );
    print_r("token");
    die();
  }
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
function get_events($data, $next, $id)
{
  $calendar = cons_get('/me/calendarGroups/AAMkADVmMGRkNjI2LWJiOTYtNDcwOC1hODdhLTMyYWVmYzAwZTQ3YQBGAAAAAADq-N749Fd6QLA6AZtqkPZYBwBvfdw-pazgQrgXvDXj_SaEAAAAAAEGAABvfdw-pazgQrgXvDXj_SaEAAAMwQm2AAA=/calendars/'.$id.'/calendarView?$select=id,subject,seriesMasterId,showAs,type,organizer,start,end&startDateTime=' . $data . 'T00:00:00.283Z&endDateTime=' . $next . 'T04:59:59.283Z');
  $calendardata = array();
  foreach ($calendar as $value) {
    $value = (array_values((array)$value));
    $array['subject'] = $value[0]['subject'];
    $array['id'] = $value[0]['id'];
    $array['showAs'] = $value[0]['showAs'];
    $array['start'] = $value[0]['start']['dateTime'];
    $array['end'] = $value[0]['end']['dateTime'];
    $array['organizer'] = $value[0]['organizer']['emailAddress'];
    $array['type'] = $value[0]['type'];
    array_push($calendardata, $array);
  }
  $Count = 0;
  foreach ($calendardata as $value) {
    if (explode('T', $value['start'])[0] != $data || $value['showAs'] == 'free' || $value['showAs'] == 'tentative') {
      unset($calendardata[$Count]);
    }
    $Count++;
  }
  usort($calendardata,'DescSort');
  return $calendardata;
}
function DescSort($item1,$item2)
{
  $item1= explode('.', (explode('T', $item1['start'])[1]))[0];
  $item2= explode('.', (explode('T', $item2['start'])[1]))[0];
    if ( $item1 == $item2) return 0;
    return ($item1 > $item2) ? 1 : -1;
}
