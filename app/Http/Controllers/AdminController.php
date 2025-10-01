<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Solicitud;
use App\Models\Recoleccion;
use Illuminate\Http\Request; // ← Import necesario para los type-hints de Request

/**
 * Controlador: AdminController
 * -----------------------------------------------------------------------------
 * Responsable de las funcionalidades exclusivas del rol ADMIN dentro de EcoGestión.
 * 
 * Funcionalidades actuales:
 *  - dashboard(): Muestra métricas globales (usuarios, solicitudes, recolecciones).
 *  - users():     Lista paginada de usuarios (vista informativa por ahora).
 *  - manageUsers(): Pantalla para gestionar usuarios (buscar/editar/eliminar).
 *  - searchUser():  Busca un usuario por ID o por nombre (username parcial).
 *  - updateUser():  Actualiza nombre/email/rol de un usuario.
 *  - deleteUser():  Elimina un usuario existente.
 * 
 * NOTAS:
 *  - El acceso a estas rutas está protegido por el Gate 'admin-only' definido
 *    en App\Providers\AppServiceProvider::boot(), y además por middleware
 *    en routes/web.php (Route::middleware('can:admin-only')).
 *  - Este controlador no realiza verificación de permisos por método porque ya
 *    se encuentra detrás del middleware; si se desea mayor robustez, puede
 *    añadirse Gate::authorize('admin-only') al inicio de cada método.
 */
class AdminController extends Controller
{
    /**
     * Panel de administración con métricas globales.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        // Contadores simples para mostrar un resumen general al administrador.
        $usuarios      = User::count();
        $solicitudes   = Solicitud::count();
        $recolecciones = Recoleccion::count();

        // Renderiza la vista principal del panel admin con las métricas agregadas.
        return view('admin.dashboard', compact('usuarios','solicitudes','recolecciones'));
    }

    /**
     * Listado paginado de usuarios (visión global para el admin).
     * Por ahora sirve como vista informativa; la gestión puntual se hace en manageUsers().
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function users()
    {
        // Paginación básica de usuarios (10 por página).
        $usuarios = User::paginate(10);

        // Renderiza la tabla con la lista de usuarios.
        return view('admin.users', compact('usuarios'));
    }

    /**
     * Pantalla de gestión de usuarios (buscar, editar, eliminar).
     * Carga el formulario de búsqueda; si viene un $user desde searchUser(),
     * la misma vista mostrará el bloque de edición/eliminación.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function manageUsers()
    {
        // Solo retorna la vista; la variable $user se inyectará cuando exista resultado de búsqueda.
        return view('admin.manage-users');
    }

    /**
     * Buscar un usuario por ID exacto o por nombre parcial (username).
     * - Si se encuentra el primer match, se retorna a la misma vista con $user.
     * - Si no hay resultados, la vista puede mostrar un mensaje adecuado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function searchUser(Request $request)
    {
        // Extrae criterios de búsqueda del formulario (pueden venir vacíos).
        $id       = $request->input('id');
        $username = $request->input('username');

        // Construye la consulta: match por ID o por nombre parcial (LIKE).
        $user = User::when($id,       fn($q) => $q->orWhere('id', $id))
                    ->when($username, fn($q) => $q->orWhere('name', 'like', "%{$username}%"))
                    ->first();

        // Renderiza la misma vista de gestión, ahora con $user (o null si no hubo match).
        return view('admin.manage-users', compact('user'));
    }

    /**
     * Actualizar datos básicos de un usuario: name, email, role.
     * La validación puede endurecerse según las reglas del negocio (únicos, formato, etc.).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  ID del usuario a actualizar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        // Recupera el usuario o falla con 404 si no existe.
        $user = User::findOrFail($id);

        // Validación ligera (puedes ajustarla a tus requisitos):
        // - name: requerido
        // - email: requerido, formato email
        // - role: uno de los roles soportados
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'role'  => ['required', 'in:user,admin,recolector'],
        ]);

        // Actualiza los campos permitidos.
        $user->update($validated);

        // Vuelve a la pantalla anterior con un mensaje de éxito.
        return back()->with('ok','Usuario actualizado correctamente');
    }

    /**
     * Eliminar (borrar) un usuario existente.
     * Considera reglas de negocio (no permitir eliminarse a sí mismo, usuarios con dependencias, etc.).
     *
     * @param  int  $id  ID del usuario a eliminar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($id)
    {
        // Busca el usuario objetivo.
        $user = User::findOrFail($id);

        // REGLA: impedir que un admin se elimine a sí mismo.
        if (auth()->id() === $user->id) {
        return back()->with('ok', 'No puedes eliminar tu propio usuario.');
        }

        // Ejecuta borrado definitivo.
        $user->delete();

        // Redirige atrás con confirmación.
        return back()->with('ok','Usuario eliminado');
    }
}