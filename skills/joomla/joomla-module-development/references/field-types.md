# Complete Field Types Reference for Joomla 5/6

## Text Fields

### text
```xml
<field name="titulo" type="text"
    label="MOD_LABEL_TITULO"
    description="MOD_DESC_TITULO"
    default="My Title"
    size="50"
    maxlength="255"
    placeholder="Enter a title"
    readonly="false"
    disabled="false"
    class="input-large"
    hint="Help text"
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

## Numeric Fields

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

## List Fields

### list
```xml
<field name="estado" type="list"
    label="MOD_LABEL_ESTADO"
    description="MOD_DESC_ESTADO"
    default="1"
    multiple="false"
    size="1"
    class="input-medium">
    <option value="1">Published</option>
    <option value="0">Unpublished</option>
    <option value="-2">Trashed</option>
</field>
```

### radio
```xml
<field name="vista" type="radio"
    label="MOD_LABEL_VISTA"
    description="MOD_DESC_VISTA"
    default="lista"
    class="btn-group">
    <option value="lista">List</option>
    <option value="grid">Grid</option>
    <option value="tabla">Table</option>
</field>
```

### checkbox
```xml
<field name="caracteristicas" type="checkbox"
    label="MOD_LABEL_FEATURES"
    description="MOD_DESC_FEATURES"
    default="1"
/>

<!-- Multiple checkboxes -->
<field name="opciones" type="checkboxes"
    label="MOD_LABEL_OPTIONS"
    description="MOD_DESC_OPTIONS"
    default="">
    <option value="1">Option 1</option>
    <option value="2">Option 2</option>
    <option value="3">Option 3</option>
</field>
```

## Selection Fields

### category - Content Categories
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

### article - Articles
```xml
<field name="articulo" type="article"
    label="MOD_LABEL_ARTICULO"
    description="MOD_DESC_ARTICULO"
    default=""
/>
```

### user - Users
```xml
<field name="usuario" type="user"
    label="MOD_LABEL_USUARIO"
    description="MOD_DESC_USUARIO"
    default=""
    inactive="false"
/>
```

### usergroup - User Groups
```xml
<field name="grupo" type="usergroup"
    label="MOD_LABEL_GRUPO"
    description="MOD_DESC_GRUPO"
    default=""
    multiple="false"
    size="10"
/>
```

### menu - Menu Elements
```xml
<field name="menu" type="menu"
    label="MOD_LABEL_MENU"
    description="MOD_DESC_MENU"
    default=""
    menu_type="mainmenu"
/>
```

### menuitem - Specific Menu Items
```xml
<field name="menuitem" type="menuitem"
    label="MOD_LABEL_MENUITEM"
    description="MOD_DESC_MENUITEM"
    default=""
/>
```

## Special Fields

### sql - SQL Query
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

### modulelayout - Module Layouts
```xml
<field name="layout" type="modulelayout"
    label="JFIELD_ALT_LAYOUT_LABEL"
    description="JFIELD_ALT_LAYOUT_DESC"
/>
```

### spacer - Visual Divider
```xml
<field name="separador1" type="spacer"
    label="Advanced Section"
    description=""
/>
```

### note - Information Note
```xml
<field name="nota" type="note"
    label="Important Information"
    description="This is an informational message for the administrator"
    class="alert alert-info"
    heading="Attention"
/>
```

### hidden - Hidden Field
```xml
<field name="id_oculto" type="hidden"
    default="0"
/>
```

## Date and Time Fields

### calendar - Date Picker
```xml
<field name="fecha" type="calendar"
    label="MOD_LABEL_FECHA"
    description="MOD_DESC_FECHA"
    default=""
    format="%Y-%m-%d"
/>
```

### text with date type
```xml
<field name="fecha_html5" type="text"
    label="MOD_LABEL_FECHA"
    description="MOD_DESC_FECHA"
    default=""
    type="date"
    validate="date"
