<?php
/**
*    File        : backend/controllers/studentsSubjectsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

//este archivo hace lo mismo que el studentsController.php pero para la tabla studentsSubjects
//las funciones son identicas excepto por el nombre de la tabla y los nombres de las columnas
require_once("./models/studentsSubjects.php");

function handleGet($conn) 
{
    $studentsSubjects = getAllSubjectsStudents($conn);
    echo json_encode($studentsSubjects);
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    /**
     * esto ya funciona por el json, pero como no es correcto hacer una consulta sql en los controladores,
     * necesitamos validar que los datos que vienen del json son correctos de otra forma
    */
    
    $studentId = $input['student_id'];
    $subjectId = $input['subject_id'];
    $approved = $input['approved'];

    /** 
     * el ? es un placeholder, para declarar un parámetro que se pasará más adelante
     * esto ayuda a evitar inyecciones sql ya que solo permite datos de tipo entero
    */ 

    if (relationAlreadyExists($conn, $subjectId, $studentId)) //significa que encontró una relación existente
    {
        http_response_code(400);
        echo json_encode(["error" => "Ya existe una relación entre este estudiante y materia"]);
        return;
    }
    
    // si no existe, procede a crear la asignación
    $result = assignSubjectToStudent($conn, $studentId, $subjectId, $approved);
    if ($result['inserted']>0) 
    {
        echo json_encode(["message" => "Asignación realizada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    $result = updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved']);
    if ($result['updated']>0) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = removeStudentSubject($conn, $input['id']);
    if ($result['deleted']>0) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
