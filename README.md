# Sistema de Gestión de Huella de Carbono

## Descripción General

Este sistema está enfocado exclusivamente en la gestión y cálculo de la huella de carbono. Permite registrar, medir y analizar las emisiones de CO2 de una organización para ayudar en la toma de decisiones ambientales.

## Módulos del Sistema

### 1. Huella de Carbono

Este módulo permite el registro detallado de las emisiones de CO2:

-   **Registro por categorías**:

    -   Combustible: registra consumos de diferentes tipos de combustibles fósiles
    -   Electricidad: registra consumo eléctrico en kWh
    -   Residuos: registra generación de diferentes tipos de desechos

-   **Información registrada**:

    -   Fecha del consumo o emisión
    -   Categoría y tipo específico de fuente
    -   Cantidad consumida (en la unidad correspondiente)
    -   Identificador de la fuente (ej: patente del vehículo)
    -   Horas de operación (para combustibles)
    -   Notas adicionales

-   **Cálculo automático**:
    -   Conversión inmediata de consumos a emisiones de CO2e
    -   Visualización del resultado en kilogramos de CO2 equivalente

### 2. Parámetros de Huella de Carbono

Este módulo permite gestionar los factores de conversión utilizados en los cálculos:

-   **Gestión de parámetros por categoría**:

    -   Definición de tipos específicos dentro de cada categoría
    -   Configuración de factores de conversión precisos
    -   Establecimiento de unidades de medida

-   **Funcionalidades**:

    -   Activación/desactivación de parámetros según necesidad
    -   Actualización de factores según normativas vigentes
    -   Descripción detallada de cada parámetro

-   **Estructura de parámetros**:
    -   Categoría (combustible, electricidad, residuos)
    -   Tipo (específico dentro de cada categoría)
    -   Factor de conversión (valor numérico)
    -   Unidad de medida (litros, kWh, kg, etc.)
    -   Unidad de resultado (siempre en kgCO2e)

### 3. Dashboard

Ofrece una visión consolidada de las emisiones:

-   Total de emisiones en kgCO2e
-   Distribución por categorías
-   Tendencias de emisión
-   Indicadores clave de desempeño ambiental

## Flujo de Trabajo del Sistema

1. **Configuración inicial**: Defina los parámetros necesarios en el módulo "Parámetros de Huella"

    - Establezca los tipos de combustibles usados
    - Configure los factores de conversión apropiados
    - Active los parámetros relevantes para su organización

2. **Registro de emisiones**: Utilice el módulo "Huella de Carbono"

    - Seleccione la categoría y tipo específico
    - Ingrese la cantidad consumida
    - Agregue los datos identificatorios necesarios
    - El sistema calculará automáticamente las emisiones

3. **Análisis**: Utilice el Dashboard para:
    - Visualizar el impacto ambiental total
    - Identificar principales fuentes de emisión
    - Analizar tendencias temporales
    - Tomar decisiones informadas sobre estrategias de reducción

El sistema está diseñado para simplificar el proceso de medición de la huella de carbono, permitiendo a las organizaciones enfocarse en acciones concretas de mitigación ambiental y sostenibilidad.
