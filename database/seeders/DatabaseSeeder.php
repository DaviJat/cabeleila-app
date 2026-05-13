<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\Availability;
use App\Models\Appointment;
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

        // Geração da base de clientes fictícios
        $clients = Client::factory(20)->create();

        // Configuração do período de geração: Primeiro ao último dia do mês ATUAL
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $hoje = Carbon::today();

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
                $availability = Availability::create([
                    'date' => $date->format('Y-m-d'),
                    'hour' => $hour . ':00',
                    'is_available' => true,
                ]);

                // Geração de agendamentos fictícios apenas entre o começo do mês e a data atual (40% de chance de ocupação)
                if ($date->lte($hoje) && rand(1, 100) <= 40) {

                    // Simulação de cenário onde um cliente cancelou e outro pegou a vaga (15% de chance dentro dos ocupados)
                    $isRebooked = rand(1, 100) <= 15;

                    if ($isRebooked) {
                        // Criação do agendamento antigo (Cancelado)
                        Appointment::create([
                            'client_id' => $clients->random()->id,
                            'availability_id' => $availability->id,
                            'status' => 'canceled',
                            'notes' => 'Cliente cancelou com antecedência.',
                            'created_at' => now()->subDays(3), // Simulando que foi agendado e cancelado no passado
                            'updated_at' => now()->subDays(2),
                        ]);

                        // Definição do status do novo cliente que pegou a vaga (Concluído se for no passado, Confirmado se for hoje)
                        $newStatus = $date->lt($hoje) ? 'completed' : 'confirmed';

                        // Criação do agendamento novo (Ativo)
                        Appointment::create([
                            'client_id' => $clients->random()->id,
                            'availability_id' => $availability->id,
                            'status' => $newStatus,
                            'notes' => 'Agendamento de encaixe na vaga que foi cancelada.',
                            'created_at' => now()->subDays(1), // Simulando que foi criado após o cancelamento do anterior
                            'updated_at' => now()->subDays(1),
                        ]);

                        // Atualiza a disponibilidade do horário, pois a vaga foi preenchida novamente
                        $availability->update(['is_available' => false]);
                    } else {
                        // Fluxo normal: apenas 1 agendamento para o horário
                        if ($date->lt($hoje)) {
                            // Horários passados: 80% de chance de conclusão, 20% de cancelamento
                            $status = rand(1, 100) <= 80 ? 'completed' : 'canceled';
                        } else {
                            // Horários de hoje: 50% de chance de pendente, 50% de confirmado
                            $status = rand(1, 100) <= 50 ? 'confirmed' : 'pending';
                        }

                        // Criação do agendamento vinculado ao cliente e ao horário
                        Appointment::create([
                            'client_id' => $clients->random()->id,
                            'availability_id' => $availability->id,
                            'status' => $status,
                            'notes' => 'Agendamento gerado automaticamente pelo sistema.',
                        ]);

                        // Atualização da disponibilidade do horário caso o agendamento seja mantido
                        if ($status !== 'canceled') {
                            $availability->update(['is_available' => false]);
                        }
                    }
                }
            }
        }
    }
}
