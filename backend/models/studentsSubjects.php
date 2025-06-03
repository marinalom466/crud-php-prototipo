<?php
/**
*    File        : backend/models/studentsSubjects.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

// archivo que maneja, a partir de comando sql, la tabla students_subjects, agregar, eliminar, consultar y modificar
//Las funciones usan sentencias preparadas: buena práctica de seguridad.
function assignSubjectToStudent($conn, $student_id, $subject_id, $approved) 
{
    $sql = "INSERT INTO students_subjects (student_id, subject_id, approved) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $subject_id, $approved);
    $stmt->execute();

    return[
        'inserted' => $stmt->affected_rows, // devuelve la cantidad de filas afectadas
        'id' => $conn->insert_id // devuelve el id del último registro insertado para validar en el controlador
    ];
}

//query escrita sin ALIAS
function getAllSubjectsStudents($conn) 
{
    $sql = "SELECT students_subjects.id,
                students_subjects.student_id,
                students_subjects.subject_id,
                students_subjects.approved,
                students.fullname AS student_fullname,
                subjects.name AS subject_name
            FROM students_subjects
            JOIN subjects ON students_subjects.subject_id = subjects.id
            JOIN students ON students_subjects.student_id = students.id";

    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC); // devuelve un array asociativo con todos los registros
}

//query escrita con ALIAS
function getSubjectsByStudent($conn, $student_id) 
{
    $sql = "SELECT ss.subject_id, s.name, ss.approved
        FROM students_subjects ss
        JOIN subjects s ON ss.subject_id = s.id
        WHERE ss.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);//trae mas de una materia por estudiante en un array asociativo 
    //fetch_assoc() en getSubjectsByStudent() puede limitar resultados si hay más de una materia asignada.
}

function updateStudentSubject($conn, $id, $student_id, $subject_id, $approved) 
{
    $sql = "UPDATE students_subjects 
            SET student_id = ?, subject_id = ?, approved = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $student_id, $subject_id, $approved, $id);
    $stmt->execute();

    return ['updated' => $stmt->affected_rows];
}

function removeStudentSubject($conn, $id) 
{
    $sql = "DELETE FROM students_subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return ['deleted' => $stmt->affected_rows];
}

function relationAlreadyExists($conn, $subject_id, $student_id) 
{
    $stmt = $conn->prepare("SELECT 1 FROM students_subjects WHERE subject_id = ? AND student_id = ?"); 
    //el ? es un placeholder, para declarar un parámetro que se pasará más adelante
    $stmt->bind_param("ii", $subject_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0; // devuelve true si ya existe una relación entre el estudiante y la materia
}

?>