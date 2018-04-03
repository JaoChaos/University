<?php

	require_once 'db.php';
	require_once 'functions_html.php';

	$db = initDB();

	$username = $_POST['username'];
	$passencrypted = hash_hmac("sha512", $_POST['password'], KEY);

	$query = mysqli_query($db, "SELECT username,password FROM users WHERE username = '$username'" );
	if(mysqli_num_rows($query) > 0){
		$data = mysqli_fetch_array($query);
		if($data['password'] != $passencrypted)
			echo "Contraseña incorrecta";
		else{
			setcookie("autentificado", "yes");
			HTMLprincipal();
		}
	} else
		echo "No existe el nombre de usuario. Puedes registrarte <a href='registro.php'>aquí</a>";

?>
