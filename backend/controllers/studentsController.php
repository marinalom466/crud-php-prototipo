<?php
/**
*    File        : backend/controllers/studentsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

//este archivo se encarga de manejar las peticiones HTTP relacionadas con los estudiantes
require_once("./models/students.php");

function handleGet($conn) {
    $input = json_decode(file_get_contents("php://input"), true);
    //Intenta leer el contenido del cuerpo de la petición (php://input) y convertirlo desde JSON a un array de PHP usando json_decode    
    if (isset($input['id'])) //si el json recibido contiene un campo "id"...
    {
        $student = getStudentById($conn, $input['id']);
        echo json_encode($student); //se devuelve un solo estudiante
    } 
    else {
        $students = getAllStudents($conn);
        echo json_encode($students);
    }
}

function handlePost($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);
    if ($result['inserted']>0) {
        echo json_encode(["message" => "Estudiante agregado correctamente"]);
    } 
    else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo agregar"]);
    }
}

function handlePut($conn) {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated']>0) {
        echo json_encode(["message" => "Actualizado correctamente"]);
    } 
    else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    $studentId = $input['student_id'];
    $stmt = $conn->prepare("SELECT 1 FROM students_subjects WHERE student_id = ? "); //esto hay que cambiarlo, no puede tener consultas sql
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(["error" => "No se puede eliminar porque el estudiante tiene materias asignadas"]);
        return;
    }

    $result = deleteStudent($conn, $input['id']); //reuso la variable result
    // Si no hay materias asignadas, procede a eliminar el estudiante
    if ($result['deleted']>0) {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>