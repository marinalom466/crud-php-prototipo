<?php
/**
*    File        : backend/server.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

/** FOR DEBUG:
 * ini_set('display_errors', 1);  indica a php que muestre los errores directamente por pantalla
 * ini_set('display_startup_errors', 1); muestra errores de inicio de php, como problemas de configuracion
 * error_reporting(E_ALL);  muestra todos los tipos de errores 
 */

header("Access-Control-Allow-Origin: *"); /** permite que cualquier sitio web se comunique con este servidor
  * en prod * se suele reemplazar por una dirección específica  
  * es necesario cuando el frontend y backend no estan en el mismo dominio o puerto 
  */

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); //indica al navegador los metodos http aceptados por el bcend
header("Access-Control-Allow-Headers: Content-Type"); /** permite las solicitudes con ciertos encabezados
*ese encabezado solo permite el nombre del encabezado, no sus valores
*el tipo de contenido se chequea en el backend
*/

function sendCodeMessage($code, $message = "") //manda el mensaje sea cual sea el error
{
    http_response_code($code);
    echo json_encode(["message" => $message]);
    exit();
}

//respuesta correcta para solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { /** el metodo options atiende preflights requests del navegador
  *It is an HTTP request of the OPTIONS method, sent before the request itself, 
  *in order to determine if it is safe to send it.
 */
    sendCodeMessage(200);
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

  /** 
  *require_once("./routeDispatcher.php");
  *dispatchRoute( $_SERVER['REQUEST_URI'] ); //funcion dentro del archivo de arriba, le mando la url obtenida
  *como ahora hago el enrutamiento en este mismo archivo, ya no necesito el dispatcher
  */



// Obtener el módulo desde la query string
$uri = parse_url($_SERVER['REQUEST_URI']); //crea una matriz asociativa con todos los componentes de la url (el parametro que tiene)
$query = $uri['query'] ?? ''; //le doy a query el valor de la clave query de la matriz asociativa
//si no existe le doy un string vacio 
parse_str($query, $query_array);// Parsear la query string para obtener el módulo
//parsea la cadena de consulta y la convierte en un array asociativo
$module = $query_array['module'] ?? null;//obtenemos el valor de la clave module, si no existe le doy null

// Validación de existencia del módulo
if (!$module)
{
    sendCodeMessage(400, "Módulo no especificado");
}

// Validación de caracteres seguros: solo letras, números y guiones bajos
if (!preg_match('/^\w+$/', $module))// devuelve la cantidad de matcheos o falso
{
    sendCodeMessage(400, "Nombre de módulo inválido");
}

// Buscar el archivo de ruta correspondiente
$routeFile = __DIR__ . "/routes/{$module}Routes.php"; //se podrá hacer a partir de renombramientos como haciamos en el routedispatcher? 

if (file_exists($routeFile))
{
    require_once($routeFile); //incluye el archivo y ejecuta el código dentro de él
} else{
    sendCodeMessage(404, "Ruta para el módulo '{$module}' no encontrada"); //reemplaza el archivo de errores que teniamos
}

/**
 * el parseo que haciamos nosotros antes era parecido a este, pero este sin tocar el router
 * por eso no necesita el archivo .htaccess 
 * el archivo .htaccess es un archivo de configuracion del servidor web apache
 * ademas no usabamos el _DIR_ y definiamos una variable para el pricipio de todas las rutas
 */

?>