<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\Availability;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Desativa a emissão de eventos de modelo para otimizar o tempo de execução da seed.
     */
    use WithoutModelEvents;

    /**
     * Executa as seeds do banco de dados.
     */
    public function run(): void
    {
        // Criação da conta de administrador do sistema
        User::factory()->create([
            'name' => 'Leila Admin',
            'email' => 'leila@example.com',
            'role' => 'admin',
        ]);

        // Criação de usuários secundários para testes de permissão (por enquanto comentado para simular um cenário inicial com apenas o administrador)
        // User::factory(2)->create();

        // Definição do catálogo de serviços com escopo real de descrições, preços e durações
        Service::factory()
            ->count(8)
            ->state(new Sequence(
                [
                    'name' => 'Corte Feminino',
                    'description' => 'Corte estilizado incluindo lavagem e secagem básica.',
                    'price' => 80.00,
                    'duration_minutes' => 60
                ],
                [
                    'name' => 'Corte Masculino',
                    'description' => 'Corte moderno com acabamento detalhado.',
                    'price' => 45.00,
                    'duration_minutes' => 40
                ],
                [
                    'name' => 'Manicure',
                    'description' => 'Cutilagem e esmaltação das unhas das mãos.',
                    'price' => 30.00,
                    'duration_minutes' => 45
                ],
                [
                    'name' => 'Pedicure',
                    'description' => 'Cutilagem e esmaltação das unhas dos pés.',
                    'price' => 35.00,
                    'duration_minutes' => 50
                ],
                [
                    'name' => 'Escova e Modelagem',
                    'description' => 'Lavagem capilar e finalização com escova.',
                    'price' => 55.00,
                    'duration_minutes' => 45
                ],
                [
                    'name' => 'Coloração Completa',
                    'description' => 'Aplicação de tintura profissional em todo o cabelo.',
                    'price' => 150.00,
                    'duration_minutes' => 120
                ],
                [
                    'name' => 'Hidratação Profunda',
                    'description' => 'Tratamento de reposição de nutrientes e brilho.',
                    'price' => 70.00,
                    'duration_minutes' => 40
                ],
                [
                    'name' => 'Design de Sobrancelha',
                    'description' => 'Modelagem de sobrancelhas com pinça ou linha.',
                    'price' => 35.00,
                    'duration_minutes' => 30
                ],
            ))
            ->create();

        // Geração da base de clientes fictícios (por enquanto comentado para simular um cenário inicial sem clientes)
        // Client::factory(50)->create();

        // Configuração do período de geração: Primeiro ao último dia do mês ATUAL
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Iteração diária para montagem da grade de horários
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {

            // Regra de negócio: Salão não tem expediente aos domingos
            if ($date->isSunday()) {
                continue;
            }

            $hours = [];

            // Regra de negócio: Expediente em dias úteis (Segunda a Sexta)
            if ($date->isWeekday()) {
                $hours = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
            }
            // Regra de negócio: Expediente aos Sábados (Apenas período matutino)
            elseif ($date->isSaturday()) {
                $hours = ['08:00', '09:00', '10:00', '11:00'];
            }

            foreach ($hours as $hour) {
                // Registro do horário (slot) na tabela de disponibilidades
                // Todos os horários inseridos são marcados como disponíveis
                Availability::create([
                    'date' => $date->format('Y-m-d'),
                    'hour' => $hour . ':00',
                    'is_available' => true,
                ]);
            }
        }
    }
}
