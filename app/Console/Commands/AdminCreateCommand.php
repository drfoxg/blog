<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--email= : Email администратора}
                            {--name= : Имя администратора}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать администратора';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Email');
        $name = $this->option('name') ?? $this->ask('Имя', 'Admin');
        $password = $this->secret('Пароль');

        // Валидация
        $validator = Validator::make(
            compact('email', 'name', 'password'),
            [
                'email' => 'required|email|unique:users,email',
                'name' => 'required|string|max:255',
                'password' => 'required|min:8',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            $user->roles()->attach($adminRole);
        } else {
            $this->warn('Роль admin не найдена. Сначала запустите DatabaseSeeder.');
        }

        $this->info("Администратор {$email} создан.");

        return self::SUCCESS;
    }
}
