<?php 

	session_start();

	require_once('src/option.php');

	if(!empty($_POST['email']) && !empty($_POST['password'])){

		// connexion à la bdd
		require_once('src/connection.php');

		// variables 
		$email 		= htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);

		// adresse mail valide ? 
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			header("location: index.php?error=true&message=Votre adresse email est invalide");
			exit();

		}

		// chiffrement password 
		$password = 'aq1'.sha1($password.'123').'25';

		// Adresse email est-elle bien utilisée ? 
		$request = $bdd->prepare('SELECT COUNT(*) AS numberEmail FROM user WHERE email = ?');
		$request->execute([$email]);

		while($emailVerification = $request->fetch()){

			if($emailVerification['numberEmail'] != 1){

				header("location: index.php?error=true&message=Impossible de vous authentifier correctement.");
				exit();

			}

		}

		// connexion
		$req = $bdd->prepare('SELECT * FROM user WHERE email = ?');
		$req->execute([$email]);

		while($user = $req->fetch()){
			
			if($password == $user['password']){

				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $user['email'];

				// creation possible d'un cookie
				if(isset($_POST['auto'])){
					setcookie('auth', $user['secret'], time() + 365*24*3600, '/', null, false, true);
				}

				header("location: index.php?success=true");

			} else {

				header("location: index.php?error=1&message=Impossible de vous authentifier correctement.");
				exit();

			}

		}

	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/styles.css">
	<link rel="icon" type="image/png" href="assets/faviconNetflix.png">
</head>
<body>

	<?php require_once('src/header.php'); ?>

	<section>
		<div id="login-body">

			<?php if(isset($_SESSION['connect'])) { ?>

				<h1>Bonjour !</h1>

				<?php
				if(isset($_GET['success'])){

					echo'<div class="alert success">Vous êtes maintenant connecté.</div>';

				} ?>

				<p>Qu'allez-vous regarder aujourd'hui ?</p>
				<small><a href="logout.php">Déconnexion</a></small>

				<?php } else { ?>

				<h1>S'identifier</h1>
				<?php if(isset($_GET['error'])) {

					if(isset($_GET['message'])) {
						echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
					}

				} ?>
				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>
				

				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php require_once('src/footer.php'); ?>
	
</body>
</html>