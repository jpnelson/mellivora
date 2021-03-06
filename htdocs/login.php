<?php

require('../include/mellivora.inc.php');

if (user_is_logged_in()) {
    redirect(CONFIG_LOGIN_REDIRECT_TO);
    exit();
}

prefer_ssl();

head('Login');

echo '
<form method="post" class="form-signin" action="actions/login">
    <h2>Please sign in</h2>
    <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="email" class="form-control" placeholder="Email address" required autofocus />
    <input name="',md5(CONFIG_SITE_NAME.'PWD'), '" type="password" class="form-control" placeholder="Password" required />
    <input type="hidden" name="action" value="login" />
    <label class="checkbox">
        <input type="checkbox" name="remember_me" value="1"> Remember me
    </label>
    <button class="btn btn-primary" type="submit">Sign in</button> <a href="reset_password">I\'ve forgotten my password</a>
</form>
';

if (CONFIG_ACCOUNTS_SIGNUP_ALLOWED) {
    echo '
    <form method="post" id="registerForm" class="form-signin" action="actions/login">
        <h2>or, register a team</h2>
        <p>
            Your team shares one account.
            ',(CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? 'An confirmation email containing a random password will be sent to the chosen address.' : ''),'
        </p>
        <input name="',md5(CONFIG_SITE_NAME.'USR'),'" type="email" class="form-control" placeholder="Email address" required />
        ',(!CONFIG_ACCOUNTS_EMAIL_PASSWORD_ON_SIGNUP ? '<input name="'.md5(CONFIG_SITE_NAME.'PWD').'" type="password" class="form-control" placeholder="Password" required />' : ''),'
        <input name="team_name" type="text" class="form-control" placeholder="Team name" minlength="',CONFIG_MIN_TEAM_NAME_LENGTH,'" maxlength="',CONFIG_MAX_TEAM_NAME_LENGTH,'" required />';

    $user_types = db_select_all(
        'user_types',
        array(
            'id',
            'title',
            'description'
        )
    );

    if (!empty($user_types)) {
        echo '<select name="type" class="form-control">
        <option disabled selected>-- Please select team type --</option>';

        foreach ($user_types as $user_type) {
            echo '<option value="',htmlspecialchars($user_type['id']),'">',htmlspecialchars($user_type['title'] . ' - ' . $user_type['description']),'</option>';
        }

        echo '</select>';
    }

    $countries = db_select_all(
        'countries',
        array(
            'id',
            'country_name'
        ),
        null,
        'country_name ASC'
    );

    echo '<select name="country" class="form-control">
        <option disabled selected>-- Please select a country --</option>';

    foreach ($countries as $country) {
        echo '<option value="',htmlspecialchars($country['id']),'">',htmlspecialchars($country['country_name']),'</option>';
    }

    echo '</select>';

    if (CONFIG_RECAPTCHA_ENABLE_PUBLIC) {
        display_captcha();
    }

    echo '
    <input type="hidden" name="action" value="register" />
    <button class="btn btn-primary" type="submit">Register team</button>
</form>
';

} else {
    echo '<i>Registration is currently closed, but you can still <a href="interest">register your interest for upcoming events</a>.</i>';
}

foot();