<?php

// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

namespace App\Models;

// Mesas del salón
class MesaModelo extends ModeloBase
{
    protected $table         = 'mesas';
    protected $allowedFields = ['numero', 'capacidad', 'forma', 'pos_x', 'pos_y'];

    protected $validationRules = [
        'id'        => 'permit_empty|is_natural_no_zero',
        'numero'    => 'required|is_natural_no_zero|less_than[1000]|is_unique[mesas.numero,id,{id}]',
        'capacidad' => 'required|is_natural_no_zero|less_than_equal_to[12]',
        'forma'     => 'required|in_list[cuadrada,redonda,rectangular]',
        'pos_x'     => 'required|is_natural|less_than_equal_to[1000]',
        'pos_y'     => 'required|is_natural|less_than_equal_to[640]',
    ];

    protected $validationMessages = [
        'numero' => [
            'required'           => 'El número es obligatorio.',
            'is_natural_no_zero' => 'El número debe ser mayor a cero.',
            'less_than'          => 'El número no puede superar 999.',
            'is_unique'          => 'Ya existe una mesa con ese número, revisá también las dadas de baja.',
        ],
        'capacidad' => [
            'required'           => 'La capacidad es obligatoria.',
            'is_natural_no_zero' => 'La capacidad debe ser mayor a cero.',
            'less_than_equal_to' => 'La capacidad máxima es de 12 personas.',
        ],
        'forma' => [
            'required' => 'Elegí una forma.',
            'in_list'  => 'La forma no es válida.',
        ],
        'pos_x' => [
            'required'           => 'Falta la posición en el plano.',
            'is_natural'         => 'La posición no es válida.',
            'less_than_equal_to' => 'La mesa queda fuera del plano.',
        ],
        'pos_y' => [
            'required'           => 'Falta la posición en el plano.',
            'is_natural'         => 'La posición no es válida.',
            'less_than_equal_to' => 'La mesa queda fuera del plano.',
        ],
    ];

    // Mesas con su visita abierta
    public function listarConEstado(bool $incluirBajas = false): array
    {
        $consulta = $this->select('mesas.*, v.id AS visita_id, v.creado_en AS visita_desde, v.total AS visita_total')
            ->join(
                'mesa_visita v',
                "v.mesa_id = mesas.id AND v.estado = 'abierta' AND v.eliminado_en IS NULL",
                'left',
                false,
            );

        if ($incluirBajas) {
            $consulta->withDeleted();
        }

        return $consulta->orderBy('mesas.numero')->findAll();
    }

    // Visita abierta de una mesa
    public function visitaAbierta(int $mesaId): ?array
    {
        return $this->db->table('mesa_visita')
            ->where('mesa_id', $mesaId)
            ->where('estado', 'abierta')
            ->where('eliminado_en', null)
            ->get()
            ->getRowArray();
    }
}

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
