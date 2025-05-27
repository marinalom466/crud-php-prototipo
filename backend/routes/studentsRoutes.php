<?php
require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/studentsController.php");

// routeRequest($conn);

/**
 * switch ($_SERVER['REQUEST_METHOD']) ya no es necesario
 * su funcion es la misma que la de routesFactory
 * es apto a handlers customizados 
 */

 
/**
 * Ejemplo de como se extiende un archivo de rutas 
 * para casos particulares
 * o validaciones:
 */

routeRequest($conn, [
    'POST' => function($conn) 
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

/**
 * si no llamo al routeRequest con el post concretamente que es lo que hace?
*/
?>