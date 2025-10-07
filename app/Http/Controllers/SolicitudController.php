@startuml
title Sistema de Gestión de Residuos - Aplicación de Patrones GRASP (EcoGestión)
skinparam style strictuml
skinparam classAttributeIconSize 0
skinparam noteBackgroundColor #EAF4EA
skinparam noteBorderColor #88C057

' ==========================
' CLASES PRINCIPALES
' ==========================

class Usuario {
  -id: int
  -nombre: String
  -email: String
  -password: String
  -telefono: String
  -direccion: String
  -puntos: int
  +registrar()
  +login()
  +solicitarRecoleccion()
  +consultarReporte()
  +canjearPuntos()
}

class Solicitud {
  -id: int
  -fecha_programada: Date
  -tipo_residuo: String
  -frecuencia: String
  -estado: String
  +crear()
  +actualizar()
  +eliminar()
  +asignarRecolector()
}

class Recoleccion {
  -id: int
  -fecha: Date
  -hora: Time
  -estado: String
  -tipoResiduo: String
  -peso: float
  +programar()
  +confirmar()
  +cancelar()
}

class Notificacion {
  -id: int
  -tipo: String
  -mensaje: String
  -fechaEnvio: String
  +enviarWhatsApp()
  +enviarEmail()
}

class Punto {
  -id: int
  -puntosOtorgados: int
  -fecha: Date
  +calcularPuntos()
  +canjear()
}

class Recolector {
  -id: int
  -nombre: String
  -empresaId: int
  +registrarPeso()
  +confirmarRecoleccion()
}

class Empresa {
  -id: int
  -nombre: String
  -especialidad: String
  +asignarRecolector()
  +generarReporte()
}

class Administrador {
  -id: int
  -nombre: String
  -email: String
  +gestionarUsuarios()
  +modificarFormulaPuntos()
  +generarReportes()
}

class Reporte {
  -id: int
  -tipo: String
  -fechaInicio: Date
  -fechaFin: Date
  -datos: JSON
  +generarReporteUsuario()
  +generarReporteEmpresa()
  +generarReporteGeneral()
}

' ==========================
' CONTROLADORES
' ==========================
class UsuarioController {
  +registrarUsuario()
  +iniciarSesion()
  +verSolicitudes()
}

class SolicitudController {
  +crearSolicitud()
  +editarSolicitud()
  +eliminarSolicitud()
}

class RecoleccionController {
  +programarRecoleccion()
  +confirmarRecoleccion()
}

class AdminController {
  +gestionarUsuarios()
  +verReportes()
}


' ==========================
' RELACIONES ENTRE CLASES
' ==========================

Usuario "1" --> "*" Solicitud : realiza >
Solicitud "1" --> "1" Recoleccion : genera >
Usuario "1" --> "*" Notificacion : recibe >
Usuario "1" --> "*" Punto : acumula >
Empresa "1" --> "*" Recolector : asigna >
Administrador "1" --> "*" Reporte : genera >
Recolector "1" --> "*" Recoleccion : ejecuta >
Empresa "1" --> "*" Solicitud : pertenece a >

' ==========================
' PATRONES GRASP (ETIQUETAS Y JUSTIFICACIÓN)
' ==========================

note right of SolicitudController
  << Controller >>
  Gestiona la creación, edición y eliminación
  de solicitudes. Actúa como mediador entre
  la vista y los modelos.
end note

note right of Usuario
  << Creator >>
  Crea instancias de Solicitud cuando
  un usuario solicita una recolección.
end note

note right of Solicitud
  << Information Expert >>
  Posee la información necesaria para
  gestionar sus propios datos.
end note

note right of Recoleccion
  << Low Coupling / High Cohesion >>
  Maneja tareas específicas sin depender
  de otras clases. Se mantiene modular.
end note

note right of AdminController
  << Controller / Polymorphism >>
  Gestiona usuarios y reportes con 
  comportamientos distintos según el rol.
end note

@enduml