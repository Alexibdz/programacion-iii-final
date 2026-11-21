<?php

namespace App\Models;

use CodeIgniter\Model;

// Modelo base con baja lógica y auditoría
abstract class ModeloBase extends Model
{
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField   = 'eliminado_en';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'creado_en';
    protected $updatedField  = 'actualizado_en';

    // Eventos de auditoría
    protected $afterInsert = ['auditarAlta'];
    protected $afterUpdate = ['auditarModificacion'];
    protected $afterDelete = ['auditarBaja'];

    // Restauración de un registro dado de baja
    public function restaurar(int $id): bool
    {
        $registro = $this->withDeleted()->find($id);

        if ($registro === null || $registro[$this->deletedField] === null) {
            return false;
        }

        $this->builder()
            ->where($this->primaryKey, $id)
            ->update([$this->deletedField => null]);

        $this->registrarAuditoria('RESTAURACION', $id);

        return true;
    }

    protected function auditarAlta(array $evento): array
    {
        if ($evento['result']) {
            $this->registrarAuditoria('ALTA', $evento['id']);
        }

        return $evento;
    }

    protected function auditarModificacion(array $evento): array
    {
        if ($evento['result']) {
            foreach ($evento['id'] ?? [] as $id) {
                $this->registrarAuditoria('MODIFICACION', $id);
            }
        }

        return $evento;
    }

    protected function auditarBaja(array $evento): array
    {
        if ($evento['result']) {
            foreach ($evento['id'] ?? [] as $id) {
                $this->registrarAuditoria('BAJA', $id);
            }
        }

        return $evento;
    }

    // Registro en auditoría
    protected function registrarAuditoria(string $accion, int $registroId): void
    {
        $this->db->table('auditoria')->insert([
            'tabla'       => $this->table,
            'registro_id' => $registroId,
            'accion'      => $accion,
            'usuario_id'  => session('usuario_id'),
        ]);
    }
}
