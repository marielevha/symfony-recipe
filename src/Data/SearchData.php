<?php


namespace App\Data;


use App\Entity\Categorie;
use Doctrine\Common\Collections\Collection;

class SearchData
{
    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Categorie
     */
    public $categorie;
}