<?php 

  if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])){

    // connexion à la bdd
    require_once('connection.php');

    // variable secret
    $secret = htmlspecialchars($_COOKIE['auth']);
    
    // Le "secret" existe-til ? 
    $req = $bdd->prepare('SELECT COUNT(*) AS numberSecret FROM user WHERE secret = ?');
    $req->execute([$secret]);

    while($user = $req->fetch()){

      if($user['numberSecret'] == 1) {

        // Lire toutes les variables de l'user
        $userRequest = $bdd->prepare('SELECT * FROM user WHERE secret = ?');
        $userRequest->execute([$secret]);

        while($userInformation = $userRequest->fetch()){

          $_SESSION['connect']  = 1;
          $_SESSION['email']    = $userInformation['email'];

        }

      }

    }


  }

?>