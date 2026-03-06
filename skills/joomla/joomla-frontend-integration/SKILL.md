---
name: joomla-frontend-integration
description: Domina integración frontend (CSS/JS) en Joomla 5/6 con Helix Framework. WebAssetManager, Bootstrap 5, módulos custom, SP Page Builder, responsive design, Web Components. Triggers: css joomla, javascript joomla, WebAssetManager, cargar css joomla, assets joomla, bootstrap helix, frontend joomla, joomla.asset.json, helix custom code, componentes responsive
---

# Integración Frontend en Joomla 5/6 con Helix Framework

Guía completa para dominar CSS/JavaScript, WebAssetManager y diseño responsive en Joomla 5/6 utilizando Helix Framework y Bootstrap 5.

---

## 1. WebAssetManager: El Sistema Moderno

El **WebAssetManager** (`\Joomla\CMS\WebAsset\WebAssetManager`) es la forma oficial de gestionar assets en Joomla 5/6. Reemplaza HTMLHelper y Document API (deprecados en 5.3+).

### Ciclo de vida
1. **Registered**: Asset declarado en `joomla.asset.json`
2. **Used**: Activado con `$wa->useScript()` o `$wa->useStyle()`
3. **Rendered**: Insertado en HTML por `<jdoc:include type="head" />`
4. **Loaded**: Descargado por navegador con versionado automático

### Obtener instancia
```php
$wa = \Joomla\CMS\Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();

// Cargar assets
$wa->useScript('jquery');
$wa->useStyle('bootstrap');
```

### Niveles de carga en Joomla
```
1. media/vendor/joomla.asset.json
2. media/system/joomla.asset.json
3. media/legacy/joomla.asset.json
4. media/com_active/joomla.asset.json
5. templates/active_template/joomla.asset.json
```

---

## 2. Estructura joomla.asset.json

Archivo JSON que declara todos los assets de forma centralizada. Se carga automáticamente.

### Estructura Básica
```json
{
  "$schema": "https://json.schemastore.org/joomla-manifest-schema.json",
  "name": "myext",
  "version": "1.0.0",
  "assets": [
    {
      "name": "my-script",
      "type": "script",
      "uri": "js/myfile.js",
      "dependencies": ["jquery"],
      "attributes": {
