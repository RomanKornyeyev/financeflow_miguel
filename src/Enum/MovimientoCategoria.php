<?php

namespace App\Enum;

enum MovimientoCategoria: string
{
    // Ingresos
    case SUELDO = 'sueldo';
    case VENTAS = 'ventas';
    case REGALO = 'regalo';
    case OTROS_INGRESOS = 'otros_ingresos';

    // Gastos
    case COMIDA = 'comida';
    case ALQUILER_HIPOTECA = 'alquiler_hipoteca';
    case SERVICIOS = 'servicios'; // luz, agua, gas, internet
    case TRANSPORTE = 'transporte'; // coche, metro, bus
    case SALUD = 'salud'; // médicos, farmacia
    case OCIO = 'ocio'; // cine, bares, viajes
    case EDUCACION = 'educacion'; // cursos, libros
    case ROPA = 'ropa';
    case GASTO_IMPREVISTO = 'gasto_imprevisto';
    case AHORRO = 'ahorro';
}
