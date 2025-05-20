<?php
//enruta las peticiones HTTP a los handlers correspondientes
function routeRequest($conn, $customHandlers = [], $prefix = 'handle') //recibe un array de handlers personalizados 
{
    $method = $_SERVER['REQUEST_METHOD'];

    // Lista de handlers CRUD por defecto
    $defaultHandlers = [
        'GET'    => $prefix . 'Get', //no entiendo porque no puede usar handle y necesita el $prefix
        'POST'   => $prefix . 'Post',
        'PUT'    => $prefix . 'Put',
        'DELETE' => $prefix . 'Delete'
    ];

    // Sobrescribir handlers por defecto si hay personalizados
    $handlers = array_merge($defaultHandlers, $customHandlers);

    if (!isset($handlers[$method]))//si no existe el metodo en este array 
    {
        http_response_code(405);
        echo json_encode(["error" => "Método $method no permitido"]);
        return;
    }

    $handler = $handlers[$method];

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