<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo base del que heredan todos los modelos del sistema.
 * Configura la baja lógica, las fechas automáticas y registra
 * cada alta, modificación, baja y restauración en la tabla auditoria.
 */
abstract class ModeloBase extends Model
{
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField   = 'eliminado_en';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'creado_en';
    protected $updatedField  = 'actualizado_en';

    // Eventos del modelo que disparan la auditoría
    protected $afterInsert = ['auditarAlta'];
    protected $afterUpdate = ['auditarModificacion'];
    protected $afterDelete = ['auditarBaja'];

    /**
     * Restaura un registro dado de baja y lo registra en la auditoría.
     */
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

    /**
     * Inserta una fila en la tabla auditoria con la acción realizada.
     */
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
