# Checklist Desarrollo Frontend Joomla 5/6 + Helix

## Pre-Desarrollo

### Preparación del Entorno
- [ ] Joomla 5.6+ instalado localmente
- [ ] Helix Ultimate 2.x+ template activo
- [ ] Cache deshabilitado en desarrollo (Site > Global Configuration > System)
- [ ] Error reporting activado (System > Global Configuration > Server)
- [ ] Browser developer tools abiertos (F12)
- [ ] Extensión de Joomla debug instalada (opcional pero recomendada)

### Análisis de Requisitos
- [ ] Identificar todos los assets (CSS, JS) necesarios
- [ ] Listar dependencias entre assets
- [ ] Definir qué assets son críticos (above the fold)
- [ ] Determinar breakpoints responsive (mobile, tablet, desktop)
- [ ] Crear lista de navegadores a soportar

### Arquitectura del Proyecto
- [ ] Decidir si usar módulo, componente o plugin
- [ ] Crear estructura de carpetas
- [ ] Planificar joomla.asset.json (presets, versionado)
- [ ] Definir convención de nombres CSS/JS

---

## Durante Desarrollo

### Estructura de Archivos
- [ ] Crear carpeta `/css` en el proyecto
- [ ] Crear carpeta `/js` en el proyecto
- [ ] Crear `joomla.asset.json` en raíz del proyecto
- [ ] Crear `README.md` con instrucciones
- [ ] NO crear archivos en carpetas core de Helix

### Assets Management (WebAssetManager)
- [ ] Registrar todos los assets en `joomla.asset.json`
- [ ] Usar versioning automático (`"version": "auto"`)
- [ ] Declarar todas las dependencias correctamente
- [ ] Usar presets para agrupar assets relacionados
- [ ] Validar JSON con schema oficial

### CSS Development
- [ ] Usar mobile-first approach (CSS base para mobile)
- [ ] Escribir CSS modular y reutilizable
- [ ] No usar `!important` (excepto casos extremos)
- [ ] Usar CSS custom properties (variables)
- [ ] Seguir naming convention (BEM o similar)
- [ ] NO sobrescribir clases Bootstrap directamente
- [ ] Crear clases custom en lugar de modificar base

### JavaScript Development
- [ ] Usar `defer` para scripts custom
- [ ] Usar `async` solo para analytics/tracking
- [ ] Documentar funciones con JSDoc
- [ ] Usar `document.addEventListener('DOMContentLoaded', ...)`
- [ ] Manejar errores apropiadamente
- [ ] NO usar jQuery innecesariamente (aunque disponible)
- [ ] Considerar Web Components para componentes reutilizables

### Responsive Design
- [ ] Diseñar mobile-first
- [ ] Probar en viewport 375px (móvil pequeño)
- [ ] Probar en viewport 768px (tablet)
- [ ] Probar en viewport 1024px (desktop)
- [ ] Probar en viewport 1400px (desktop grande)
- [ ] Usar media queries correctamente
- [ ] Verificar que Bootstrap grid funciona (col-12, col-md-6, etc.)
- [ ] Probar zoom (100%, 90%, 110%)

### Módulos Custom (si aplica)
- [ ] Crear `joomla.asset.json` en raíz del módulo
- [ ] Crear `helper.php` con métodos de ayuda
- [ ] Cargar assets en helper.php
- [ ] Crear template HTML limpio
- [ ] Añadir CSS y JS modulares
- [ ] Hacer el módulo reutilizable

### SP Page Builder Addons (si aplica)
- [ ] Crear plugin tipo sppagebuilder
- [ ] Usar Custom CSS para estilos
- [ ] Para JS: crear plugin custom
- [ ] Registrar assets en el plugin
- [ ] Documentar parámetros del addon
- [ ] Hacer responsive el addon

---

## Testing & Validación

### Navegadores Desktop
- [ ] Chrome/Chromium (versión actual)
- [ ] Firefox (versión actual)
- [ ] Safari (si tienes Mac)
- [ ] Edge (versión actual)
- [ ] Compatibilidad con versiones antiguas (IE11 si es requerido)

### Navegadores Mobile
- [ ] Chrome Mobile
- [ ] Safari Mobile (iOS)
- [ ] Samsung Internet
- [ ] Firefox Mobile

