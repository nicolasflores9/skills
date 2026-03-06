<?php
/**
 * COMPLETE EXAMPLE: Override - Advanced Login Module
 *
 * Template location: /templates/cassiopeia/html/mod_login/default.php
 *
 * CHANGES MADE:
 * - Modern form with HTML5 validation
 * - Responsive design
 * - Remember user (checkbox)
 * - Password recovery link
 * - Registration link
 * - Custom error messages
 * - Improved accessibility (labels, aria-*)
 *
 * AVAILABLE VARIABLES:
 * @var  object  $this->module        Module object
 * @var  object  $this->params        Module parameters
 *
 * JOOMLA: 5.x, 6.x
 * DATE: 2024-03-06
 */

defined('_JEXEC') or die;

$params = $this->params;
$app = JFactory::getApplication();
$user = JFactory::getUser();

// If already logged in, show logged-in user view
if ($user->id):
    ?>
    <div class="login-module logged-in">
        <div class="user-welcome">
            <p class="welcome-text">
                Welcome, <strong><?php echo htmlspecialchars($user->name); ?></strong>
            </p>
        </div>

        <nav class="user-menu" aria-label="User">
            <ul class="user-nav-list">
                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>"
                       class="user-profile-link">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                </li>
                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1'); ?>"
                       class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </a>
                </li>
            </ul>
        </nav>
    </div>

<?php else: ?>

    <!-- USER NOT LOGGED IN - LOGIN FORM -->
    <div class="login-module">

        <!-- MODULE TITLE -->
        <?php if ($params->get('show_module_title', 1)): ?>
            <h3 class="module-title">
                <?php echo htmlspecialchars($this->module->title); ?>
            </h3>
        <?php endif; ?>

        <!-- ERROR/SUCCESS MESSAGES -->
        <?php if ($app->getMessageQueue()): ?>
            <div class="login-messages">
                <?php foreach ($app->getMessageQueue() as $message): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($message['type']); ?>" role="alert">
                        <?php echo htmlspecialchars($message['text']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="login-form"
              aria-label="Login form">

            <!-- USERNAME FIELD -->
            <div class="form-group">
                <label for="modlgn-username" class="form-label">
                    <i class="fas fa-envelope"></i> Email or Username
                </label>
                <input type="text"
                       id="modlgn-username"
                       name="username"
                       class="form-control"
                       placeholder="you@email.com"
                       required
                       autocomplete="username"
                       aria-required="true">
            </div>

            <!-- PASSWORD FIELD -->
            <div class="form-group">
                <label for="modlgn-password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password"
                       id="modlgn-password"
                       name="password"
                       class="form-control"
                       required
                       autocomplete="current-password"
                       aria-required="true">
            </div>

            <!-- REMEMBER USER -->
            <?php if ($params->get('show_remember', 1)): ?>
                <div class="form-group form-check">
                    <input type="checkbox"
                           id="modlgn-remember"
                           name="remember"
                           class="form-check-input"
                           value="yes">
                    <label for="modlgn-remember" class="form-check-label">
                        Remember me
                    </label>
                </div>
            <?php endif; ?>

            <!-- SUBMIT BUTTON -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block login-submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </div>

            <!-- REQUIRED HIDDEN FIELDS -->
            <input type="hidden" name="option" value="com_users">
            <input type="hidden" name="task" value="user.login">
            <input type="hidden" name="return" value="<?php echo base64_encode(JUri::current()); ?>">
            <?php echo JHtml::_('form.token'); ?>

        </form>

        <!-- ADDITIONAL LINKS -->
        <div class="login-links">
            <ul class="links-list">
                <?php if ($params->get('show_forgot_password', 1)): ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"
                           class="forgot-password-link">
                            Forgot my password
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($params->get('show_register', 1)): ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"
                           class="register-link">
                            Create new account
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($params->get('show_forgot_username', 1)): ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"
                           class="forgot-username-link">
                            Forgot my username
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>

<?php endif; ?>

<?php
/**
 * IMPLEMENTATION NOTES:
 *
 * 1. LOGGED-IN USER: Show different UI
 * 2. VALIDATION: HTML5 required + server
 * 3. MESSAGES: Show app message queue
 * 4. TOKEN: Required for CSRF security
 * 5. ROUTES: Use JRoute::_() for compatibility
 * 6. ACCESSIBILITY: labels, aria-required, aria-label
 * 7. SECURITY: Do not store password in value
 * 8. UX: Placeholder, autocomplete, required
 * 9. PARAMETERS: Show/hide based on config
 * 10. ICONS: Use fontawesome (ensure loaded in template)
 *
 * MODULE PARAMETERS:
 * - show_module_title: show title
 * - show_remember: show remember me checkbox
 * - show_forgot_password: show link
 * - show_register: show registration link
 * - show_forgot_username: show username link
 *
 * EXPECTED CSS:
 * - .login-module: main container
 * - .login-form: form
 * - .form-group: field group
 * - .form-control: input
 * - .btn: button
 * - .alert: messages
 * - .login-links: additional links
 */
?>
