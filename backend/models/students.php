<?php
/**
*    File        : backend/models/students.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

//archivo que maneja, a partir de comando sql, la tabla students, agregar, eliminar, consultar y modificar
//en la nueva version del crud este tambien se mantiene igual
function getAllStudents($conn) 
{
    $sql = "SELECT * FROM students";

    //MYSQLI_ASSOC devuelve un array ya listo para convertir en JSON:
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function getStudentById($conn, $id) 
{
    $stmt = $conn->prepare ("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc(); //devuelve un array asociativo con los datos del estudiante
}

function createStudent($conn, $fullname, $email, $age) 
{
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age);
    $stmt->execute();

    return [
        'inserted' => $stmt->affected_rows, //devuelve la cantidad de filas afectadas
        'id' => $conn->insert_id //devuelve el id del último registro insertado para validar en el controlador
    ];
}

function updateStudent($conn, $id, $fullname, $email, $age) 
{
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    $stmt->execute();
    //se retorna fila afectada para validar en controlador

    return ['updated' => $stmt->affected_rows];
}

function deleteStudent($conn, $id) 
{
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    //se retorna fila adectada para validar en controlador
    return ['deleted'=> $stmt->affected_rows];
}
?>