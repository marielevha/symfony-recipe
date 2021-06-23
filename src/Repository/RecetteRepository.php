<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    // /**
    //  * @return Recette[] Returns an array of Recette objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recette
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllWithPaginate($page, $limit = 12)
    {
        $recipes = [];
        $query = $this->createQueryBuilder('r')
            ->orderBy('r.id', 'desc')
            ->getQuery()
            ;
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = intval(ceil($totalItems / $limit));

        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page-1)) // set the offset
            ->setMaxResults($limit); // set the limit

        foreach ($paginator as $pageItem) {
            array_push($recipes, $pageItem);
        }

        return [
            'data' => $recipes,
            'current_page' => $page,
            'last_page' => $pagesCount,
            'total_pages' => $pagesCount,
            'total_items' => $totalItems,
        ];
    }

    public function latest_recipes($limit, $category = null): array
    {
        $criteria = [];
        if ($category != null) {
            $criteria = ['categorie' => $category];
        }


        $recipes = $this->findBy(
            $criteria,
            ['date' => 'desc'],
            $limit,
            0
        );
        return $recipes;
    }

    public function popular_recipes($limit): array
    {
        $recipes = $this->findBy(
            [],
            ['id' => 'desc']
        );

        foreach ($recipes as $recipe) {
            $recipe->rating = $this->avg_rating($recipe->getNotes());
        }
        usort($recipes, function($a, $b)
        {
            return strcmp($b->rating, $a->rating);
        });

        $_recipes = []; $count = 1;
        foreach ($recipes as $recipe) {
            if ($count <= $limit) {
                array_push($_recipes, $recipe);
            }
            $count ++;
        }

        return $_recipes;
    }

    private function avg_rating($notes): int
    {
        $rating = 0;
        foreach ($notes as $note) {
            $rating += $note->getValeur();
        }
        return $rating == 0 ? 0 : intval(ceil($rating / $notes->count()));
    }

    public function findSearch(SearchData $search)
    {
        $query = $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->join('r.categorie', 'c')
        ;

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('r.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        if (!empty($search->categorie)) {
            $query = $query
                ->andWhere('r.categorie = :cat')
                ->setParameter('cat', $search->categorie);
        }

        return $query->getQuery()->getResult();
    }
}
