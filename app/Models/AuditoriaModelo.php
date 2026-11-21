<?php

namespace App\Models;

use CodeIgniter\Model;

// Registro de auditoría, sin eventos para no auditarse a sí mismo
class AuditoriaModelo extends Model
{
    protected $table      = 'auditoria';
    protected $returnType = 'array';

    // Movimientos con el nombre del usuario
    public function listarConUsuario(): array
    {
        return $this->select('auditoria.*, usuarios.nombre AS usuario')
            ->join('usuarios', 'usuarios.id = auditoria.usuario_id', 'left')
            ->orderBy('auditoria.id', 'DESC')
            ->findAll();
    }
}
