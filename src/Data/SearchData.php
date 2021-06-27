<?php


namespace App\Data;


use App\Entity\Categorie;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\Collection;

class SearchData
{
    /**
     * @var string
     */
    public $q = '';

    /**
     * @var string
     */
    public $level = '';

    /**
     * @var DateTime
     */
    public $start = null;

    /**
     * @var DateTime
     */
    public $end = null;

    /**
     * @var Categorie
     */
    public $categorie;

    /**
     * @var User
     */
    public $user;
}