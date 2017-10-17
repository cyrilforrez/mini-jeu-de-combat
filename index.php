<?php

$db = new PDO('mysql:host=localhost;dbname=minijeudecombat', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.


class character
{
    private $_damages;
    private $_id;
    private $_name;

    const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même.
  const charactNNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le charactnnage en le frappant.
  const charactNNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le charactnnage.


  public function __construct(array $donnees)
  {
      $this->hydrate($donnees);
  }

    public function hit(character $charact)
    {
        if ($charact->id() == $this->_id) {
            return self::CEST_MOI;
        }

        // On indique au character qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode : self::character_TUE ou self::charactNNAGE_FRAPPE
        return $charact->receive_damage();
    }

    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function receive_damage()
    {
        $this->_damages += 5;

        // Si on a 100 de dégâts ou plus, on dit que le charactnnage a été tué.
        if ($this->_damages >= 100) {
            return self::charactNNAGE_TUE;
        }

        // Sinon, on se contente de dire que le charactnnage a bien été frappé.
        return self::charactNNAGE_FRAPPE;
    }


    // GETTERS //


    public function getDamages()
    {
        return $this->_damages;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setDamages($damages)
    {
        $damages = (int) $damages;

        if ($damages >= 0 && $damages <= 100) {
            $this->_damages = $damages;
        }
    }

    public function setId($id)
    {
        $id = (int) $id;

        if ($id > 0) {
            $this->_id = $id;
        }
    }

    public function setName($name)
    {
        if (is_string($name)) {
            $this->_name = $name;
        }
    }
}

class CharactersManager
{
    private $_db;

    public function __construct($db)
    {
        $this->setDb($db);
    }



    public function add(character $charact)
    {
      $q = $this->_db->prepare('INSERT INTO peoples(name) VALUES(:name)');
      $q->bindValue(':name', $charact->name());
      $q->execute();

      $charact->hydrate([
        'id' =>$this->_db->lastInsertId(),
        'damages' => 0,
      ]);
    }



    public function count()
    {
      return $this->_db->query('SELECT COUNT(*) FROM peoples')->fecthColumn();
    }



    public function delete(character $charact)
    {
      $this->_db->exec('DELETE FROM peoples WHERE id = '.$charact->id());
    }



    public function exists($info)
    {
      if (is_int($info)) {
        return (bool) $this->_db->query('SELECT COUNT(*) FROM peoples WHERE id ='.$info)->fecthColumn();
      }
      $q = $this->_db->prepare('SELECT COUNT(*) FROM peoples WHERE name = :name');
      $q->execute([':name' => $info]);

      return (bool) $q->fecthColumn();
    }



    public function get($info)
    {
      if (is_int($info)) {
        $q = $this->_db->query('SELECT id, name, damages FROM peoples WHERE id = '.$info);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);

        return new character($donnees);
      }
      else {
        $q = $this->_db->prepare('SELECT id, name, damages FROM peoples WHERE name = :name');
        $q->execute([':name' => $info]);

        return new character($q->fecth(PDO::FECTH_ASSOC));
      }
    }



    public function getList($name)
    {
      $charact = [];

      $q = $this->_db->prepare('SELECT id, name, damages FROM peoples WHERE name <> :name ORDER BY name');
      $q->execute(['name' => $name]);
      while ($donnees = $q->fecth(PDO::FETCH_ASSOC)) {
        $charact[] = new character($donnees);
      }
      return $charact;
    }



    public function update(character $charact)
    {
      $q = $this->_db->prepare('UPDATE peoples SET damages = :damages WHERE id = :id');

      $q->bindValue('damages', $charact->damages(), PDO::PARAM_INT);
      $q->bindValue(':id', $charact->id(), PDO::PARAM_INT);

      $q->execute();
    }



    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
}

$manager = new CharactersManager($db);
if (isset($_POST['create']) && isset($_POST['name']))
{
  $charact = new character(['name' =>$_POST['name']]);
  if (!$charact->nameValid()) {
    $message = 'The chosen name is invalid.';
    unset($charact);
  }
  elseif ($manager->exists($charact->name())) {
    $message ='The character name is already taken.';
    unset($charact);
  }
  else {
    $manager->add($charact);
  }
}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>TP : Mini jeu de combat</title>



    <meta charset="utf-8" />

  </head>

  <body>

    <form action="" method="post">

      <p>

        Nom : <input type="text" name="nom" maxlength="50" />

        <input type="submit" value="Créer ce personnage" name="creer" />

        <input type="submit" value="Utiliser ce personnage" name="utiliser" />

      </p>

    </form>

  </body>

</html>