/>
```

## Advanced Fields

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

### editor - WYSIWYG Editor
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

### subform - Nested Form
```xml
<field name="items" type="subform"
    label="MOD_LABEL_ITEMS"
    description="MOD_DESC_ITEMS"
    multiple="true">
    <form>
        <field name="titulo" type="text" label="Title" />
        <field name="enlace" type="text" label="Link" />
        <field name="icono" type="text" label="Icon" />
    </form>
</field>
```

## Validation

```xml
<!-- Validation attributes -->
<field name="ejemplo" type="text"
    validate="Text"              <!-- Text, Integer, Numeric, Email, URL, etc. -->
    required="true"              <!-- Required field -->
    maxlength="100"              <!-- Maximum length -->
    pattern="[a-z]+"             <!-- Regular expression -->
    filter="string"              <!-- Filter: string, integer, float, boolean, etc. -->
/>

<!-- Available validators -->
<!-- Text, Integer, Numeric, Date, Email, URL, Alphanumeric, Username, Password -->
```

## Common Attributes

```xml
<field
    name="campo"                 <!-- Field name (key) -->
    type="text"                  <!-- Field type -->
    label="MOD_LABEL_CAMPO"      <!-- Translatable label -->
    description="MOD_DESC_CAMPO" <!-- Translatable description -->
    default="valor"              <!-- Default value -->
    class="input-large"          <!-- CSS classes -->
    readonly="false"             <!-- Read-only -->
    disabled="false"             <!-- Disabled -->
    required="false"             <!-- Required field -->
    hint="Suggestion"            <!-- Help text (placeholder) -->
    validate="Type"              <!-- Validation -->
    filter="filter_type"         <!-- Data filter -->
    size="50"                    <!-- Field size -->
    multiple="false"             <!-- Multiple selection -->
    onchange="javascript()"      <!-- onChange event -->
/>
```

## Complete Configuration Example

```xml
<config>
    <fields name="params">
        <!-- Basic Fieldset -->
        <fieldset name="basic" label="MOD_FIELDSET_BASIC">
            <field name="titulo" type="text"
                label="MOD_LABEL_TITULO"
                default="My Module"
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

        <!-- Display Options Fieldset -->
        <fieldset name="display" label="MOD_FIELDSET_DISPLAY">
            <field name="orden" type="list"
                label="MOD_LABEL_ORDEN"
                default="fecha">
                <option value="fecha">By Date</option>
                <option value="titulo">By Title</option>
                <option value="visitas">By Views</option>
            </field>

            <field name="direccion" type="radio"
                label="MOD_LABEL_DIRECCION"
                default="desc"
                class="btn-group">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </field>

            <field name="color" type="color"
                label="MOD_LABEL_COLOR"
                default="#ffffff" />
        </fieldset>

        <!-- Advanced Fieldset -->
        <fieldset name="advanced" label="MOD_FIELDSET_ADVANCED">
            <field name="layout" type="modulelayout"
                label="JFIELD_ALT_LAYOUT_LABEL" />

            <field name="cache" type="list"
                label="JFIELD_CACHING_LABEL"
                default="1">
                <option value="0">No</option>
                <option value="1">Yes</option>
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

## Accessing Parameters in PHP

```php
<?php
// In Dispatcher or Helper
$params = $this->module->params;

// Basic types
$texto = $params->get('titulo', 'default');
$numero = (int) $params->get('cantidad', 10);
$booleano = (bool) $params->get('opcion', false);

// Array (for multiple fields)
$array = $params->get('opciones', array());

// JSON (for subform)
$items = $params->get('items', array());
if (is_string($items)) {
    $items = json_decode($items, true);
}

// Iterate parameters
foreach ($params as $key => $value) {
    echo "$key: $value";
}
?>
```

---

**Note**: Field names must be translated in .ini files using the convention `MOD_[MODULE]_LABEL_[FIELD]`
