<?php 
	if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {
		$secret = htmlspecialchars($_COOKIE['auth']);

		//verification
		require "database.php";
		$db = Database::connect();
		$statement = $db->prepare("SELECT count(*) as numberAccount FROM users where secret = ?");
		$statement->execute(array($secret));

		while ($userSecret = $statement->fetch()) {
			if ($userSecret["numberAccount"] == 1) {
				$statementUser = $db->prepare("SELECT * FROM users where secret = ?");
				$statementUser->execute(array($secret));

				while ($userAccount = $statementUser->fetch()) {
					$_SESSION['connect'] = 1;
					$_SESSION["email"] = $userAccount["email"];
				}
			}
		}
	}
?>