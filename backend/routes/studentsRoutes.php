<?php
/**
*    File        : backend/routes/studentsRoutes.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/studentsController.php");

//Actúa como un "puente" entre la solicitud que llega del frontend y el controlador que se encarga de procesarla

routeRequest($conn);

/**
 * switch ($_SERVER['REQUEST_METHOD']) ya no es necesario
 * su funcion es la misma que la de routesFactory
 * es apto a handlers customizados 
 */

 
/**
 * Ejemplo de como se extiende un archivo de rutas 
 * para casos particulares o validaciones:
 */

/* routeRequest($conn, [
    'POST' => function($conn) //si la solicitud http es post, sucede esta validacion
    {
        // Validación o lógica extendida
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input['fullname'])) 
        {
            http_response_code(400);
            echo json_encode(["error" => "Falta el nombre"]);//esto es solo en caso de que no hayan puesto el nombre
            return;
        }
        handlePost($conn);
    }
]);
*/
?>