# Referencia Completa de Tipos de Campos en Joomla 5/6

## Campos de Texto

### text
```xml
<field name="titulo" type="text"
    label="MOD_LABEL_TITULO"
    description="MOD_DESC_TITULO"
    default="Mi Título"
    size="50"
    maxlength="255"
    placeholder="Ingrese un título"
    readonly="false"
    disabled="false"
    class="input-large"
    hint="Texto de ayuda"
    required="false"
    validate="Text"
/>
```

### textarea
```xml
<field name="descripcion" type="textarea"
    label="MOD_LABEL_DESC"
    description="MOD_DESC_DESC"
    default=""
    rows="5"
    cols="50"
    class="full-width"
    maxlength="5000"
    filter="string"
/>
```

### email
```xml
<field name="correo" type="email"
    label="MOD_LABEL_EMAIL"
    description="MOD_DESC_EMAIL"
    default=""
    validate="email"
/>
```

### url
```xml
<field name="enlace" type="url"
    label="MOD_LABEL_URL"
    description="MOD_DESC_URL"
    default="https://"
    validate="url"
/>
```

### password
```xml
<field name="contrasena" type="password"
    label="MOD_LABEL_PASSWORD"
    description="MOD_DESC_PASSWORD"
    default=""
    autocomplete="off"
/>
```

## Campos Numéricos

### integer
```xml
<field name="cantidad" type="integer"
    label="MOD_LABEL_CANTIDAD"
    description="MOD_DESC_CANTIDAD"
    default="10"
    min="1"
    max="100"
    step="1"
    validate="Numeric"
/>
```

### number
```xml
<field name="precio" type="number"
    label="MOD_LABEL_PRECIO"
    description="MOD_DESC_PRECIO"
    default="0.00"
    min="0"
    max="999999.99"
    step="0.01"
    filter="number"
/>
```

## Campos de Lista

### list
```xml
<field name="estado" type="list"
    label="MOD_LABEL_ESTADO"
    description="MOD_DESC_ESTADO"
    default="1"
    multiple="false"
    size="1"
    class="input-medium">
    <option value="1">Publicado</option>
    <option value="0">No Publicado</option>
    <option value="-2">Papelera</option>
</field>
```

### radio
```xml
<field name="vista" type="radio"
    label="MOD_LABEL_VISTA"
    description="MOD_DESC_VISTA"
    default="lista"
    class="btn-group">
    <option value="lista">Lista</option>
    <option value="grid">Grid</option>
    <option value="tabla">Tabla</option>
</field>
```

### checkbox
```xml
<field name="caracteristicas" type="checkbox"
    label="MOD_LABEL_CARACTERÍSTICAS"
    description="MOD_DESC_CARACTERÍSTICAS"
    default="1"
/>

<!-- Múltiples checkboxes -->
<field name="opciones" type="checkboxes"
    label="MOD_LABEL_OPCIONES"
    description="MOD_DESC_OPCIONES"
    default="">
    <option value="1">Opción 1</option>
    <option value="2">Opción 2</option>
    <option value="3">Opción 3</option>
</field>
```

## Campos de Selección

### category - Categorías de Contenido
```xml
<field name="categoria" type="category"
    label="MOD_LABEL_CATEGORIA"
    description="MOD_DESC_CATEGORIA"
    extension="com_content"
    default=""
    single="false"
    multiple="false"
    size="10"
/>
```

### article - Artículos
```xml
<field name="articulo" type="article"
    label="MOD_LABEL_ARTICULO"
    description="MOD_DESC_ARTICULO"
    default=""
/>
```

### user - Usuarios
```xml
<field name="usuario" type="user"
    label="MOD_LABEL_USUARIO"
    description="MOD_DESC_USUARIO"
    default=""
    inactive="false"
/>
```

### usergroup - Grupos de Usuarios
```xml
<field name="grupo" type="usergroup"
    label="MOD_LABEL_GRUPO"
    description="MOD_DESC_GRUPO"
    default=""
    multiple="false"
    size="10"
/>
```

