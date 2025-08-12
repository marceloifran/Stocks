<?php

namespace Database\Seeders;

use App\Models\Obra;
use App\Models\personal;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏗️ Creando obras de ejemplo...');

        // Crear obras de ejemplo
        $obras = [
            [
                'nombre' => 'Construcción Edificio Central',
                'codigo' => 'OBRA-001',
                'descripcion' => 'Construcción de edificio de oficinas de 15 pisos en el centro de la ciudad',
                'ubicacion' => 'Av. Principal 1234, Ciudad',
                'cliente' => 'Constructora ABC S.A.',
                'fecha_inicio' => Carbon::now()->subDays(30),
                'fecha_fin_estimada' => Carbon::now()->addMonths(12),
                'estado' => 'en_progreso',
                'presupuesto' => 5000000.00,
                'contactos' => [
                    [
                        'nombre' => 'Juan Pérez',
                        'telefono' => '+54 11 1234-5678',
                        'email' => 'juan.perez@constructoraabc.com',
                        'cargo' => 'Jefe de Obra'
                    ],
                    [
                        'nombre' => 'María González',
                        'telefono' => '+54 11 8765-4321',
                        'email' => 'maria.gonzalez@constructoraabc.com',
                        'cargo' => 'Supervisora de Seguridad'
                    ]
                ],
                'activa' => true
            ],
            [
                'nombre' => 'Planta Industrial Norte',
                'codigo' => 'OBRA-002',
                'descripcion' => 'Construcción de planta industrial para manufactura',
                'ubicacion' => 'Parque Industrial Norte, Lote 45',
                'cliente' => 'Industrias XYZ Ltda.',
                'fecha_inicio' => Carbon::now()->subDays(15),
                'fecha_fin_estimada' => Carbon::now()->addMonths(8),
                'estado' => 'en_progreso',
                'presupuesto' => 3200000.00,
                'contactos' => [
                    [
                        'nombre' => 'Carlos Rodríguez',
                        'telefono' => '+54 11 2468-1357',
                        'email' => 'carlos.rodriguez@industriasxyz.com',
                        'cargo' => 'Gerente de Proyecto'
                    ]
                ],
                'activa' => true
            ],
            [
                'nombre' => 'Complejo Residencial Sur',
                'codigo' => 'OBRA-003',
                'descripcion' => 'Desarrollo de complejo residencial con 200 unidades',
                'ubicacion' => 'Barrio Sur, Manzana 12',
                'cliente' => 'Desarrollos Inmobiliarios S.R.L.',
                'fecha_inicio' => Carbon::now()->addDays(10),
                'fecha_fin_estimada' => Carbon::now()->addMonths(18),
                'estado' => 'planificada',
                'presupuesto' => 8500000.00,
                'contactos' => [
                    [
                        'nombre' => 'Ana Martínez',
                        'telefono' => '+54 11 9876-5432',
                        'email' => 'ana.martinez@desarrollosinmobiliarios.com',
                        'cargo' => 'Directora de Proyecto'
                    ]
                ],
                'activa' => true
            ],
            [
                'nombre' => 'Remodelación Oficinas Centro',
                'codigo' => 'OBRA-004',
                'descripcion' => 'Remodelación integral de oficinas corporativas',
                'ubicacion' => 'Torre Empresarial, Piso 8-12',
                'cliente' => 'Corporación Empresarial',
                'fecha_inicio' => Carbon::now()->subDays(60),
                'fecha_fin_estimada' => Carbon::now()->subDays(10),
                'estado' => 'completada',
                'presupuesto' => 750000.00,
                'contactos' => [],
                'activa' => false
            ]
        ];

        foreach ($obras as $obraData) {
            Obra::create($obraData);
        }

        $this->command->info('✅ Obras creadas exitosamente.');

        // Verificar si hay personal existente
        $personalExistente = personal::count();

        if ($personalExistente === 0) {
            $this->command->info('👥 Creando personal de ejemplo...');

            // Crear personal de ejemplo
            $personalData = [
                [
                    'nombre' => 'Roberto Silva',
                    'dni' => '12345678',
                    'nro_identificacion' => '001',
                    'departamento' => 'Producción'
                ],
                [
                    'nombre' => 'Laura Fernández',
                    'dni' => '23456789',
                    'nro_identificacion' => '002',
                    'departamento' => 'Producción'
                ],
                [
                    'nombre' => 'Miguel Torres',
                    'dni' => '34567890',
                    'nro_identificacion' => '003',
                    'departamento' => 'Logística'
                ],
                [
                    'nombre' => 'Carmen López',
                    'dni' => '45678901',
                    'nro_identificacion' => '004',
                    'departamento' => 'Producción'
                ],
                [
                    'nombre' => 'Diego Morales',
                    'dni' => '56789012',
                    'nro_identificacion' => '005',
                    'departamento' => 'Producción'
                ],
                [
                    'nombre' => 'Patricia Ruiz',
                    'dni' => '67890123',
                    'nro_identificacion' => '006',
                    'departamento' => 'Administración'
                ]
            ];

            foreach ($personalData as $persona) {
                personal::create($persona);
            }

            $this->command->info('✅ Personal creado exitosamente.');
        }

        $this->command->info('🔄 Asignando personal a obras...');

        // Obtener obras activas y personal disponible
        $obrasActivas = Obra::where('activa', true)->where('estado', 'en_progreso')->get();
        $personalDisponible = personal::where('disponible_para_asignacion', true)->get();

        if ($obrasActivas->count() > 0 && $personalDisponible->count() > 0) {
            // Asignar personal a las primeras dos obras
            $obra1 = $obrasActivas->first();
            $obra2 = $obrasActivas->skip(1)->first();

            // Asignar 3 personas a la primera obra
            $personalObra1 = $personalDisponible->take(3);
            foreach ($personalObra1 as $persona) {
                $persona->asignarAObra($obra1);
                $this->command->line("  - {$persona->nombre} asignado a {$obra1->nombre}");
            }

            // Asignar 2 personas a la segunda obra si existe
            if ($obra2 && $personalDisponible->count() > 3) {
                $personalObra2 = $personalDisponible->skip(3)->take(2);
                foreach ($personalObra2 as $persona) {
                    $persona->asignarAObra($obra2);
                    $this->command->line("  - {$persona->nombre} asignado a {$obra2->nombre}");
                }
            }
        }

        $this->command->info('✅ Asignaciones completadas.');
        $this->command->info('🎉 Seeder de obras ejecutado exitosamente.');
        $this->command->newLine();
        $this->command->info('📋 Resumen:');
        $this->command->info('  - Obras creadas: ' . Obra::count());
        $this->command->info('  - Personal total: ' . personal::count());
        $this->command->info('  - Personal asignado: ' . personal::whereNotNull('obra_actual_id')->count());
        $this->command->info('  - Personal disponible: ' . personal::where('disponible_para_asignacion', true)->whereNull('obra_actual_id')->count());
    }
}
