<?php 
	session_start();
	require ("log.php");

	if (isset($_SESSION['connect'])) {
		header('Location: ./index.php');
	}

	if (!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["password_two"])) {
		$email 			= checkInput($_POST['email']);
		$password 		= checkInput($_POST["password"]);
		$password_two 	= checkInput($_POST["password_two"]);
		

		if ($password != $password_two ) {
			header('Location: ./inscription.php?error=1&message=Les deux mots de passe doivent etre identiques');
			exit();
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			header('location: inscription.php?error=1&message=Veuillez entrer une adresse mail valide');
			exit();
		}

		//email deja utilisé ?
		require_once "database.php";
		$db = Database::connect();

		$statement = $db->prepare("SELECT count (*) as numberEmail from users where email=?");
		$statement->execute(array($email));

		Database::disconnect();

		while ($verification = $statement->fetch()) {
			if ($verification["numberEmail"] != 0) {
			header('Location: ./inscription.php?error=1&message=Cette adresse mail existe deja');
			exit();
			}
		}
			
		require_once "database.php";
		$db = Database::connect();

		$hashedPassword = hash("sha256", $password);
		$secret = hash("sha256", $hashedPassword);

		$statement = $db->prepare("INSERT INTO users (email, password, secret) VALUES (?, ?, ?)");
		$statement->execute(array($email, $hashedPassword, $secret));
		header('Location: inscription.php?success=1');
		exit();

		Database::disconnect();
	}


	function checkInput($data) {
		$data = trim($data);
		$data = stripcslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php if (isset($_GET['error'])) {
				if (isset($_GET['message'])) {
					echo '<div class="alert error">' .checkInput($_GET['message']). '</div>';
				}
			} else if (isset($_GET["success"])) {
				echo '<div class="alert success"> Vous êtes bien inscrit!<br> <a href="index.php"> Cliquer ici pour vous connecter</a> </div>';
			}
			?>
				
			

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>