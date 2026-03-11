<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cipta akaun admin baru';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->ask('Nama penuh admin');
        $email = $this->ask('Alamat e-mel');
        $password = $this->secret('Kata laluan');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200', 'unique:admin_users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $admin = AdminUser::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        $this->info("Akaun admin berjaya dicipta!");
        $this->table(
            ['ID', 'Nama', 'E-mel'],
            [[$admin->id, $admin->name, $admin->email]]
        );

        return Command::SUCCESS;
    }
}
