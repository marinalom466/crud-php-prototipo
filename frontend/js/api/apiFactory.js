/**
*    File        : frontend/js/api/apiFactory.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

//fabrica que genera un objeto para interactuar con una api backend con un modulo especifico

export function createAPI(moduleName, config = {}) //la config va vacia porque no se usa 
//el config se usa en el caso de que se quiera cambiar la url del backend
{
    const API_URL = config.urlOverride ?? `../../backend/server.php?module=${moduleName}`;
    //el ?? es un operador de coalescencia nula, si no existe la urlOverride se usa la url por defecto (la que tiene a la derecha)
    //se queda con lo primero que no es null
    async function sendJSON(method, data) //metodos http post, put o delete
    //este metodo se encarga de enviar los datos al backend, recibe el metodo y los datos
    {
        const res = await fetch(API_URL, //method get
        //el uso de await indica que se espera la respuesta antes de seguir
        {
            method,
            headers: { 'Content-Type': 'application/json' }, //indica que se envía json
            body: JSON.stringify(data)
            //body convierte los datos a texto json antes de enviarlos
        });

        if (!res.ok) throw new Error(`Error en ${method}`);
        return await res.json();
    }

    return { //esta es la devolucion de la funcion entera
        //devuelve un objeto con las funciones que se pueden usar para interactuar con la api
        async fetchAll()
        {//obtiene todos los registros del modulo desde el servidor
            const res = await fetch(API_URL);
            if (!res.ok) throw new Error("No se pudieron obtener los datos");
            return await res.json();
        },
        async create(data)
        {
            return await sendJSON('POST', data);
        },
        async update(data)
        {
            return await sendJSON('PUT', data);
        },
        async remove(id)
        {
            return await sendJSON('DELETE', { id });
        }
    };
}