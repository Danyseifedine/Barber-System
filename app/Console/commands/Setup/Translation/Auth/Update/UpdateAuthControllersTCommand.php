<?php

namespace App\Console\Commands\Setup\Translation\Auth\Update;

use App\Traits\Commands\ControllerFileHandler;
use Illuminate\Console\Command;

class UpdateAuthControllersTCommand extends Command
{

    use ControllerFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:auth-controllers-t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Auth Controllers';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $path = 'Auth';

        $loginControllerContent = <<< CONTROLLER
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected \$redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        \$this->middleware('guest')->except('logout');
    }

    protected function authenticated()
    {
        return \$this->successRedirectResponse('/home');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
CONTROLLER;

        $registerControllerContent = <<< CONTROLLER
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected \$redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        \$this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  \$data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array \$data)
    {
        return Validator::make(\$data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  \$data
     * @return \App\Models\User
     */
    protected function create(array \$data)
    {
        return User::create([
            'name' => \$data['name'],
            'email' => \$data['email'],
            'password' => Hash::make(\$data['password']),
        ]);
    }

    protected function registered()
    {
        return \$this->successRedirectResponse(\$this->redirectTo);
    }
}

CONTROLLER;

        $forgotPasswordControllerContent = <<< CONTROLLER
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class ForgotPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    public function sendResetLinkEmail(Request \$request)
    {
        \$this->validateEmail(\$request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        \$response = \$this->broker()->sendResetLink(
            \$this->credentials(\$request)
        );

        return \$response == Password::RESET_LINK_SENT
            ? \$this->successToastResponse(__('Password reset email sent!'))
            : \$this->errorToastResponse(__('Failed to send password reset email!'));
    }
}

CONTROLLER;

        $resetPasswordControllerContent = <<< CONTROLLER
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;


class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected \$redirectTo = '/home';

    public function reset(Request \$request)
    {
        \$request->validate(\$this->rules(), \$this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        \$response = \$this->broker()->reset(
            \$this->credentials(\$request),
            function (\$user, \$password) {
                \$this->resetPassword(\$user, \$password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return \$response == Password::PASSWORD_RESET
            ? \$this->successRedirectResponse(\$this->redirectTo)
            : \$this->errorToastResponse(__('Failed to reset password!'));
    }
}
CONTROLLER;

        $verificationControllerContent = <<< CONTROLLER
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected \$redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        \$this->middleware('auth');
        \$this->middleware('signed')->only('verify');
        \$this->middleware('throttle:6,1')->only('verify', 'resend');
    }


    protected function verified()
    {
        return redirect(\$this->redirectTo);
    }

    public function resend(Request \$request)
    {
        if (\$request->user()->hasVerifiedEmail()) {
            return \$this->errorResponse('Email already verified.');
        }

        \$request->user()->sendEmailVerificationNotification();

        return \$this->successToastResponse(__('Verification email sent!'));
    }
}
CONTROLLER;

        $confirmPasswordControllerContent = <<< CONTROLLER
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected \$redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        \$this->middleware('auth');
    }
}

CONTROLLER;

        if ($this->updateControllerFile('LoginController', $loginControllerContent, $path)) {
            $this->info('LoginController updated');
        } else {
            $this->error('LoginController update failed');
        }

        if ($this->updateControllerFile('RegisterController', $registerControllerContent, $path)) {
            $this->info('RegisterController updated');
        } else {
            $this->error('RegisterController update failed');
        }

        if ($this->updateControllerFile('ForgotPasswordController', $forgotPasswordControllerContent, $path)) {
            $this->info('ForgotPasswordController updated');
        } else {
            $this->error('ForgotPasswordController update failed');
        }

        if ($this->updateControllerFile('ResetPasswordController', $resetPasswordControllerContent, $path)) {
            $this->info('ResetPasswordController updated');
        } else {
            $this->error('ResetPasswordController update failed');
        }

        if ($this->updateControllerFile('VerificationController', $verificationControllerContent, $path)) {
            $this->info('VerificationController updated');
        } else {
            $this->error('VerificationController update failed');
        }

        if ($this->updateControllerFile('ConfirmPasswordController', $confirmPasswordControllerContent, $path)) {
            $this->info('ConfirmPasswordController updated');
        } else {
            $this->error('ConfirmPasswordController update failed');
        }
    }
}
