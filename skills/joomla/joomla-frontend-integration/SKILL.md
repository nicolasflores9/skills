---
name: joomla-frontend-integration
description: Domina integración frontend en Joomla 5/6 con Helix Framework.
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
