/**
*    File        : frontend/js/controllers/subjectsController.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

import { subjectsAPI } from '../api/subjectsAPI.js';
import { studentsSubjectsAPI } from '../api/studentsSubjectsAPI.js';  //lo necesito para no borrar materias que tengan estudiantes asociados
//hacemos uso de las funciones de la apifactory y creas el objeto

/**
 * los nombres de las funciones son los mismos que los de la apiFactory
 * son medio autoexplicativas 
 * 
 * las funciones de este archivoy sus descripciones son basicamente iguales a las de studentscontroller.js
 * pero referido a las materias
*/

document.addEventListener('DOMContentLoaded', () => 
{
    loadSubjects();
    setupSubjectFormHandler();
    setupCancelHandler();
});

function setupSubjectFormHandler() 
{
  const form = document.getElementById('subjectForm');
  form.addEventListener('submit', async e => 
  {
        e.preventDefault();
        const subject = 
        {
            id: document.getElementById('subjectId').value.trim(),
            name: document.getElementById('name').value.trim()
        };

        try 
        {
            if (subject.id) 
            {
                await subjectsAPI.update(subject);
            }
            else
            {
                const existingSubjects = await subjectsAPI.fetchAll(); //se trae todas las materias existentes
                const nameAlreadyExists = existingSubjects.some(s => s.name.toLowerCase() === subject.name.toLowerCase());
                //some es un metodo que recorre el arreglo de materias y compara el nombre de cada uno


                /**
                 * s es cada materia existente en el array existingSubjects
                 * s.name es el nombre de esa materia existente
                 * subject.name es el nombre de la materia que queres agregar
                 */
                if (nameAlreadyExists)
                {
                    alert('Ya existe una materia con ese nombre.');
                    return; //frena la ejecución si ya existe una materia con ese nombre
                }

                await subjectsAPI.create(subject);
            }
            
            form.reset();
            document.getElementById('subjectId').value = '';
            loadSubjects();
        }
        catch (err)
        {
            console.error(err.message);
        }
  });
}

function setupCancelHandler()
{
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', () => 
    {
        document.getElementById('subjectId').value = '';
    });
}

async function loadSubjects()
{
    try
    {
        const subjects = await subjectsAPI.fetchAll();
        renderSubjectTable(subjects);
    }
    catch (err)
    {
        console.error('Error cargando materias:', err.message);
    }
}

function renderSubjectTable(subjects)
{
    const tbody = document.getElementById('subjectTableBody');
    tbody.replaceChildren();

    subjects.forEach(subject =>
    {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(subject.name));
        tr.appendChild(createSubjectActionsCell(subject));

        tbody.appendChild(tr);
    });
}

function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createSubjectActionsCell(subject)
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => 
    {
        document.getElementById('subjectId').value = subject.id;
        document.getElementById('name').value = subject.name;
    });

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDeleteSubject(subject.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

async function confirmDeleteSubject(id) //este funciona
{
    if (!confirm('¿Seguro que deseas borrar esta materia?')) return;

    try
    {
        const existingRelations = await studentsSubjectsAPI.fetchAll();
        const SubjectIsRelated = existingRelations.some(rel => rel.id === id);

        if (SubjectIsRelated)
        {
            alert('No se puede borrar la materia porque tiene estudiantes asociados.');
            return; //frena la ejecución si la materia tiene estudiantes asociados
        }
        //si no hay estudiantes inscritos, procede a borrar la materia
        await subjectsAPI.remove(id);
        loadSubjects();
    }
    catch (err)
    {
        console.error('Error al borrar materia:', err.message);
    }
}
