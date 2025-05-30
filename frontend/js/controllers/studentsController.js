//este archivo maneja la logica de vista del modulo de estudiantes
//crea y actualiza el estado de todo elemento html dinamicamente
import { studentsAPI } from '../api/studentsAPI.js'; //lo importamos para usar las funciones del apifactory, para comunicarse con el backend
import { studentsSubjectsAPI } from '../api/studentsSubjectsAPI.js'; //lo necesito para no borrar estudiantes que tengan materias asociadas


document.addEventListener('DOMContentLoaded', () => 
{//cuando el navegador termina de cargar toda la estructura de la pagina (DOMContentLoaded), sigue:
    loadStudents(); //carga y muestra todos los estudiantes de la tabla
    setupFormHandler(); //prepara el formulario para que al hacer clic en "guardar" se capture y procese la informacion
    setupCancelHandler(); //configura el comportamiento del boton cancelar
});
  
function setupFormHandler()//configurar el envio del formulario
{
    const form = document.getElementById('studentForm');//se busca el form con dicho id desde el html
    form.addEventListener('submit', async e => 
    {
        e.preventDefault(); //cancela el comportamiento por defecto del navegador
        //evita que se recargue la pagina al enviar el formulario
        const student = getFormData(); //extrae los datos del formulario
    
        try //se intenta enviar los datos del estudiante al backend
        {
            if (student.id) //si hay id, es porque se esta editando un estudiante
            {
                await studentsAPI.update(student);
            } 
            else //si no hay, se crea
            {
                await studentsAPI.create(student);
            }
            clearForm(); //se limpian los campos del form
            loadStudents(); //se recarga la tabla de estudiantes para ver los cambios
        }
        catch (err) //si algo en el blque try atrapa una excepcion, se atrapa aca y se envia por consola
        {
            console.error(err.message);
        }
    });
}

function setupCancelHandler() //configura la cancelacion del formulario
{
    const cancelBtn = document.getElementById('cancelBtn'); 
    //se obtiene el boton de cancelar por el id que tiene en el html
    cancelBtn.addEventListener('click', () => 
    {
        document.getElementById('studentId').value = '';
    });//Cuando el botón se hace clic, se borra el valor del campo oculto studentId.
    //evita errores al cancelar una modificacion
}
  
function getFormData() //obtiene datos del form
{//se crea un objeto JS (data object) con los datos del formulario
    return {
        id: document.getElementById('studentId').value.trim(),
        fullname: document.getElementById('fullname').value.trim(),
        email: document.getElementById('email').value.trim(),
        age: parseInt(document.getElementById('age').value.trim(), 10)
    };
}
  
function clearForm() //limpia el form
{
    document.getElementById('studentForm').reset();//reset limpia todos los campos
    document.getElementById('studentId').value = ''; //borra manualmente el campo de studentid
}
  
async function loadStudents() //carga estudiantes desde el backend
{
    try 
    {
        const students = await studentsAPI.fetchAll();
        renderStudentTable(students); //se muestran por pantalla pasando por parametro los datos de estudiantes recuperados de la api
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes:', err.message);
    }
}
  
function renderStudentTable(students)//este crea/actualiza la tabla celda por celda cada vez que recarga la pagina
{ //es lo genera el efecto de dinamismo de la tabla
    const tbody = document.getElementById('studentTableBody');
    tbody.replaceChildren(); //elimina las filas anteriores para empezar desde cero
  
    students.forEach(student => 
    {
        const tr = document.createElement('tr');
    
        tr.appendChild(createCell(student.fullname));
        tr.appendChild(createCell(student.email));
        tr.appendChild(createCell(student.age.toString()));
        tr.appendChild(createActionsCell(student));
        //celdas para cada uno de los campos, y una adicional para los botones
    
        tbody.appendChild(tr); //agrega la fila completa a la tabla
    });
}
  
function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text; //usa textcontent para seguridad, bloque entradas html
    return td;
}
  
function createActionsCell(student)
{
    const td = document.createElement('td');
  
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(student)); //al hacer click llama a fillform oara llenar el form con los datos seleccionados
  
    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(student.id));//pide confirmacion y si la obtiene llama a confirmdelete
  
    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}
  
function fillForm(student) //como indica el nombre, carga los datos en el form
{ //Esta función copia los datos del estudiante seleccionado en el formulario, para permitir su edición.
    document.getElementById('studentId').value = student.id;
    document.getElementById('fullname').value = student.fullname;
    document.getElementById('email').value = student.email;
    document.getElementById('age').value = student.age;
}

async function confirmDelete(id) 
{
    if (!confirm('¿Estás seguro que deseas borrar este estudiante?')) return;
  
    try 
    {
        const existingRelations = await studentsSubjectsAPI.fetchAll();
        const StudentIsRelated = existingRelations.some(rel => rel.id === id);
        //console.log('StudentIsRelated:', StudentIsRelated);
        if (StudentIsRelated)
        {
            alert('No se puede borrar el estudiante porque tiene materias asociadas.');
            return; //frena la ejecución si el estudiante tiene materias asociadas
        }
        //si no, procede a borrar el estudiante
        await studentsAPI.remove(id);//llama para borrar en el backend
        loadStudents();
    } 
    catch (err) 
    {
        console.error('Error al borrar:', err.message);
    }
}
  
