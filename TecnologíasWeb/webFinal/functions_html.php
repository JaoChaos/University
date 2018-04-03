<?php
   require_once 'db.php';

// Función para crear el head de la página y el header del body.
// El parámetro "style" corresponde al nombre del fichero css que requiere la página a ser creada (inicio, listado de películas, película individual...)
function HTMLinicio($style){
echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta author="Juan Anaya Ortiz."/>
<meta author="Pablo Rey Pedrosa."/>
<title>FilmsDB</title>
<link rel="script" href="checkuser.js"/>
<link rel="icon" href="media/icono.png"/>
<link rel="stylesheet" href="css/footer.css"/>
<link rel="stylesheet" href="css/header.css"/>
<link rel="stylesheet" href="css/{$style}"/>
</head>
<body>
<header>
<div id="h_top">
<h1 id="title"> Films DataBase </h1>
<h3 id="phrase"> Share, Like and Order your films </h3><br>
</div>
<div id="h_bot">
<nav>
<div id="apartados-izq">
<ul class="lista_izq">
<li><a href="index.php">Inicio</a></li>
<li><a href="peliculas.php">Películas</a></li>
<li><a href="#">Usuarios</a></li>
<li><a href="#">Conócenos</a></li>
<li><a href="#">Contáctanos</a></li>
</ul>
</div>
<div id="apartados-der">
HTML;

// Barra de navegación según el usuario esté autenticado o no.
   if(!isset($_COOKIE['autentificado']) || ( isset($_COOKIE['autentificado']) && $_COOKIE['autentificado'] == "no" ) ){
echo <<<HTML
<ul class="lista_der">
<li><a href="signin.php">Sign In</a></li>
<li><a href="signup.php">Sign Up</a></li>
</ul>
HTML;
   }
   else{
echo <<<HTML
<ul class="lista_der">
<li><a href="logout.php">Log out</a></li>
</ul>
HTML;
   }

echo <<<HTML
</div>
</nav>
</div>
</header>
HTML;
}

// Función para la creación de la página de inicio
function HTMLprincipal(){
HTMLinicio("principal.css");

echo <<<HTML
<div class="cuerpo">
<div class="principal-izq">
<br>
<p> FilmsDB es una página Web opensource realizada por dos alumnos de Ingeniería Informática de la Universidad de Granada como proyecto para una de sus asignaturas.
Esperamos que os guste y os sea útil.
</p>

</div>

<div class="principal-der">
<img src="http://martingranados.es/wp-content/uploads/2016/12/peliculas.jpg">
</div>

<div id="aniadidas">

<h3> Películas añadidas recientemente</h3>

<ul class="lista">
<li><img src="http://www.todocuadros.com/imagenes/posters/peliculas/matrix.jpg"></li>
<li><img src="https://s-media-cache-ak0.pinimg.com/736x/82/a9/cc/82a9cc2633c6d9370d2152f252bf2491.jpg"></li>
<li><img src="http://media.salir-static.net/_images_/peliculas/8/f/6/2/cartel_el_senor_de_los_anillos_el_retorno_del_rey_0.jpg"></li>
<li><img src="https://www.cookingideas.es/imagenes/2013/05/batman-official.jpg"></li>
</ul>
</div>

</div>
HTML;

HTMLfinal();
}

// Función para crear el footer del documento HTML y los tags necesarios para cerrar el documento.
function HTMLfinal(){

echo <<<HTML
<footer>
<h4> FilmsDB. </h4>

<nav>
<ul class="lista">
<li><a href="#">Inicio</a></li>
<li><a href="#">About Us</a></li>
<li><a href="#">Contáctanos</a></li>
</ul>

</footer>
</body>
</html>
HTML;
}

// Función para crear la página individual de una película.
// El parámetro $title es el nombre de la película.

