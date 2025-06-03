<?php
/**
*    File        : backend/controllers/subjectsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

//este archivo hace lo mismo que el studentsController.php pero para la tabla subjects
//las funciones son identicas excepto por el nombre de la tabla y los nombres de las columnas
//se podrán estandarizar de alguna forma?
require_once("./models/subjects.php");

function handleGet($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['id']))
    {
        $subject = getSubjectById($conn, $input['id']);
        echo json_encode($subject);
    } 
    else{
        $subjects = getAllSubjects($conn);
        echo json_encode($subjects);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $nombre = $input['name'];

    /**
     * no necesito hacer una consulta para ver si existe una materia con ese nombre
     * porque lo hace la misma base de datos al definir el campo name como UNIQUE
     */
    /*
    stmt es una variable que contiene la consulta sql preparada
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE  LOWER(name) = LOWER(?)"); 
    //la funcion lower convierte el texto a minusculas para evitar problemas de mayusculas y minusculas
    $stmt->bind_param("s", $nombre);//ayuda a evitar inyecciones sql ya que solo permite datos de tipo string
    $stmt->execute();
    $result = $stmt->get_result(); //objeto de tipo mysqli , permite acceder a los resultados de la consulta

    if ($result->num_rows > 0) //encontró una materia con ese nombre (en realidad una coincidencia)
    {
        http_response_code(400);
        echo json_encode(["error" => "Ya existe una materia con ese nombre"]);
        return;
    }
    */

    $result = createSubject($conn, $nombre); 
    if ($result['inserted']>0)  //si no existe crearla
    {
        echo json_encode(["message" => "Materia creada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo crear"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    $result = updateSubject($conn, $input['id'], $input['name']);
    if ($result['updated']>0) 
    {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) //hay q cambiarlo, no puede hacer consultas sql
{
    $input = json_decode(file_get_contents("php://input"), true);

    $subjectId = $input['subject_id'];

    if (subjectHasStudents($conn, $subjectId)) //significa que encontró una relación existente
    {
        http_response_code(400);
        echo json_encode(["error" => "No se puede eliminarla materia porque hay estudiantes asignados a esta materia"]);
        return;
    }
    // si no hay estudiantes asignados, procede a eliminar la materia
    $result = deleteSubject($conn, $input['id']);
    if ($result['deleted']>0) 
    {
        echo json_encode(["message" => "Materia eliminada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>