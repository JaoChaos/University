<?php

   var intentos = 3; //Variable para contar el número de intentos. Si se pasan los 3 intentos fallidos, se borran los campos.

   function validate(){
      var username = document.getElementById("username").value;
   	var password = document.getElementById("password").value;
   	if ( username == $username && password == $password){

   		alert ("Login successfully");
   		window.location = "index.php"; // Cuando el login es correcto te lleva a la página principal.

   		return false;
   	}
   	else{

   		intentos --;// Disminuye en 1 los intentos.
   		alert("Le quedan "+intentos+" intentos;");

   		// Si el número de intentos llega a 0, deletea todos los campos.
   		if( intentos == 0){
   			document.getElementById("username").disabled = true;
   			document.getElementById("password").disabled = true;
   			document.getElementById("submit").disabled = true;
   		return false;
   		}
   	}
   }
?>
