
<?php

use Beta\Microsoft\Graph\Model\Document;

require 'vendor/autoload.php';
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
session_start();
if(isset($_SESSION['token'])){
  header('Location: '.'salas.php');
  die();
}
  // Initialize the OAuth client
  $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => ('a49b63ab-844e-48f5-9ffa-54a439feaf0a'),
    'clientSecret'            => ('Ppe8Q~_T_GSJrhF3wMRqqew0eGe_JeFtTpiZ~biQ'),
    'redirectUri'             => ('http://localhost/calendarios/salas.php/callback'),
    'urlAuthorize'            => ('https://login.microsoftonline.com/common').('/oauth2/v2.0/authorize'),
    'urlAccessToken'          => ('https://login.microsoftonline.com/common').('/oauth2/v2.0/token'),
    'urlResourceOwnerDetails' => '',
    'scopes'                  => ('openid profile offline_access user.read mailboxsettings.read calendars.readwrite')
  ]);

  $authUrl = $oauthClient->getAuthorizationUrl();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</head>

<body>
  <div class="container ">
    
<div class="px-4 py-5 my-5  text-center">
    <img class=" mx-auto mb-4" src="images/logo-repremundo-main.png" alt="" >
    <h1 class="display-5 fw-bold">Seleccion Sala Repremundo</h1>
    <div class="col-lg-6 mx-auto">
      <p class="lead mb-4">Esta accediendo a la interfaz de selección de sala de REPREMUNDO, en ella se visualiza la disponibilidad de las salas.</p>
      <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
        <button type="button" style="background-color:#3973b3;"onclick="location.href='<?php echo ($authUrl); ?>'" class="btn btn-primary btn-lg px-4 gap-3">Iniciar sesion</button>
        <button type="button" style="background-color:#ea5647;"class="btn  btn-lg px-4 text-light">Regresar</button>
      </div>
    </div>
  </div> 
  </div>
</body>
</html>