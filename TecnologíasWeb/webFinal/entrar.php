<?php

	// Fichero PHP para la autenticación de un usuario.


	require_once 'db.php';
	require_once 'functions_html.php';

	$db = initDB();

	$username = htmlentities($_POST['username']);
	$passencrypted = hash_hmac("sha512", htmlentities($_POST['password']), KEY); // Comprobamos si el HASH de la clave introducida se corresponde con el almacenado en la base de datos.

	$query = mysqli_query($db, "SELECT username,password FROM users WHERE username='$username'".mysql_escape_string($db,$username,$password));
	if(mysqli_num_rows($query)==0)
		HTMLentrar("No existe el nombre de usuario. Puedes registrarte <a href='registro.php'>aquí</a>");
	else{
		$data = mysqli_fetch_array($query);
		if($data['password'] != $passencrypted)
			HTMLentrar("Contraseña incorrecta");
		else{
			$_SESSION["username"]=$username; 
			HTMLprincipal();
		}
	}

?>
