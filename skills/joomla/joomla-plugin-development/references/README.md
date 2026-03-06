# Archivos de Referencias: Plugins Joomla 5/6

Esta carpeta contiene documentación de referencia y ejemplos adicionales para complementar el contenido principal de la skill.

## Archivos Disponibles

### 1. REFERENCIA_RAPIDA.md
**Propósito:** Consulta rápida durante el desarrollo
**Contenido:**
- Checklist de creación de plugins
- Estructura mínima copiable
- Tabla de eventos principales
- Servicios comunes (DB, User, App, etc.)
- Ejemplos de parámetros
- Validación y filtrado
- Queries SQL
- Debugging rápido
- Errores comunes y soluciones
- Tablas de referencia

**Ideal para:** Buscar rápidamente cómo hacer algo específico

### 2. SNIPPETS.md
**Propósito:** Código listo para copiar y pegar
**Contenido:**
- Template mínimo de plugin (manifest + provider + Extension)
- Acceso a servicios comunes (BD, usuario, aplicación, cache)
- Patrones de eventos comunes
- Validación y filtrado de entrada
- Manejo de errores
- Internacionalización
- Configuración de plugin
- Queries avanzadas
- Inyección de dependencias
- Checklist de implementación

**Ideal para:** Copiar código base rápidamente y adaptarlo

### 3. EJEMPLOS_AVANZADOS.md
**Propósito:** Ejemplos completos de plugins reales
**Contenido:**
- Plugin Sistema con Logger
- Plugin Usuario con Email
- Plugin con DI avanzada
- Plugin Contenido con Event Classes tipadas
- Plugin con Validación
- Plugin Sistema con Múltiples Eventos

**Ideal para:** Aprender patrones avanzados con ejemplos funcionales

### 4. TROUBLESHOOTING.md
**Propósito:** Resolución de problemas
**Contenido:**
- Problemas de instalación
- Errores de clase/namespace
- Problemas de eventos
- Configuración y traducciones
- Rendimiento
- Seguridad
- Compatibilidad
- Debugging efectivo

**Ideal para:** Cuando algo no funciona y necesitas soluciones

## Flujo de Uso Recomendado

### Nuevo en plugins Joomla?
1. Lee SKILL.md (contenido principal)
2. Consulta REFERENCIA_RAPIDA.md para estructura
3. Copia un template de SNIPPETS.md
4. Adapta según tu caso de uso

### Necesitas crear algo específico?
1. Abre REFERENCIA_RAPIDA.md para la tabla de eventos
2. Busca patrón similar en SNIPPETS.md
3. Si necesitas algo más avanzado, revisa EJEMPLOS_AVANZADOS.md

### Tienes un problema?
1. Abre TROUBLESHOOTING.md
2. Encuentra tu síntoma
3. Aplica las soluciones sugeridas
4. Revisa en SNIPPETS.md si necesitas código correcto

### Quieres aprender patrones avanzados?
1. Lee secciones relevantes en SKILL.md
2. Estudia EJEMPLOS_AVANZADOS.md
3. Adapta al tu caso de uso

## Convenciones de Nomenclatura

### Grupos de Plugins
- **system** - Eventos del sistema
- **content** - Eventos de contenido/artículos
- **user** - Eventos de usuario
- **editor** - Eventos del editor
- **installer** - Eventos de instalación

### Naming
```
plg_[grupo]_[nombre]

Ejemplos:
- plg_system_helloworld
- plg_content_shortcodes
- plg_user_email
```

### Namespaces
```
MyCompany\Plugin\[Grupo]\[Nombre]

Ejemplos:
- MyCompany\Plugin\System\Helloworld
- MyCompany\Plugin\Content\Shortcodes
- MyCompany\Plugin\User\Email
```

## Eventos por Categoría

### Eventos del Sistema (onXxx)
onAfterInitialise, onAfterRoute, onAfterDispatch, onBeforeRender, onBeforeCompileHead, onAfterRender

### Eventos de Contenido (onXxx)
onContentPrepare, onContentAfterTitle, onContentBeforeSave, onContentAfterSave, onContentBeforeDelete, onContentAfterDelete, onContentChangeState

### Eventos de Usuario (onXxx)
onUserBeforeSave, onUserAfterSave, onUserBeforeDelete, onUserAfterDelete, onUserLogin, onUserLogout

## Estructura Recomendada

```
plg_grupo_nombre/
├── manifest.xml              # Configuración e instalación
├── services/
│   └── provider.php          # Inyección de dependencias
├── src/
│   ├── Extension/
│   │   └── Nombre.php       # Clase principal
│   ├── Event/               # Opcional: Event classes personalizadas
│   └── Helper/              # Opcional: Clases auxiliares
└── language/
    ├── en-GB/
    │   ├── plg_grupo_nombre.ini
    │   └── plg_grupo_nombre.sys.ini
    └── es-ES/
        ├── plg_grupo_nombre.ini
        └── plg_grupo_nombre.sys.ini
```

## Checklist Rápido

Para crear un plugin nuevoen 5 minutos:

1. [ ] Crear carpeta: `plugins/[grupo]/[nombre]/`
2. [ ] Copiar template de manifest.xml de SNIPPETS.md
3. [ ] Copiar template de services/provider.php
4. [ ] Copiar template de Extension class
5. [ ] Crear archivos .ini en language/en-GB/
6. [ ] Cambiar namespace en 3 archivos (manifest, provider, Extension)
7. [ ] Panel Control > Extensiones > Plugins > Habilitar
8. [ ] Verificar en logs/joomla.log

## Recursos Externos

- [Joomla Manual Oficial](https://manual.joomla.org/)
- [Documentación de Plugins](https://docs.joomla.org/Plugin)
- [Joomla API Documentation](https://api.joomla.org/)
- [Forum de la Comunidad](https://forum.joomla.org/)

## Versiones Soportadas

Esta documentación cubre:
- Joomla 5.0+
- Joomla 6.0+ (cuando esté disponible)

Características específicas:
- SubscriberInterface: Joomla 4.4+
- Event Classes: Joomla 5.2+
- PSR-4 Namespaces: Joomla 4.0+

## Historial de Versiones

**v1.0.0** - 6 Marzo 2025
- Documentación inicial completa
- 5 archivos de referencias
- Cubrimiento de Joomla 5/6
- 80+ ejemplos de código

## Contribuciones

Si encuentras errores o tienes sugerencias, por favor:
1. Reporta el problema
2. Proporciona ejemplos de código
3. Sugiere mejoras

---

**Última actualización:** 6 de Marzo 2025
**Versión de documentación:** 1.0.0
**Enfoque:** Joomla 5/6 - Patrones Modernos
