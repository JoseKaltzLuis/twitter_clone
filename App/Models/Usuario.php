<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;


    public function __get($attribute)
    {
        return $this->$attribute;
    }

    public function __set($attribute, $value)
    {
        $this->$attribute = $value;
    }

    public function salvar() {

		$query = "INSERT INTO usuarios(nome, email, senha)values(:nome, :email, :senha)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nome', $this->__get('nome'));
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		return $this;
	}

    public function validarCadastro()
    {
        $valido = true;

        if(strlen($this->__get('nome')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    public function getEmailValido()
    {
        $query = "SELECT nome, email from usuarios WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function autenticar()
    {

        $query = "SELECT * from usuarios where email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($usuario['id'] != '' && $usuario['nome'] != '') {
            if(password_verify($this->__get('senha'), $usuario['senha'])) {
                $this->__set('id', $usuario['id']);
                $this->__set('nome', $usuario['nome']);
            }
        }

        return $this;
    }

    public function getAll()
    {
        $query = "select 
        u.id, 
        u.nome, 
        u.email,
        (
            select
                count(*)
            from
                usuarios_seguidores as us 
            where
                us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
        ) as seguindo_sn
        from  
            usuarios as u
        where 
            u.nome like :nome and u.id != :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getInfoUsuario()
    {
        $query = "SELECT nome from usuarios where id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalTweets()
    {
        $query = "SELECT count(*) as total_tweet from tweets where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalSeguindo()
    {
        $query = "SELECT count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalSeguidores()
    {
        $query = "SELECT count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}