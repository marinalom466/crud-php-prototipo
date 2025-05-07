<?php
/**
 * DEBUG MODE
 */
ini_set('display_errors', 1);// indica a php que muestre los errores directamente por pantalla
error_reporting(E_ALL);  //muestra todos los tipos de errores 

header("Access-Control-Allow-Origin: *"); /* permite que cualquier frontend, desde cualquier origen *,
             pueda acceder al backend. es peligroso en temas de seguridad esto o no tiene que ver?  
             a que se refiere con front end? al lenguaje? */
             /* es necesario cuando el frontend y backend no estan en el mismo dominio o puerto */
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); //indica al navegador los metodos http aceptados por el bcend
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once("./routes/studentsRoutes.php"); 
/* 
   usa require once para incluirlo una sola vez, lo que evita errores por multiples inclusiones
 */
?>