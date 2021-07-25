<?php

namespace App\Event;

final class MembreEvents
{
    const CHANGE_PASSWORD_COMPLETED = 'membre.change_password_completed';
    const PROFILE_EDIT_INITIALIZE = 'membre.profile_edit_initialize';
    const PROFILE_EDIT_COMPLETED = 'membre.profile_edit_completed';
    const REGISTRATION_INITIALIZE = 'membre.registration_initialize';
    const REGISTRATION_CONFIRM = 'membre.registration_confirm';
    const REGISTRATION_CONFIRMED = 'membre.registration_confirmed';
    const REGISTRATION_SUCCESS = 'membre.registration_success';
    const REGISTRATION_COMPLETED = 'membre.registration_completed';
    const REGISTRATION_FAILURE = 'membre.registration_failure';
    const RESETTING_SEND_EMAIL_INITIALIZE = 'membre.resetting_send_email_initialize';
    const RESETTING_SEND_EMAIL_COMPLETED = 'membre.resetting_send_email_completed';
    const RESETTING_RESET_COMPLETED = 'membre.resetting_reset_completed';
}
