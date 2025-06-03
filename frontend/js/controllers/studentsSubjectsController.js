/**
*    File        : frontend/js/controllers/studentsSubjectsController.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

import { studentsAPI } from '../api/studentsAPI.js';
import { subjectsAPI } from '../api/subjectsAPI.js';
import { studentsSubjectsAPI } from '../api/studentsSubjectsAPI.js'; //no lo usamos pero lo importamos para que no de error
//este archivo es el que se encarga de cargar los estudiantes y las materias, y de crear la relación entre ellos

document.addEventListener('DOMContentLoaded', () => 
{
    loadRelations();
    initSelects();//Se cargan los <select> con estudiantes y materias.
    setupFormHandler();
    setupCancelHandler(); //para que al hacer click en el botón cancelar se limpie el formulario
    
});

async function initSelects() //Carga estudiantes y materias en sus respectivos <select>.
{
    try 
    {
        // Cargar estudiantes
        const students = await studentsAPI.fetchAll();
        const studentSelect = document.getElementById('studentIdSelect');
        students.forEach(s => 
        {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = s.fullname;
            studentSelect.appendChild(option);
        });

        // Cargar materias
        const subjects = await subjectsAPI.fetchAll();
        const subjectSelect = document.getElementById('subjectIdSelect');
        subjects.forEach(sub => 
        {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;
            subjectSelect.appendChild(option);
        });
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes o materias:', err.message);
    }
}

function setupFormHandler() 
{
    const form = document.getElementById('relationForm');
    form.addEventListener('submit', async e => 
    {
        e.preventDefault();

        const relation = getFormData();//lo llama para armar el objeto

        try 
        {
            if (relation.id) //si hay id hace update
            {
                await studentsSubjectsAPI.update(relation);
            } 
            else 
            {   
                //traer todas las relaciones actuales
                const allRelations = await studentsSubjectsAPI.fetchAll();
                //buscar si ya existe una relación con ese estudiante y esa materia
                const alreadyExists = allRelations.some(r => r.student_id === relation.student_id && r.subject_id === relation.subject_id);

                if (alreadyExists)
                {
                    alert('La relación entre ese estudiante y materia ya existe.');
                    return; //frena la ejecución si ya existe una relación con ese estudiante y esa materia
                }

                await studentsSubjectsAPI.create(relation);
            }
            clearForm(); //limpia el formulario
            loadRelations();//recarga la tabla
        } 
        catch (err) 
        {
            console.error('Error guardando relación:', err.message);
        }
    });
}

function setupCancelHandler()//Resetea el formulario 
{
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', () => 
    {
        document.getElementById('relationId').value = '';//limpia el campo oculto relationId
    });
}

function getFormData() 
{
    return{
        id: document.getElementById('relationId').value.trim(),
        student_id: document.getElementById('studentIdSelect').value,
        subject_id: document.getElementById('subjectIdSelect').value,
        approved: document.getElementById('approved').checked ? 1 : 0 
        //Convierte el checkbox a entero (1 o 0), para facilitar la compatibilidad con el backend (MySQL no tiene true/false).

    };
}

function clearForm() 
{
    document.getElementById('relationForm').reset();
    document.getElementById('relationId').value = '';//limpia el campo oculto relationId
}

async function loadRelations() 
{
    try 
    {
        const relations = await studentsSubjectsAPI.fetchAll();
        
        /**
         * DEBUG
         */
        //console.log(relations);

        /**
         * En JavaScript: Cualquier string que no esté vacío ("") es considerado truthy.
         * Entonces "0" (que es el valor que llega desde el backend) es truthy,
         * ¡aunque conceptualmente sea falso! por eso: 
         * Se necesita convertir ese string "0" a un número real 
         * o asegurarte de comparar el valor exactamente. 
         * Con el siguiente código se convierten todos los string approved a enteros.
         */
        relations.forEach(rel => 
        {
            rel.approved = Number(rel.approved);//convierte el campo approved a numero real
        }); //evita que "0"(string) sea considerado truthy.
        
        renderRelationsTable(relations);
    } 
    catch (err) 
    {
        console.error('Error cargando inscripciones:', err.message);
    }
}

function renderRelationsTable(relations) 
{
    const tbody = document.getElementById('relationTableBody');
    tbody.replaceChildren();

    relations.forEach(rel =>    
    {
        const tr = document.createElement('tr'); //DOM seguro

        // tr.appendChild(createCell(rel.fullname || rel.student_id));old
        tr.appendChild(createCell(rel.student_fullname));
        // tr.appendChild(createCell(rel.name || rel.subject_id));old
        tr.appendChild(createCell(rel.subject_name));
        tr.appendChild(createCell(rel.approved ? 'Sí' : 'No'));
        tr.appendChild(createActionsCell(rel));

        tbody.appendChild(tr);
    });
}

function createCell(text) 
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createActionsCell(relation) 
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(relation));

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(relation.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

function fillForm(relation)
//Llena el formulario con los datos de una relación seleccionada para edición.
 
{
    document.getElementById('relationId').value = relation.id;
    document.getElementById('studentIdSelect').value = relation.student_id;
    document.getElementById('subjectIdSelect').value = relation.subject_id;
    document.getElementById('approved').checked = !!relation.approved;
}

async function confirmDelete(id) //Confirma el borrado con window.confirm()
{
    if (!confirm('¿Estás seguro que deseas borrar esta inscripción?')) return;

    try 
    {
        await studentsSubjectsAPI.remove(id);
        loadRelations();
    } 
    catch (err) 
    {
        console.error('Error al borrar inscripción:', err.message);
    }
}
