function checkUser(){
   var xmlhttp = new XMLHttpRequest();
   var username = document.getElementById("user").value;
   var password = document.getElementById("pass").value;
   xmlhttp.open("POST", "entrar.php");

   console.log("Hola");
   xmlhttp.onreadystatechange = function() {
      if(xmlhttp.readystate == 4 && xmlhttp.status == 200){
         document.getElementById("mensajeerror").innerHTML = xmlhttp.responseText;
         console.log(xmlhttp.responseText);
      }
      else
         console.log("Adios");
   };

   xmlhttp.send(username, password);
}
