<?php
/**
 * EJEMPLO COMPLETO: Override - Módulo Login Avanzado
 *
 * Ubicación en template: /templates/cassiopeia/html/mod_login/default.php
 *
 * CAMBIOS REALIZADOS:
 * - Formulario moderno con validación HTML5
 * - Diseño responsive
 * - Recordar usuario (checkbox)
 * - Enlace de recuperación de contraseña
 * - Enlace de registro
 * - Mensajes de error personalizados
 * - Accesibilidad mejorada (labels, aria-*)
 *
 * VARIABLES DISPONIBLES:
 * @var  object  $this->module        Objeto del módulo
 * @var  object  $this->params        Parámetros del módulo
 *
 * JOOMLA: 5.x, 6.x
 * FECHA: 2024-03-06
 */

defined('_JEXEC') or die;

$params = $this->params;
$app = JFactory::getApplication();
$user = JFactory::getUser();

// Si ya está logueado, mostrar usuario logueado
if ($user->id):
    ?>
    <div class="login-module logged-in">
        <div class="user-welcome">
            <p class="welcome-text">
                Bienvenido, <strong><?php echo htmlspecialchars($user->name); ?></strong>
            </p>
        </div>

        <nav class="user-menu" aria-label="Usuario">
            <ul class="user-nav-list">
                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>"
                       class="user-profile-link">
                        <i class="fas fa-user-circle"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1'); ?>"
                       class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </nav>
    </div>

<?php else: ?>

    <!-- USUARIO NO LOGUEADO - FORMULARIO DE LOGIN -->
    <div class="login-module">

        <!-- TÍTULO DEL MÓDULO -->
        <?php if ($params->get('show_module_title', 1)): ?>
            <h3 class="module-title">
                <?php echo htmlspecialchars($this->module->title); ?>
            </h3>
        <?php endif; ?>

        <!-- MENSAJES DE ERROR/ÉXITO -->
        <?php if ($app->getMessageQueue()): ?>
            <div class="login-messages">
                <?php foreach ($app->getMessageQueue() as $message): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($message['type']); ?>" role="alert">
                        <?php echo htmlspecialchars($message['text']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO DE LOGIN -->
        <form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="login-form"
              aria-label="Formulario de inicio de sesión">

            <!-- CAMPO USUARIO -->
            <div class="form-group">
                <label for="modlgn-username" class="form-label">
                    <i class="fas fa-envelope"></i> Email o Usuario
                </label>
                <input type="text"
                       id="modlgn-username"
                       name="username"
                       class="form-control"
                       placeholder="tu@email.com"
                       required
                       autocomplete="username"
                       aria-required="true">
            </div>

            <!-- CAMPO CONTRASEÑA -->
            <div class="form-group">
                <label for="modlgn-password" class="form-label">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <input type="password"
                       id="modlgn-password"
                       name="password"
                       class="form-control"
                       required
                       autocomplete="current-password"
                       aria-required="true">
            </div>

            <!-- RECORDAR USUARIO -->
            <?php if ($params->get('show_remember', 1)): ?>
                <div class="form-group form-check">
                    <input type="checkbox"
                           id="modlgn-remember"
                           name="remember"
                           class="form-check-input"
                           value="yes">
                    <label for="modlgn-remember" class="form-check-label">
                        Recordarme
                    </label>
                </div>
            <?php endif; ?>

            <!-- BOTÓN SUBMIT -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block login-submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </div>

            <!-- CAMPOS OCULTOS REQUERIDOS -->
            <input type="hidden" name="option" value="com_users">
            <input type="hidden" name="task" value="user.login">
            <input type="hidden" name="return" value="<?php echo base64_encode(JUri::current()); ?>">
            <?php echo JHtml::_('form.token'); ?>

        </form>

        <!-- ENLACES ADICIONALES -->
        <div class="login-links">
            <ul class="links-list">
                <?php if ($params->get('show_forgot_password', 1)): ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"
                           class="forgot-password-link">
                            Olvidé mi contraseña
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($params->get('show_register', 1)): ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"
                           class="register-link">
                            Crear nueva cuenta
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($params->get('show_forgot_username', 1)): ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"
                           class="forgot-username-link">
                            Olvidé mi usuario
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>

<?php endif; ?>

<?php
/**
 * NOTAS DE IMPLEMENTACIÓN:
 *
 * 1. USUARIO LOGUEADO: Mostrar diferente UI
 * 2. VALIDACIÓN: HTML5 requerida + servidor
 * 3. MENSAJES: Mostrar queue de la app
 * 4. TOKEN: Obligatorio para seguridad CSRF
 * 5. RUTAS: Usar JRoute::_() para compatibilidad
 * 6. ACCESIBILIDAD: labels, aria-required, aria-label
 * 7. SEGURIDAD: No guardar password en value
 * 8. UX: Placeholder, autocomplete, required
 * 9. PARÁMETROS: Mostrar/ocultar según config
 * 10. ICONO: Usar fontawesome (asegurar cargado en template)
 *
 * PARÁMETROS DEL MÓDULO:
 * - show_module_title: mostrar título
 * - show_remember: mostrar checkbox recordarme
 * - show_forgot_password: mostrar enlace
 * - show_register: mostrar enlace registro
 * - show_forgot_username: mostrar enlace usuario
 *
 * CSS ESPERADO:
 * - .login-module: contenedor principal
 * - .login-form: formulario
 * - .form-group: grupo de campo
 * - .form-control: input
 * - .btn: botón
 * - .alert: mensajes
 * - .login-links: enlaces adicionales
 */
?>
