<?php 
	session_start();

	require ("log.php");

	if (!empty($_POST["email"]) && !empty($_POST["password"])) {

		$email 			= checkInput($_POST['email']);
		$password 		= checkInput($_POST["password"]);
		$hashedPassword = hash("sha256", $password);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			header('location: ./?error=1&message=Veuillez entrer une adresse mail valide');
			exit();
		}

		//email deja utilisé ? 
		require_once "database.php";
		$db = Database::connect();
		$statement = $db->prepare("SELECT count(*) as numberEmail FROM users WHERE email = ?");
		$statement->execute(array($email));
		Database::disconnect();

		while ($verification = $statement->fetch()) {
			if ($verification["numberEmail"] != 1) {
				header('Location: ./?error=1&message=Impossible de vous authentifier correctement');
				exit();
			}
		}

		//connexion
		require_once "database.php";
		$db = Database::connect();
		$statement = $db->prepare("SELECT * FROM users where email = ?");
		$statement->execute(array($email));
		Database::disconnect();

		while ($connection = $statement->fetch()) {
			if ($connection['password'] == $hashedPassword) {

				$_SESSION['connect'] = 1;
				$_SESSION["email"] = $connection["email"];

				if (isset($_POST["auto"])) {
					setcookie("auth", $connection['secret'], time() + 24*3600, '', null, false, true);
				}

				header("Location: ./?success=1");
				exit();
			} else {
				header('Location: ./?error=1&message=Impossible de vous authentifier correctement');
				exit();
			}
		}
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

			<?php if (isset($_SESSION['connect'])) { ?>

				<h1> Bonjour ! </h1>
				<?php 
				if (isset($_GET['success'])) {
					echo '<div class="alert success"> Vous êtes bien connecté </div>';
				} ?>
				<p>Qu'allez vous regarder aujourd'hui ?</p>
				<small><a href="logout.php">Deconnexion</a></small>

			<?php } else { ?>

				<h1>S'identifier</h1>

				<?php if (isset($_GET['error'])) {
				if (isset($_GET['message'])) {
					echo '<div class="alert error">' .checkInput($_GET['message']). '</div>';
					}
				} 
				?>

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

	<?php include('src/footer.php'); ?>
</body>
</html>