function HTMLindividual($title){
   HTMLinicio("individual.css");
   $db = initDB();
   $title = urldecode($title);
   $movie = mysqli_query($db, "SELECT * FROM movies WHERE name='$title'".mysql_escape_string($db, $title));
   if(!$movie){
      $msg = mysqli_error($db);
      echo "<h3> Error al cargar la página de la película.<h3>";
   }
   else{
      $movie = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM movies WHERE name='$title'".mysql_escape_string($db, $name)));
      $res_actores = getActors($db, $title);


function AniadirFav(){
   echo <<<HTML
   <form action="favoritas.php" method="post">
   Comentario: <input type="text" name="comment">
   <br/>
   Value: <input type="text" name="value"/>
   </br>
   
   <input type="submit"/>
   </form>
   HTML;

   $title = urldecode($title);
   $user = htmlentities($_POST['username']);
}

echo <<<HTML
<div id="izquierda">
<h2>${movie['name']}</h2>
<img src="${movie['img_path']}" alt="$title" height="200px" width="150px"/>

<h6>Puntuación ${movie['score']}</h6>
<h6>Duración: ${movie['duration']} minutos</h6>
<h6>Año ${movie['year']}</h6>

</div>

<div id="derecha">

HTML;
// Barra de navegación para usuarios registrados
   if(isset($_COOKIE['autentificado']) && $_COOKIE['autentificado'] == "yes"){
echo <<<HTML
<nav id="lista_favoritos">
<ul>
<li><a href="#">Añadir a mi Lista</a></li>
<li><a href="#">Eliminar de mi Lista</a></li>
<li><a href="#">Usuarios</a></li>
</ul>
</nav>
HTML;
   }

echo <<<HTML
</div>

<div id="abajo">
<h5>Reparto:</h5>
<ul id="reparto">
HTML;
   for($i = 0; $i < mysqli_num_rows($res_actores); $i++){
      $actor = mysqli_fetch_array($res_actores);
echo <<<HTML
<li>${actor['actor_name']}</li>
HTML;
}
echo <<<HTML
</ul>
</div>
HTML;
   }
   HTMLfinal();

}

// Función para crear la página con el listado de todas las películas
function HTMLpeliculas(){
   $db = initDB();
   $movies = getMovies($db);
   HTMLinicio("peliculas.css");
   echo "<table>";
   for($i = 0; $i < mysqli_num_rows($movies); $i += 3){
      echo "<tr>";
      for($j = 0; $j < 3; $j++){
         $tupla_movies = mysqli_fetch_array($movies);
         $title = $tupla_movies['name'];
         $img = $tupla_movies['img_path'];
         $link = urlencode($title);
echo <<<HTML
<th>
<a href="individual.php?movie=${link}"><img id="caratula" src="$img" alt="$title"/></a>
<a><h3>$title</h3></a>
</th>
HTML;
      }
         echo "</tr>";
   }
   echo "</table>";
   HTMLfinal();
}

// Función para crear el formulario para registrarse
function HTMLregistro($msg=""){
   HTMLinicio("log.css");
echo <<<HTML
<form action="registro.php" method="post">
Usuario: <input type="text" name="username" required/>
<br/>
Contraseña: <input type="password" name="password" required/>
<br/>
Repite Contraseña: <input type="password" name="cpassword" required/>
<br/>
Email: <input type="email" name="email" required/>
<br/>
<input type="submit" value="Registrar" />
</form>
<p>$msg</p>
HTML;
   HTMLfinal();
}

// Función para crear el formulario para iniciar sesión
function HTMLentrar($msg=""){
   HTMLinicio("log.css");
echo <<<HTML
<form action="entrar.php" method="POST">
Usuario: <input id="user" type="text" name="username" required/>
<br />
Contraseña: <input id="pass" type="password" name="password" required/>
<br />
<input type="submit"/>
</form>
<p>$msg</p>
HTML;
   HTMLfinal();
}


function acabarSesion() {
   // La sesión debe estar iniciada
   if (session_status()==PHP_SESSION_NONE)
   session_start();
   // Borrar variables de sesión
   //$_SESSION = array();
   session_unset();
   // Obtener parámetros de cookie de sesión
   $param = session_get_cookie_params();
   // Borrar cookie de sesión
   setcookie(session_name(), $_COOKIE[session_name()], time()-2592000,
   $param['path'], $param['domain'], $param['secure'], $param['httponly']);
   // Destruir sesión
   session_destroy();
}

?>
