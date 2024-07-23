<?php
function validate_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_mobile($mobile)
{
    return preg_match('/^\d{10}$/', $mobile);
}

function validate_password($password)
{
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

function password_match($password, $confirm_password)
{
    return $password === $confirm_password;
}
