<?php

namespace App\Console\Commands\Setup\NotTranslation\Auth\Update;

use App\Traits\Commands\ModelFileHandler;
use Illuminate\Console\Command;

class UserModelCommand extends Command
{

    use ModelFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:user-model-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user model';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $userModelContent = <<< MODEL
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected \$fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected \$hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected \$casts = [
        'email_verified_at' => 'datetime',
    ];
}
MODEL;


        if ($this->updateModelFile('User', $userModelContent)) {
            $this->info('User model has been updated successfully!');
        } else {
            $this->error('Failed to update user model!');
        }
    }
}
