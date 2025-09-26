<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['id', 'name', 'email', 'role']);

        $this->info('Users in database:');
        foreach($users as $user) {
            $this->line("ID: {$user->id} - Name: {$user->name} - Email: {$user->email} - Role: {$user->role}");
        }

        return 0;
    }
}
