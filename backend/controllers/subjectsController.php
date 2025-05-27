<?php
//este archivo hace lo mismo que el studentsController.php pero para la tabla subjects
//las funciones son identicas excepto por el nombre de la tabla y los nombres de las columnas
//se podrán estandarizar de alguna forma?
require_once("./models/subjects.php");

function handleGet($conn) 
{
    if (isset($_GET['id'])){
        $result = getSubjectById($conn, $_GET['id']);
        echo json_encode($result->fetch_assoc());
    } else{
        $result = getAllSubjects($conn);
        $data = [];
        while ($row = $result->fetch_assoc()) 
        {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $nombre = $input['name'];
    //stmt es una variable que contiene la consulta sql preparada
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE  LOWER(name) = LOWER(?)"); //ve
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

    if (createSubject($conn, $input['name']))  //si no existe crearla
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
    if (updateSubject($conn, $input['id'], $input['name'])) 
    {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
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

    $subjectId = $input['subject_id'];
    $stmt = $conn->prepare("SELECT 1 FROM students_subjects WHERE subject_id = ?"); 
    //hace select 1 y no select * porque no importa que columnas tenga la tabla, solo nos interesa si hay o no registros con ese id
    $stmt->bind_param("i", $subjectId); //asociamos el parametro a la consulta preparada
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) //significa que encontró una relación existente
    {
        http_response_code(400);
        echo json_encode(["error" => "No se puede eliminarla materia porque hay estudiantes asignados a esta materia"]);
        return;
    }
    // si no hay estudiantes asignados, procede a eliminar la materia
    if (deleteSubject($conn, $input['id'])) 
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