<?php
namespace App\Classe;

use App\Entity\Category;

class search
{
    /**
     * @var string
     */
    public $string = ''; // Propriété string qui contiendra la chaîne de recherche

    /**
     * @var Category[]
     */
    public $categories = []; // Propriété categories qui contiendra les catégories de recherche à selectionner
}