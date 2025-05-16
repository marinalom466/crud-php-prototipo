<?php
//PREGUNTAR POR EL .HTACCESS
// darle acceso a ciertas rutas del servidor, para
/**
 * DEBUG MODE
 */
ini_set('display_errors', 1);// indica a php que muestre los errores directamente por pantalla
error_reporting(E_ALL);  //muestra todos los tipos de errores 

header("Access-Control-Allow-Origin: *"); /** permite que cualquier frontend, desde cualquier origen *,
  *pueda acceder al backend. es peligroso en temas de seguridad esto o no tiene que ver?  
  *a que se refiere con front end? al lenguaje? */
  /* es necesario cuando el frontend y backend no estan en el mismo dominio o puerto */

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); //indica al navegador los metodos http aceptados por el bcend
header("Access-Control-Allow-Headers: Content-Type"); /** permite las solicitudes con ciertos encabezados
*ese encabezado solo permite el nombre del encabezado, no sus valores
*el tipo de contenido se chequea en el backend*/

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { /* el metodo options atiende preflights requests del navegador
  It is an HTTP request of the OPTIONS method, sent before the request itself, 
  in order to determine if it is safe to send it.
 */
    http_response_code(200);
    exit();
}

// require_once("./routes/studentsRoutes.php"); 
/** incluye el archivo que define las RUTAS o la lógica que responderá a la peticion actual
  * usa require once para incluirlo una sola vez, lo que evita errores por multiples inclusiones
 */
/* este archivo debería analizar la URL, metodo y decidir qué controlador invocar. controlador?
 */

 /** tiene que estar preparado para modulos futuros
 *analizando la URL y decidiendo qué archivo de ruta invocar usando alguna convencion
 *un switch?
  */
  require_once("./routeDispatcher.php");
  dispatchRoute( $_SERVER['REQUEST_URI'] ); //funcion dentro del archivo de arriba, le mando la url obtenida

?>