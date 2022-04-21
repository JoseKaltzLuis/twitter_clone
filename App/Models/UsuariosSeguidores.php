<?php

namespace App\Models;

use MF\Model\Model;

class UsuariosSeguidores extends Model
{
    private $id;
    private $id_usuario;
    private $id_usuario_seguindo;

    public function __get($attribute)
    {
        return $this->$attribute;
    }

    public function __set($attribute, $value)
    {
        $this->$attribute = $value;
    }

    public function seguirUsuario($id_usuario_seguindo)
    {
        $query = "INSERT INTO usuarios_seguidores(id_usuario, id_usuario_seguindo) values (:id_usuario, :id_usuario_seguindo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
    }

    public function deixarSeguirUsuario($id_usuario_seguindo)
    {
        $query = "DELETE FROM usuarios_seguidores WHERE id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
    }

    
}