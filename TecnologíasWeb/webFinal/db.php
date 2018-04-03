<?php

   require_once 'credenciales.php';

   // Fucnión para iniciar la conexión con la base de datos.
   function initDB(){
      $db = mysqli_connect(DB_HOST,DB_USER,DB_PASSWD,DB_DATABASE);
      mysqli_set_charset($db,"utf8");
      return $db;
   }

   /***********************************************************************************************/
   //                     Funciones utilizadas para la extracción de los datos                    //
   function nextPos($pos, $line){
      return strpos($line, ",", $pos+1);
   }

   function jumpOne(&$pos1, &$pos2, $line){
      $pos1 = nextPos($pos2, $line);
      $pos2 = nextPos($pos1, $line);
   }

   function jumpTwo(&$pos1, &$pos2, $line){
      $pos1 = nextPos($pos2, $line);
      $pos2 = nextPos($pos1, $line);
      $pos1 = $pos2;
      $pos2 = nextPos($pos2, $line);
   }
   /***********************************************************************************************/
   // Función para extraer todos los datos que nos interesan
   function getData($line, &$director, &$duration, &$actor1, &$generos, &$actor2, &$titulo, &$actor3, &$lenguaje, &$pais, &$year, &$imdbscore){
      // EXTRAER DIRECTOR
      $pos1 = strpos($line, ",");
      $pos2 = nextPos($pos1, $line);
      $director = substr($line, $pos1+1, $pos2-$pos1-1);

      // EXTRAER DURACIÓN
      jumpOne($pos1, $pos2, $line);
      $duration = substr($line, $pos1+1, $pos2-$pos1-1);
      if($duration=="")
         $duration=0;

      // EXTRAER ACTOR
      jumpTwo($pos1, $pos2, $line);
      $actor1 = substr($line, $pos1+1, $pos2-$pos1-1);

      // EXTRAER GENEROS
      jumpTwo($pos1, $pos2, $line);
      $generos = substr($line, $pos1+1, $pos2-$pos1-1);

      // EXTRAER ACTOR 2
      $pos1 = $pos2;
      $pos2 = nextPos($pos2, $line);
      $actor2 = substr($line, $pos1+1, $pos2-$pos1-1);

      //EXTRAER TÍTULO DE LA PELÍCULA
      $pos1 = $pos2;
      $pos2 = nextPos($pos2, $line);
      $titulo= substr($line, $pos1+1, $pos2-$pos1-1);
      // Doblar comillas si las tiene:
      $comilla = strpos($titulo, "'");
      if($comilla)
         $titulo=str_replace("'","''", $titulo);

      // EXTRAER ACTOR 3
      jumpTwo($pos1, $pos2, $line);
      $actor3 = substr($line, $pos1+1, $pos2-$pos1-1);

      // EXTRAER LENGUAJE
      jumpTwo($pos1, $pos2, $line);
      jumpOne($pos1, $pos2, $line);
      $lenguaje = substr($line, $pos1+1, $pos2-$pos1-1);

      // EXTRAER PAÍS
      $pos1 = $pos2;
      $pos2 = nextPos($pos2, $line);
      $pais = substr($line, $pos1+1, $pos2-$pos1-1);

      // EXTRAER AÑO
      jumpTwo($pos1, $pos2, $line);
      $year = substr($line, $pos1+1, $pos2-$pos1-1);
      if($year=="")
         $year=NULL;

      // EXTRAER IMDB_SCORE
      jumpOne($pos1, $pos2, $line);
      $imdbscore = substr($line, $pos1+1, $pos2-$pos1-1);
   }

   // Función para insertar una tupla en la tabla con los datos extraidos.
   function insertIntoTable($db, &$director, &$duration, &$actor1, &$generos, &$actor2, &$titulo, &$actor3, &$lenguaje, &$pais, &$year, &$imdbscore){
      // INSERTAR PELÍCULA
      $res = mysqli_query($db, "INSERT INTO movies(name, year, country, duration, score, img_path) VALUES('$titulo', $year, '$pais', $duration, NULL, '$img')");
      if(!$res){
         echo $titulo, " ", $year, " ", $pais, " ", $duration, "<br>";
         echo mysqli_error($db), "<br>";
      }

      // COMPROBAR SI YA EXISTE EL DIRECTOR. SI NO EXISTE, LO INTRODUCIMOS EN LA TABLA DE DIRECTORES
      if($director != ""){
         $res = mysqli_query($db, "SELECT * FROM directors WHERE director_name='$director'");
         if(!(mysqli_num_rows($res) > 0) )
            mysqli_query($db, "INSERT INTO directors(director_name) VALUES('$director')");


         $res = mysqli_query($db, "SELECT director_id FROM directors WHERE director_name='$director'");
         $tupla = mysqli_fetch_array($res);
         mysqli_query($db, "INSERT INTO directs(director_id, title) VALUES ({$tupla['director_id']}, '$titulo')");
      }


      // PARA CADA ACTOR, COMPROBAMOS SI YA EXISTE EN LA TABLA DE ACTORES. SI NO EXISTEN, LOS INTRODUCIMOS EN ELLA
      if($actor1 != ""){
         $res = mysqli_query($db, "SELECT * FROM actors WHERE actor_name='$actor1'");
         if(!(mysqli_num_rows($res) > 0) )
            mysqli_query($db, "INSERT INTO actors(actor_name) VALUES('$actor1')");

         $res = mysqli_query($db, "SELECT actor_id FROM actors WHERE actor_name='$actor1'");
         $tupla = mysqli_fetch_array($res);
         mysqli_query($db, "INSERT INTO acts(actor_id, title) VALUES ({$tupla['actor_id']}, '$titulo')");
      }

      if($actor1 != ""){
         $res = mysqli_query($db, "SELECT * FROM actors WHERE actor_name='$actor2'");
         if(!(mysqli_num_rows($res) > 0) )
            mysqli_query($db, "INSERT INTO actors(actor_name) VALUES('$actor2')");

         $res = mysqli_query($db, "SELECT actor_id FROM actors WHERE actor_name='$actor2'");
         $tupla = mysqli_fetch_array($res);
         mysqli_query($db, "INSERT INTO acts(actor_id, title) VALUES ({$tupla['actor_id']}, '$titulo')");
      }

      if($actor2 != ""){
         $res = mysqli_query($db, "SELECT * FROM actors WHERE actor_name='$actor3'");
         if(!(mysqli_num_rows($res) > 0) )
            mysqli_query($db, "INSERT INTO actors(actor_name) VALUES('$actor3')");

         $res = mysqli_query($db, "SELECT actor_id FROM actors WHERE actor_name='$actor3'");
         $tupla = mysqli_fetch_array($res);
         mysqli_query($db, "INSERT INTO acts(actor_id, title) VALUES ({$tupla['actor_id']}, '$titulo')");
      }

      // EXRTRAEMEOS LOS GÉNEROS DE LA PELICULA.
      // COMPROBAMOS SI EL GENERO EXISTE EN LA TABLA DE GENEROS. SI NO EXISTE LO INTRODUCIMOS EN ELLA
      if($generos != ""){
         $pos2 = strpos($generos, "|");
         if(!$pos2){ // Sólo tiene un género
            $res = mysqli_query($db, "SELECT * FROM genres WHERE genre_name='$generos'");
            if(!(mysqli_num_rows($res) > 0) )
               mysqli_query($db, "INSERT INTO genres(genre_name) VALUES('$generos')");

            $res = mysqli_query($db, "SELECT genre_id FROM genres WHERE genre_name='$generos'");
            $tupla = mysqli_fetch_array($res);
            mysqli_query($db, "INSERT INTO is_genre(genre_id, title) VALUES ({$tupla['genre_id']}, '$titulo')");
         }
         else{
            $pos1=0;
            $pos2 = strpos($generos, "|");

            while($pos2){
               $genre=substr($generos, $pos1, $pos2-$pos1);

               $res = mysqli_query($db, "SELECT * FROM genres WHERE genre_name='$genre'");
               if(!(mysqli_num_rows($res) > 0) )
                  mysqli_query($db, "INSERT INTO genres(genre_name) VALUES('$genre')");

               $res = mysqli_query($db, "SELECT genre_id FROM genres WHERE genre_name='$genre'");
               $tupla = mysqli_fetch_array($res);
               mysqli_query($db, "INSERT INTO is_genre(genre_id, title) VALUES ({$tupla['genre_id']}, '$titulo')");


               $pos1 = $pos2+1;
               $pos2 = strpos($generos, "|", $pos1);
            }

            $genre=substr($generos, $pos1);
            $res = mysqli_query($db, "SELECT * FROM genres WHERE genre_name='$genre'");
            if(!(mysqli_num_rows($res) > 0) )
               mysqli_query($db, "INSERT INTO genres(genre_name) VALUES('$genre')");

            $res = mysqli_query($db, "SELECT genre_id FROM genres WHERE genre_name='$genre'");
            $tupla = mysqli_fetch_array($res);
            mysqli_query($db, "INSERT INTO is_genre(genre_id, title) VALUES ({$tupla['genre_id']}, '$titulo')");
         }
      }
   }

   // Función para insertar los datos en las tablas
   function fillTables($db){
      $file = fopen("movie_metadata.csv", "r") or die("Imposible abrir el fichero.");
      $line = fgets($file);
      $i=0;
      $img="media/default.jpg";

      while(!feof($file) && $i < 1000){
         $line = fgets($file);

         getData($line, $director, $duration, $actor1, $generos, $actor2, $titulo, $actor3, $lenguaje, $pais, $year, $imdbscore);
         insertIntoTable($db, $director, $duration, $actor1, $generos, $actor2, $titulo, $actor3, $lenguaje, $pais, $year, $imdbscore);

         $i++;
      }
   }

   /****************************************************************************/
   //                                                                          //
   //                                CONSULTAS                                 //
   //                                                                          //
   /****************************************************************************/

   // Valor de retorno: Objeto mysqli_result. Cada fila es una tupla de la tabla movies ordenadas alfabeticamente. Se iterera con "mysqli_fetch_array()". Se puede conocer su tamaño con mysqli_num_rows()
   function getMovies($db){
      return mysqli_query($db, "SELECT * FROM movies ORDER BY name");
   }

   // Valor de retorno: Objeto mysqli_result de los actores que actúan en la peĺicula $movie
   function getActors($db, $movie){
      return mysqli_query($db, "SELECT actors.actor_name FROM actors INNER JOIN acts ON actors.actor_id = acts.actor_id WHERE acts.title = '$movie'");
   }

   // Valor de retorno: Objeto mysqli_result del director de la peĺicula $movie
   function getDirector($db, $movie){
      return mysqli_query($db, "SELECT directors.director_name FROM directors INNER JOIN directs ON directors.director_id = directs.director_id WHERE directs.title = '$movie'");
   }

   // Valor de retorno: Objeto mysqli_result con las películas en las que actúa $actor
   function getMoviesWhichActs($db, $actor){
      return mysqli_query($db, "SELECT acts.title FROM acts INNER JOIN actors ON actors.actor_id = acts.actor_id WHERE actors.actor_name = '$actor'");
   }

   // Valor de retorno: Objeto mysqli_result con las películas que dirige $director
   function getMoviesWhichDirects($db, $director){
      return mysqli_query($db, "SELECT directs.title FROM directs INNER JOIN directors ON directors.director_id = directs.director_id WHERE directors.director_name = '$director'");
   }

   // Valor de retorno: Objeto mysqli_result con las películas del género $genre
   function getMoviesOfGenre($db, $genre){
      return mysqli_query($db, "SELECT is_genre.title FROM is_genre INNER JOIN genres ON is_genre.genre_id = genres.genre_id WHERE genres.genre_name = '$genre'");
   }

   // Valor de retorno: Objeto mysqli_result con los géneros de una película
   function getGenreOfMovie($db, $movie){
      return mysqli_query($db, "SELECT genres.genre_name FROM genres INNER JOIN is_genre ON is_genre.genre_id = genres.genre_id WHERE is_genre.title = '$movie'");
   }

   function endDB($db){
      mysqli_close($db);
   }
?>
