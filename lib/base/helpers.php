<?php

/**
 * Escapa un texto para pintarlo dentro de HTML (también en atributos).
 * Úsalo siempre con lo que escribe el usuario: títulos, personajes, fechas...
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
