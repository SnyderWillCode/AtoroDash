<?php

namespace MythicalClient\Plugins\Events\Events;

use MythicalClient\Plugins\Events\PluginEvent;

class AuthEvent implements PluginEvent {

	public static function onAuthLoginFailed() : string {
		return "auth::LoginFailed";
	}

	public static function onAuthLoginSuccess() : string {
		return "auth::LoginSuccess";
	}

	public static function onAuthLogout() : string {
		return "auth::Logout";
	}

	public static function onAuthRegister() : string {
		return "auth::Register";
	}

	public static function onAuthRegisterFailed() : string {
		return "auth::RegisterFailed";
	}

	public static function onAuthRegisterSuccess() : string {
		return "auth::RegisterSuccess";
	}

	public static function onAuthForgotPassword() : string {
		return "auth::ForgotPassword";
	}

	public static function onAuthForgotPasswordFailed() : string {
		return "auth::ForgotPasswordFailed";
	}

	public static function onAuthForgotPasswordSuccess() : string {
		return "auth::ForgotPasswordSuccess";
	}

	public static function onAuthResetPasswordFailed() : string {
		return "auth::ResetPasswordFailed";
	}

	public static function onAuthResetPasswordSuccess() : string {
		return "auth::ResetPasswordSuccess";
	}

	public static function onAuth2FAVerifyFailed() : string {
		return "auth::2FAVerifyFailed";
	}

	public static function onAuth2FAVerifySuccess() : string {
		return "auth::2FAVerifySuccess";
	}
}
