//fabrica que genera un objeto para interactuar con una api backend con un modulo especifico

export function createAPI(moduleName, config = {}) //la config va vacia porque no se usa 
//el config se usa en el caso de que se quiera cambiar la url del backend
{
    const API_URL = config.urlOverride ?? `../../backend/server.php?module=${moduleName}`;
    //el ?? es un operador de coalescencia nula, si no existe la urlOverride se usa la url por defecto
    //se queda con lo primero que no es null
    async function sendJSON(method, data) 
    {
        const res = await fetch(API_URL,
        {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (!res.ok) throw new Error(`Error en ${method}`);
        return await res.json();
    }

    return {
        async fetchAll()
        {
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