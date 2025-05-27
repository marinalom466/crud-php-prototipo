import { subjectsAPI } from '../api/subjectsAPI.js';
//hacemos uso de las funciones de la apifactory y creas el objeto

/**
 * los nombres de las funciones son los mismos que los de la apiFactory
 * son medio autoexplicativas pero debería igual leerlas bien por dentro para entender la logica de cada una
*/

document.addEventListener('DOMContentLoaded', () => 
{
    loadSubjects();
    setupSubjectFormHandler();
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

async function confirmDeleteSubject(id)
{
    if (!confirm('¿Seguro que deseas borrar esta materia?')) return;

    try
    {
        const existingRelations = await subjectsAPI.fetchAll();
        const SubjectIsRelated = existingRelations.some(rel => rel.subject_id === id);

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
