<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Mini Jeuc de combat</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="css/materialize.min.css">
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/main.css">
</head>

<body>
<?php

  class people
  {
    private $_id;
    private $_name;
    private $_damage;

    const CEST_MOI = 1; //Constant returned by the `hit 'method if you hit yourself.
    const PEOPLE_KILL = 2; //Constant returned by the `hit 'method if the character was killed by hitting it.
    const PEOPLE_HIT = 3; //Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.

    public function __construct(array $donnees)
    {
      $this->hydrate($donnees);
    }

    public function hit(People $people1)
    {
      if ($people1->id() == $this->_id) {
        return self::CEST_MOI;
      }
    }
    // The character is told he must receive damage.

    // Then we return the value returned by the method: self :: CHARACTER_TUE or self :: PERSONNAGE_FRAPPE

    public function hydrate(array $donnees)
    {
      foreach ($donnees as $key => $value) {
        $method = 'set'.ucfirst($key);
        if (method_exists($this, $method)) {
          $this->$method($value);
        }
      }
    }

    public function receiveDamage()
    {
      $this->_damage += 5;
      // If we have 100 damage or more, we say that the character has been killed.
      if ($this->_damage >= 100) {
        return self::PEOPLE_KILL;
      }
      // Otherwise, we just say that the character has been hit.
      return self::PEOPLE_HIT;
    }

    public function getId()
    {
      return $this->_id;
    }

    public function getName()
    {
      return $this->_name;
    }

    public function getDamage()
    {
      return $this->_damage;
    }

    public function setId($id)
    {
      $id = (int) $id;
      if ($id > 0) {
        $this-> id= $id;
      }
    }

    public function setName($name)
    {
      if (is_string($name)) {
        $this->_name = $name;
      }
    }

    public function setDamage($damage)
    {
      $damage = (int) $damage;
      if ($damage >= 0 && $damage <= 100) {
        $this->_damage =$damage;
      }
    }
  }

  class peoplemanager
  {
    private $_db;

    public function add(people $people1)
    {
      $q = $this->_db->prepare('INSERT INTO people(name) VALUES(:name)');
      $q->bindValue(':name', $people1->name());
      $q->execute();

      $people1->hydrate([
        'id'=> $this->_db->lastInsertId(),
        'damage' => 0,
      ]);
    }

    public function count()
    {
      return $this->_db->query('SELECT COUNT(*) FROM people WHERE id = '.$info)->fetchColumn();
    }

    public function delete(people $people1)
    {
      $this->_db->exec('DELETE FROM people WHERE id = '.$people1->id());
    }

    public function exists($info)
    {
      if (is_int($info)) {
        return (bool) $this->_db->query('SELECT COUNT(*)FROM people WHERE id = '.$info)->fecthColumn();
      }

      $q = $this->_db->prepare('SELECT COUNT(*)FROM people WHERE name = :name');
      $q-> execute([':name => $info']);
      return (bool) $q-> fetchColumn();
    }

    public function get($info)
    {
      if (is_int($info)) {
        $q = $this->_db->query('SELECT id, name, damage FROM poeple WHERE id = '.$infos);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        return new people($donnees);
      } else {
        $q = $this->_db->prepare('SELECT id, name, damage FROM people WHERE name = :name');
        $q->execute ([':name' => $info]);
        return new poeple($q-> fecth(PDO::FETCH_ASSOC));
      }
    }

    public function getList($name)
    {
      $persos = [];

      $q = $this->_db->prepare('SELECT id, name, damage FROM poeple WHERE name <> :name ORDER BY name');
      $q ->execute([':name' => $name]);

      while ($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
        $persos[] = new poeple($donnees);
      }

    return $persos;
    }

    public function update(people $people1)
    {
      $q = $this->_db->prepare('UPDATE people SET damage = :damage WHERE id = :id');
      $q->bindValue(':damage', $people1->damage(), PDO::PARAM_INT);
      $q->binValue(':id', $people1->id(), PDO::PARAM_INT);

      $q->execute();
    }

    public function setDb(PDO $db)
    {
      $this->_db = $db;
    }
  }




$people1 = new people($donnees);
echo $people1->name(),$people1->damage() ;
 ?>



  <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="js/vendor/modernizr-2.8.3.min.js"></script>
  <script src="js/materialize.min.js"></script>
</body>

</html>
