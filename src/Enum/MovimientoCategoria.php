<?php

namespace App\Enum;

enum MovimientoCategoria: string
{
    // Gastos
    case COMIDA = 'comida / supermercado';
    case OCIO = 'ocio / restaurantes / cines'; // cine, bares, viajes
    case ALQUILER_HIPOTECA = 'alquiler / hipoteca';
    case SERVICIOS = 'luz / agua / gas / internet'; // luz, agua, gas, internet
    case ELECTRONICA = 'electrónica / pc / móvil'; // electrónica, pc, móvil
    case TRANSPORTE = 'transporte'; // coche, metro, bus
    case SALUD = 'salud'; // médicos, farmacia
    case ROPA = 'ropa';
    case SUSCRIPCIONES = 'suscripciones'; // Netflix, Spotify, Amazon Prime
    case EDUCACION = 'educacion'; // cursos, libros
    case GASTO_IMPREVISTO = 'gasto_imprevisto';
    case AHORRO = 'ahorro';
    case INVERSION = 'inversion'; // inversiones, acciones, criptomonedas
    case OTROS_GASTOS = 'otros gastos'; // otros gastos no clasificados

    // Ingresos
    case SUELDO = 'sueldo / nómina'; // sueldo, nómina
    case VENTAS = 'ventas';
    case REGALO = 'regalo';
    case HERENCIA = 'herencia';
    case OTROS_INGRESOS = 'otros ingresos';
}
