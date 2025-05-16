<?php 

const BASE_URL = '/practica2/backend/'; //principio de toda url dentro de esta carpeta
const NOT_FOUND = './routes/error/notFound.php'; //ruta para el error 404

function getRoutes () : array //me devuelve un array con todas las rutas posibles
{
    return [
        'api/students' => './routes/studentsRoutes.php' //nombro el elemento dentro del array y le designo el destino(?
        // to do...
    ];
}

function  getParsedPath (string $url) { /* me devuelve el nombre de la ruta
    dentro de esta carpeta, modifica la url que me traje de server.php*/
    return str_replace(BASE_URL, '', $url);
}

function dispatchRoute (string $url) : void  //me dirige a la ruta si es que existe o al error 404
{
    //var_dump($url);
    $availableRoutes = getRoutes();
    $path = getParsedPath($url);

    require_once( $availableRoutes[$path] ?? NOT_FOUND ); //condicional, si la ruta existe va ahí, sino va al not found
}

?>