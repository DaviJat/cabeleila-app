<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ServiceSeeder extends Seeder
{
    // Service catalog definition with real descriptions, prices, and durations
    public function run(): void
    {
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
    }
}
