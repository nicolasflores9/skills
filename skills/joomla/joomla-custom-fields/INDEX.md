# Índice de Recursos: Custom Fields en Joomla 5/6

## Archivo Principal

### SKILL.md (325 líneas)
Guía técnica completa sobre Custom Fields en Joomla 5/6. Cubre:
- Inicio rápido con ejemplos de código
- Los 16 tipos de campos disponibles
- API FieldsHelper (getFields, render)
- Creación desde panel administrativo
- Estructura de bases de datos (#__fields, #__fields_values)
- Renderizado en templates
- Uso en módulos
- Integración en componentes personalizados
- Field Groups (grupos de campos)
- Contextos soportados
- Validación y filtros
- Eventos del sistema
- Mejores prácticas
- Troubleshooting

**Triggers internos:** campo personalizado joomla, custom field, FieldsHelper, #__fields, campos artículos joomla, field group joomla

---

## Archivos de Referencia

### references/ejemplos-practicos.php (320 líneas)
Código PHP completo con 8 ejemplos prácticos listos para usar:

1. **Cargar campos en componente** - Cómo extender un modelo para cargar Custom Fields
2. **Plugin de inyección** - Inyectar campos en formularios de componentes personalizados
3. **Módulo con Custom Fields** - Helper completo para módulo que muestra campos
4. **Consultas a BD** - FieldValueRepository con métodos CRUD completos
5. **Override de template** - Renderizar campos con estilo personalizado
6. **Validación personalizada** - Crear reglas de validación JFormRule
7. **Vista de módulo** - Template HTML para mostrar campos en módulo
8. **Acceso por nombre** - Indexar campos y acceder directamente por nombre

**Características:**
- Código comentado y estructurado
- Implementación de interfaces Joomla
- Inyección de dependencias
- Manejo de errores

### references/base-datos.md (320 líneas)
Referencia completa sobre la estructura de base de datos:

**Tablas:**
- #__fields - Definiciones de campos
- #__fields_values - Valores almacenados
- #__fields_groups - Grupos de campos

**Contenidos:**
- Estructura SQL de cada tabla
- Descripción de campos (id, context, name, type, params, etc.)
- Ejemplos de consultas comunes (7 casos típicos)
- Estructura JSON en params y fieldparams
- Clase PHP FieldsRepository completa
- Consideraciones de performance
- Versionado y migración

**Utilidad:** Para desarrolladores que necesitan trabajar directamente con BD o entender la estructura interna.

### references/casos-uso.md (380 líneas)
7 casos de uso prácticos del mundo real:

1. **Galería de imágenes para artículos**
   - Campos repetibles y list of images
   - Renderizado en template

2. **SEO personalizado por artículo**
   - Campos específicos de SEO
   - Plugin que inyecta meta tags

3. **Información adicional en registro de usuario**
   - Campos en com_users.user
   - Plugin de validación frontend

4. **Tipología de contenido por categoría**
   - Diferentes campos según categoría
   - Template dinámico

5. **Contactos con información extendida**
   - Campos en com_contact.contact
   - Módulo de visualización

6. **Dashboard de usuario**
   - Acceso a campos en frontend
   - Perfil personalizado

7. **REST API con Custom Fields**
   - Exponer campos en JSON
   - Integración con API

**Características:**
- Implementación completa para cada caso
- Código HTML, PHP, SQL
- Buenas prácticas documentadas

### references/faq-troubleshooting.md (290 líneas)
Preguntas frecuentes y solución de problemas:

**FAQ (11 preguntas):**
- Cargar campos en componentes personalizados
- Campos compartidos entre contextos
- Validación server-side
- Almacenamiento de múltiples valores
- Automatic Display vs renderizado manual
- Migración entre Joomla
- Campos dinámicos desde BD
- Caché de resultados
- Reordenamiento de campos
- Y más...

**Troubleshooting (13 problemas):**
- Campos no aparecen en formulario
- Valores no se guardan
- Template override no funciona
- Campo aparece sin valor
- Campos lentos
- Campo repetible no funciona
- Permisos de acceso
- Campo de media no muestra imagen
- Validación personalizada no se ejecuta
- REST API devuelve "Field not found"
- Upgrade rompe campos
- Y más...

**Herramientas de Debug:**
- Inspeccionar campos cargados
- Verificar BD
- Logs del sistema
- Herramientas de terceros

**Checklist de Deployment**

---

## Estructura de Carpetas

```
/mnt/skills/joomla-custom-fields/
├── SKILL.md                              (Guía principal)
├── INDEX.md                              (Este archivo)
└── references/
    ├── ejemplos-practicos.php            (8 ejemplos de código)
    ├── base-datos.md                     (Estructura BD y queries)
    ├── casos-uso.md                      (7 casos prácticos reales)
    └── faq-troubleshooting.md            (FAQ y troubleshooting)
```

---

## Cómo Usar Este Skill

### Para Comenzar Rápido
1. Lee: **SKILL.md** - Sección "Inicio Rápido"
2. Consulta: **referencias/ejemplos-practicos.php** - Ejemplo 1

### Para Implementar un Módulo
1. Lee: **SKILL.md** - Sección "Uso en Módulos"
2. Copia: **referencias/ejemplos-practicos.php** - Ejemplo 3
3. Adapta a tu lógica

### Para Resolver Problemas
1. Busca en: **referencias/faq-troubleshooting.md**
2. Consulta: **referencias/base-datos.md** para queries

### Para Aplicar Casos Complejos
1. Encuentra tu caso en: **referencias/casos-uso.md**
2. Lee implementación completa
3. Copia código base
4. Personaliza para tu proyecto

### Para Trabajar con BD
1. Referencia: **referencias/base-datos.md**
2. Copia queries de "Ejemplos de Consultas Comunes"
3. Adapta a tus necesidades

---

## Contextos Cubiertos

- `com_content.article` - Artículos
- `com_content.categories` - Categorías
- `com_users.user` - Usuarios
- `com_contact.contact` - Contactos
- Componentes personalizados (com_*.*)

---

## Conceptos Clave Explicados

- **FieldsHelper** - Helper central para Custom Fields
- **jcfields** - Array de campos cargados en elemento
- **Contexto** - Identificador de dónde existen los campos
- **Field Group** - Agrupación de campos en pestañas
- **rawvalue vs value** - Valor sin procesar vs HTML renderizado
- **#__fields** - Tabla de definiciones
- **#__fields_values** - Tabla de valores

---

## Tecnologías

- **PHP 8.0+** - Sintaxis moderna con type hints
- **Joomla 5/6** - Framework CMS
- **MySQL 5.7+** - Base de datos
- **JDatabase API** - Consultas a BD
- **REST API** - Integración con servicios

---

## Niveles de Experiencia

- **Principiante:** Lee SKILL.md completo + Ejemplo 1 y 7
- **Intermedio:** Combina SKILL.md + 2-3 casos de referencias/
- **Avanzado:** Trabaja directamente con BD + plugins personalizados

---

## Última Actualización

Marzo 2026 - Joomla 5/6

## Próximos Tópicos Recomendados

- Crear tipos de campos personalizados
- REST API de Custom Fields
- Migración de datos entre campos
- Performance optimization en grandes volúmenes
- Multi-idioma en campos personalizados
