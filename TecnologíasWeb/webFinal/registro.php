<?php

	//Fichero php para hacer el registro de usuario.

	require_once 'db.php';
	require_once 'functions_html.php';

	// Conexión a la db:
	$db = initDB();

	// Comprobamos si se han enviado todos los campos:
	if (!isset($_POST["username"]) | !isset($_POST["password"]) | !isset($_POST["cpassword"]) | !isset($_POST["email"]))
		echo "Por favor, rellene todos los campos.";
	else {
		// Comprobar si el usuario ya existe:
		$username = $_POST['username'];
		$email = $_POST['email'];
		$checkemail = mysqli_query($db, "SELECT email FROM users WHERE email='$email'".mysql_escape_string($db,$email));
		$checkuser = mysqli_query($db, "SELECT username FROM users WHERE username='$username'".mysql_escape_string($db,$username) );
		if(mysqli_num_rows($checkemail) > 0)
			HTMLregistro("Ya existe un usuario registrado con ese email");
		else if(mysqli_num_rows($checkuser) > 0)
			HTMLregistro("El nombre de usuario ya existe");
		else if($_POST["password"] != $_POST["cpassword"])
			HTMLregistro("Las contraseñas no coinciden");
		else{
			// Si no hay errorres introduciomos en la BD
			$passencrypted = hash_hmac("sha512", $_POST["password"], KEY); // Contraseña encriptada. KEY es una variable del archivo credenciales.php
			$res = mysqli_query($db, "INSERT INTO users(username, email, password) VALUES('$username', '$email', '$passencrypted')".mysql_escape_string($db,$users));
			if(!$res)
				echo mysqli_error($db);
			else{
				setcookie("autentificado", "yes"); // Creamos la cookie para saber que el usuario está autenticado.
				$_POST = array(); // Limpiamos el array de los valores enviados.
				HTMLprincipal(); // Direccionamos a la página de inicio.
			}
		}
	}

?>