### menu - Elementos de Menú
```xml
<field name="menu" type="menu"
    label="MOD_LABEL_MENU"
    description="MOD_DESC_MENU"
    default=""
    menu_type="mainmenu"
/>
```

### menuitem - Elementos de Menú Específicos
```xml
<field name="menuitem" type="menuitem"
    label="MOD_LABEL_MENUITEM"
    description="MOD_DESC_MENUITEM"
    default=""
/>
```

## Campos Especiales

### sql - Consulta SQL
```xml
<field name="opcion_sql" type="sql"
    label="MOD_LABEL_OPTION_SQL"
    description="MOD_DESC_OPTION_SQL"
    default=""
    query="SELECT id, title FROM #__articles WHERE state = 1 ORDER BY title"
    key_field="id"
    value_field="title"
/>
```

### modulelayout - Layouts del Módulo
```xml
<field name="layout" type="modulelayout"
    label="JFIELD_ALT_LAYOUT_LABEL"
    description="JFIELD_ALT_LAYOUT_DESC"
/>
```

### spacer - Divisor Visual
```xml
<field name="separador1" type="spacer"
    label="Sección Avanzada"
    description=""
/>
```

### note - Nota de Información
```xml
<field name="nota" type="note"
    label="Información Importante"
    description="Este es un mensaje informativo para el administrador"
    class="alert alert-info"
    heading="Atención"
/>
```

### hidden - Campo Oculto
```xml
<field name="id_oculto" type="hidden"
    default="0"
/>
```

## Campos de Fecha y Hora

### calendar - Selector de Fecha
```xml
<field name="fecha" type="calendar"
    label="MOD_LABEL_FECHA"
    description="MOD_DESC_FECHA"
    default=""
    format="%Y-%m-%d"
/>
```

### text con tipo date
```xml
<field name="fecha_html5" type="text"
    label="MOD_LABEL_FECHA"
    description="MOD_DESC_FECHA"
    default=""
    type="date"
    validate="date"
/>
```

## Campos Avanzados

### color
```xml
<field name="color" type="color"
    label="MOD_LABEL_COLOR"
    description="MOD_DESC_COLOR"
    default="#ffffff"
/>
```

### range - Slider
```xml
<field name="rango" type="range"
    label="MOD_LABEL_RANGO"
    description="MOD_DESC_RANGO"
    default="50"
    min="0"
    max="100"
    step="5"
/>
```

### editor - Editor WYSIWYG
```xml
<field name="contenido" type="editor"
    label="MOD_LABEL_CONTENIDO"
    description="MOD_DESC_CONTENIDO"
    default=""
    rows="5"
    cols="50"
    filter="safehtml"
    buttons="true"
    hide="pagebreak,readmore"
/>
```

### subform - Formulario Anidado
```xml
<field name="items" type="subform"
    label="MOD_LABEL_ITEMS"
    description="MOD_DESC_ITEMS"
    multiple="true">
    <form>
        <field name="titulo" type="text" label="Título" />
        <field name="enlace" type="text" label="Enlace" />
        <field name="icono" type="text" label="Icono" />
    </form>
</field>
```

## Validación

```xml
<!-- Atributos de validación -->
<field name="ejemplo" type="text"
    validate="Text"              <!-- Text, Integer, Numeric, Email, URL, etc. -->
    required="true"              <!-- Campo obligatorio -->
    maxlength="100"              <!-- Longitud máxima -->
    pattern="[a-z]+"             <!-- Expresión regular -->
    filter="string"              <!-- Filtro: string, integer, float, boolean, etc. -->
/>

<!-- Validadores disponibles -->
<!-- Text, Integer, Numeric, Date, Email, URL, Alphanumeric, Username, Password -->
```

## Atributos Comunes

