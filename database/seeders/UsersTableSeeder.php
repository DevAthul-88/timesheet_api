<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->setupPassport();
        $this->createUsers();
        $this->displayInstructions();
    }

    private function setupPassport(): void
    {
        $this->command->info('Setting up Passport...');

        if (!$this->tableExists('oauth_clients')) {
            $this->command->info('Running migrations...');
            Artisan::call('migrate');
        }

        if (DB::table('oauth_clients')->count() === 0) {
            $this->command->info('Installing Passport...');
            Artisan::call('passport:install');
        }
    }

    private function createUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'john.doe@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'jane.smith@example.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'password' => Hash::make('securepass'),
            ]
        );

        $this->command->info('Test users created');
    }

    private function tableExists($table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }

    private function displayInstructions(): void
    {
        $clients = DB::table('oauth_clients')->get();

        $this->command->info('======= USER CREDENTIALS =======');
        $this->command->info('User 1: john.doe@example.com / password123');
        $this->command->info('User 2: jane.smith@example.com / securepass');

        $this->command->info('======= OAUTH CLIENTS =======');
        foreach($clients as $client) {
            $this->command->info("Client ID: {$client->id}");
            $this->command->info("Client Secret: {$client->secret}");
            $this->command->info('--------------------------');
        }

        $this->command->info('To get a token:');
        $this->command->info('POST /oauth/token');
        $this->command->info('{');
        $this->command->info('    "grant_type": "password",');
        $this->command->info('    "client_id": "2",');
        $this->command->info('    "client_secret": "(client secret)",');
        $this->command->info('    "username": "john.doe@example.com",');
        $this->command->info('    "password": "password123",');
        $this->command->info('    "scope": "*"');
        $this->command->info('}');
    }
}
