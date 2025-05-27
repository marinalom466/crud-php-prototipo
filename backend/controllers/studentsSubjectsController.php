<?php
//este archivo hace lo mismo que el studentsController.php pero para la tabla studentsSubjects
//las funciones son identicas excepto por el nombre de la tabla y los nombres de las columnas
require_once("./models/studentsSubjects.php");

function handleGet($conn) 
{
    $result = getAllSubjectsStudents($conn);
    $data = [];
    while ($row = $result->fetch_assoc()) 
    {
        $data[] = $row;
    }
    echo json_encode($data);
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $studentId = $input['student_id'];
    $subjectId = $input['subject_id'];
    $approved = $input['approved'];

    // Verificar si ya existe una asignación con el mismo estudiante y materia
    $stmt = $conn->prepare("SELECT * FROM students_subjects WHERE student_id = ? AND subject_id = ?"); 
    /** 
     * el ? es un placeholder, para valores que se pasarán más adelante
     * el ? es para declarar un parámetro que se pasará más adelante
     * esto ayuda a evitar inyecciones sql ya que solo permite datos de tipo entero
    */ 

    $stmt->bind_param("ii", $studentId, $subjectId); //asociamos los parametros a la consulta preparada
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) //significa que encontró una relación existente
    {
        http_response_code(400);
        echo json_encode(["error" => "Ya existe una relación entre este estudiante y materia"]);
        return;
    }
    // si no existe, procede a crear la asignación

    if (assignSubjectToStudent($conn, $input['student_id'], $input['subject_id'], $input['approved'])) 
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

    if (updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
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
    if (removeStudentSubject($conn, $input['id'])) 
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
