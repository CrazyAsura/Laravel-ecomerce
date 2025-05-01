<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza a role do usuário para admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::find($this->argument('id'));

        if (!$user) {
            $this->error('Usuário não encontrado!');
            return 1;
        }

        $user->update(['role' => 'admin']);
        $this->info('Usuário atualizado para admin com sucesso!');
        return 0;
    }
}
