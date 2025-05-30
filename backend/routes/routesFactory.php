<?php
/**
*    File        : backend/routes/routesFactory.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

//despachador de rutas, como el primero que hicimos que se llamaba routesdispatcher
//enruta las peticiones HTTP a los handlers correspondientes
function routeRequest($conn, $customHandlers = [], $prefix = 'handle') //recibe un array de handlers personalizados 
{
    $method = $_SERVER['REQUEST_METHOD'];
    //PHP guarda automáticamente los datos del servidor en el arreglo global $_SERVER

    // Lista de handlers CRUD por defecto
    $defaultHandlers = [
        'GET'    => $prefix . 'Get', //necesita el $prefix para los proximos customizados
        'POST'   => $prefix . 'Post',
        'PUT'    => $prefix . 'Put',
        'DELETE' => $prefix . 'Delete'
    ];

    // Sobrescribir handlers por defecto si hay personalizados
    $handlers = array_merge($defaultHandlers, $customHandlers);

    if (!isset($handlers[$method]))//valida si el metodo es soportado 
    {
        http_response_code(405);
        echo json_encode(["error" => "Método $method no permitido"]);
        return;
    }

    $handler = $handlers[$method]; //guarda el nombre de la funcion que debe ejecutarse segun el metodo solicitado

    if (is_callable($handler)) //si existe una funcion con el nombre del handler, es decir que maneje ese metodo
    {
        $handler($conn);//llama a la funcion que maneja el metodo con la conexion a la base de datos
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "Handler para $method no es válido"]);
    }
}