### Validación de Código
- [ ] Validar HTML (https://validator.w3.org/)
- [ ] Validar CSS (https://jigsaw.w3.org/css-validator/)
- [ ] Revisar console.log en Developer Tools (sin errores)
- [ ] Revisar Network tab (sin 404s)
- [ ] Usar Lighthouse para auditoría

### Performance
- [ ] PageSpeed Insights score > 80
- [ ] Lighthouse Performance > 80
- [ ] First Contentful Paint < 2s
- [ ] Largest Contentful Paint < 4s
- [ ] Cumulative Layout Shift < 0.1
- [ ] Total Blocking Time < 200ms
- [ ] Minificar CSS/JS en producción

### Accesibilidad
- [ ] WCAG 2.1 Level AA compliance
- [ ] Navegación con teclado funcional
- [ ] Focus indicators visibles
- [ ] Contraste de colores adecuado (4.5:1 mínimo)
- [ ] Labels en formularios
- [ ] Alt text en imágenes
- [ ] Landmarks HTML5 correctos (header, main, footer)
- [ ] aria-labels donde sea necesario

### Assets Loading
- [ ] `<jdoc:include type="head" />` en template
- [ ] Scripts cargan en orden correcto (Network tab)
- [ ] No hay dependencias circulares
- [ ] Todos los assets están disponibles
- [ ] Versioning funciona (check URL assets)
- [ ] Cache busting en producción

### Responsive Testing
- [ ] Usar responsive mode en DevTools
- [ ] Probar portrait y landscape
- [ ] Verificar media queries se aplican
- [ ] Imágenes responsive funcionen
- [ ] Táctil funcional (hover effects manejar)
- [ ] Overflow controlado en mobile
- [ ] Touch targets > 48px

### Cross-browser Testing
- [ ] Variables CSS soportadas en todos
- [ ] Flexbox renderiza igual
- [ ] Grid layout funciona
- [ ] Animaciones CSS suave
- [ ] Fuentes cargadas correctamente
- [ ] Formularios responsivos
- [ ] Audio/Video funciona (si aplica)

---

## Pre-Producción

### Code Review
- [ ] Revisar joomla.asset.json por errores
- [ ] Revisar CSS por especificidad excesiva
- [ ] Revisar JS por memory leaks
- [ ] Revisar comentarios y documentación
- [ ] Limpiar código Debug (console.log, etc.)
- [ ] Verificar no hay datos sensibles en código

### Optimización
- [ ] Minificar CSS (opcional)
- [ ] Minificar JS (opcional)
- [ ] Optimizar imágenes (WEBP donde sea posible)
- [ ] Usar lazy loading para imágenes off-screen
- [ ] Prefetch/preload críticos (opcional)
- [ ] Implementar compresión gzip
- [ ] Usar CDN para assets grandes (opcional)

### Security
- [ ] Validar inputs en formularios
- [ ] Escapar outputs HTML
- [ ] CSRF protection en forms
- [ ] No almacenar datos sensibles en localStorage
- [ ] HTTPS configurado
- [ ] Content Security Policy headers (opcional)

### Documentación
- [ ] Documentar estructura de proyecto
- [ ] Crear README.md con instrucciones
- [ ] Documentar convenciones CSS/JS
- [ ] Crear ejemplos de uso
- [ ] Documentar parámetros de módulos
- [ ] Crear guía de customización

### Backup & Versionado
- [ ] Código en Git/Version Control
- [ ] Tags de versión creadas
- [ ] Changelog actualizado
- [ ] Backup de database pre-deployment
- [ ] Backup de files pre-deployment

---

## Producción

### Configuración Final
- [ ] Cache habilitado (Site > Global Configuration > Cache)
- [ ] Gzip habilitado en servidor
- [ ] CDN configurado (opcional)
- [ ] Analytics instalado
- [ ] Monitoring setup (Uptime Robot, New Relic, etc.)
- [ ] Error logging configurado
- [ ] Backups automáticos activados

### Post-Deployment
- [ ] Revisar sitio en producción
- [ ] Probar todas funcionalidades
- [ ] Verificar assets cargan desde CDN
- [ ] Revisar Console en DevTools (sin errores)
- [ ] Revisar Network tab (carga óptima)
- [ ] PageSpeed Insights en producción
- [ ] Test unitarios/integración (si aplica)
- [ ] Monitor uptime y performance

### Mantenimiento
- [ ] Revisar logs de errores regularmente
- [ ] Update Joomla cuando nuevo version
- [ ] Update plugins/extensiones
- [ ] Monitorear performance metrics
- [ ] Revisar broken links mensualmente
- [ ] Actualizar contenido obsoleto
- [ ] Respaldo de base de datos diarios

---

## Troubleshooting Rápido

### Assets no cargan
```
1. Revisar <jdoc:include type="head" /> en template
2. Revisar path en joomla.asset.json
3. Limpiar cache Joomla (System > Clear Cache)
4. Revisar Network tab en DevTools (404?)
5. Revisar permisos de archivos (755)
```

### CSS/JS no aplica
```
1. Verificar que $wa->useScript() fue llamado
2. Revisar que joomla.asset.json está correcto
3. Revisar especificidad CSS (otros estilos sobreescriben?)
4. Revisar Network tab (asset está ahí?)
5. Hard refresh navegador (Ctrl+Shift+R)
```

### Bootstrap se rompe
```
1. Verificar que Bootstrap está en $wa->useStyle('bootstrap')
2. No sobrescribir variables Bootstrap
3. No usar !important en sobrescrituras
4. Revisar que clases Bootstrap están correctas
5. Revisar order de carga (Bootstrap debe cargar primero)
```

### Helix se rompe
```
1. NUNCA editar archivos core de Helix (template.css, etc.)
2. SIEMPRE usar custom.css
3. SIEMPRE usar Custom Code section para snippets globales
4. Revisar que Custom Code no tiene errores
5. Limpiar cache y hard refresh
```

---

## Recursos Útiles

- Joomla Manual: https://manual.joomla.org/
- Bootstrap Docs: https://getbootstrap.com/
- Helix Framework: https://www.joomshaper.com/
- Google DevTools: https://developer.chrome.com/docs/devtools/
- W3C HTML Validator: https://validator.w3.org/
- WebAIM Contrast Checker: https://webaim.org/resources/contrastchecker/
- GTmetrix Performance: https://gtmetrix.com/

---

Versión: 1.0
Última actualización: Marzo 2026
