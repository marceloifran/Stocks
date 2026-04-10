<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Factores de Conversión para Huella de Carbono
    |--------------------------------------------------------------------------
    |
    | Estos factores se utilizan para calcular las emisiones de CO2 equivalente
    | basados en diferentes fuentes de emisión.
    |
    */

    'factores_conversion' => [
        // Combustibles (kgCO2e por litro)
        'gasolina' => 2.31, // Un litro de gasolina emite alrededor de 2.31 kg de CO2e
        'diesel' => 2.68,   // Un litro de diésel emite aproximadamente 2.68 kg de CO2e
        'gnc' => 1.52,

        // Electricidad (kgCO2e por kWh)
        'electricidad' => 0.38, // Factor promedio para Argentina

        // Residuos (kgCO2e por kg)
        'residuos_generales' => 0.58,
        'papel' => 0.96,
        'plastico' => 2.5,
        'metal' => 1.8,
        'organicos' => 0.25,
    ],

    /*
    |--------------------------------------------------------------------------
    | Unidades de Medida
    |--------------------------------------------------------------------------
    */
    'unidades' => [
        'combustible' => [
            'litros' => 'L',
            'galones' => 'gal',
        ],
        'electricidad' => [
            'kilowatt_hora' => 'kWh',
            'megawatt_hora' => 'MWh',
        ],
        'residuos' => [
            'kilogramos' => 'kg',
            'toneladas' => 't',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Fuentes de Emisión
    |--------------------------------------------------------------------------
    */
    'tipos_fuente' => [
        'combustible' => [
            'gasolina' => 'Gasolina',
            'diesel' => 'Diésel',
            'gnc' => 'Gas Natural Comprimido',
        ],
        'electricidad' => [
            'electricidad' => 'Electricidad de Red',
            'generador' => 'Generador Eléctrico',
        ],
        'residuos' => [
            'residuos_generales' => 'Residuos Generales',
            'papel' => 'Papel y Cartón',
            'plastico' => 'Plástico',
            'metal' => 'Metal',
            'organicos' => 'Residuos Orgánicos',
        ],
    ],
];