```xml
<field
    name="campo"                 <!-- Nombre del campo (key) -->
    type="text"                  <!-- Tipo de campo -->
    label="MOD_LABEL_CAMPO"      <!-- Etiqueta translatable -->
    description="MOD_DESC_CAMPO" <!-- Descripción translatable -->
    default="valor"              <!-- Valor por defecto -->
    class="input-large"          <!-- Clases CSS -->
    readonly="false"             <!-- Solo lectura -->
    disabled="false"             <!-- Deshabilitado -->
    required="false"             <!-- Campo obligatorio -->
    hint="Sugerencia"            <!-- Texto de ayuda (placeholder) -->
    validate="Type"              <!-- Validación -->
    filter="filter_type"         <!-- Filtro de datos -->
    size="50"                    <!-- Tamaño del campo -->
    multiple="false"             <!-- Selección múltiple -->
    onchange="javascript()"      <!-- Evento onChange -->
/>
```

## Ejemplo Completo de Configuración

```xml
<config>
    <fields name="params">
        <!-- Fieldset Básico -->
        <fieldset name="basic" label="MOD_FIELDSET_BASIC">
            <field name="titulo" type="text"
                label="MOD_LABEL_TITULO"
                default="Mi Módulo"
                size="50" />

            <field name="descripcion" type="textarea"
                label="MOD_LABEL_DESC"
                rows="4" />

            <field name="categoria" type="category"
                label="MOD_LABEL_CATEGORIA"
                extension="com_content" />

            <field name="cantidad" type="integer"
                label="MOD_LABEL_CANTIDAD"
                default="10"
                min="1"
                max="100" />
        </fieldset>

        <!-- Fieldset Opciones de Visualización -->
        <fieldset name="display" label="MOD_FIELDSET_DISPLAY">
            <field name="orden" type="list"
                label="MOD_LABEL_ORDEN"
                default="fecha">
                <option value="fecha">Por Fecha</option>
                <option value="titulo">Por Título</option>
                <option value="visitas">Por Visitas</option>
            </field>

            <field name="direccion" type="radio"
                label="MOD_LABEL_DIRECCION"
                default="desc"
                class="btn-group">
                <option value="asc">Ascendente</option>
                <option value="desc">Descendente</option>
            </field>

            <field name="color" type="color"
                label="MOD_LABEL_COLOR"
                default="#ffffff" />
        </fieldset>

        <!-- Fieldset Avanzado -->
        <fieldset name="advanced" label="MOD_FIELDSET_ADVANCED">
            <field name="layout" type="modulelayout"
                label="JFIELD_ALT_LAYOUT_LABEL" />

            <field name="cache" type="list"
                label="JFIELD_CACHING_LABEL"
                default="1">
                <option value="0">No</option>
                <option value="1">Sí</option>
            </field>

            <field name="cache_time" type="integer"
                label="JFIELD_CACHE_TIME_LABEL"
                default="900" />

            <field name="moduleclass_sfx" type="text"
                label="COM_MODULES_FIELD_MODULECLASS_SFX_LABEL"
                description="COM_MODULES_FIELD_MODULECLASS_SFX_DESC" />
        </fieldset>
    </fields>
</config>
```

## Acceso a Parámetros en PHP

```php
<?php
// En Dispatcher o Helper
$params = $this->module->params;

// Tipos básicos
$texto = $params->get('titulo', 'default');
$numero = (int) $params->get('cantidad', 10);
$booleano = (bool) $params->get('opcion', false);

// Array (para campos múltiples)
$array = $params->get('opciones', array());

// JSON (para subform)
$items = $params->get('items', array());
if (is_string($items)) {
    $items = json_decode($items, true);
}

// Iterar parámetros
foreach ($params as $key => $value) {
    echo "$key: $value";
}
?>
```

---

**Nota**: Los nombres de campos deben traducirse en archivos .ini usando la convención `MOD_[MODULO]_LABEL_[CAMPO]`
