<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Estadisticas extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('estadisticas/index');
    }

    // Ventas de las visitas cobradas entre dos fechas, por defecto hoy
    public function resumen()
    {
        $hoy   = date('Y-m-d');
        $desde = $this->request->getGet('desde') ?: $hoy;
        $hasta = $this->request->getGet('hasta') ?: $desde;

        $valido = $this->validateData(['desde' => $desde, 'hasta' => $hasta], [
            'desde' => 'valid_date[Y-m-d]',
            'hasta' => 'valid_date[Y-m-d]',
        ], [
            'desde' => ['valid_date' => 'La fecha desde no es válida.'],
            'hasta' => ['valid_date' => 'La fecha hasta no es válida.'],
        ]);

        if (! $valido) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($desde > $hasta) {
            return $this->failValidationErrors(['hasta' => 'La fecha hasta no puede ser anterior a la fecha desde.']);
        }

        $fila = db_connect()->table('mesa_visita')
            ->select('COUNT(id) AS cobradas, COALESCE(SUM(total), 0) AS vendido')
            ->where('estado', 'cerrada')
            ->where('eliminado_en', null)
            ->where('cerrada_en >=', $desde . ' 00:00:00')
            ->where('cerrada_en <', date('Y-m-d', strtotime($hasta . ' +1 day')) . ' 00:00:00')
            ->get()
            ->getRowArray();

        $cobradas = (int) $fila['cobradas'];
        $vendido  = (float) $fila['vendido'];

        return $this->respond([
            'desde'    => $desde,
            'hasta'    => $hasta,
            'vendido'  => $vendido,
            'cobradas' => $cobradas,
            'promedio' => $cobradas > 0 ? round($vendido / $cobradas, 2) : 0,
        ]);
    }
}
