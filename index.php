<?php

$db = new PDO('mysql:host=localhost;dbname=minijeudecombat', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.


class character
{
  private $_damages,
          $_id,
          $_name;

  const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même.
  const charactNNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le charactnnage en le frappant.
  const charactNNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le charactnnage.


  public function __construct(array $donnees)
  {
    $this->hydrate($donnees);
  }

  public function hit(character $charact)
  {
    if ($charact->id() == $this->_id)
    {
      return self::CEST_MOI;
    }

    // On indique au character qu'il doit recevoir des dégâts.
    // Puis on retourne la valeur renvoyée par la méthode : self::character_TUE ou self::charactNNAGE_FRAPPE
    return $charact->receive_damage();
  }

  public function hydrate(array $donnees)
  {
    foreach ($donnees as $key => $value)
    {
      $method = 'set'.ucfirst($key);

      if (method_exists($this, $method))
      {
        $this->$method($value);
      }
    }
  }

  public function receive_damage()
  {
    $this->_damages += 5;

    // Si on a 100 de dégâts ou plus, on dit que le charactnnage a été tué.
    if ($this->_damages >= 100)
    {
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

    if ($damages >= 0 && $damages <= 100)
    {
      $this->_damages = $damages;
    }
  }

  public function setId($id)
  {
    $id = (int) $id;

    if ($id > 0)
    {
      $this->_id = $id;
    }
  }

  public function setName($name)
  {
    if (is_string($name))
    {
      $this->_name = $name;
    }
  }
}
