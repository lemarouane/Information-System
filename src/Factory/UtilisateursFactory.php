<?php

namespace App\Factory;

use App\Entity\Utilisateurs;
use App\Repository\UtilisateursRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Utilisateurs>
 *
 * @method        Utilisateurs|Proxy create(array|callable $attributes = [])
 * @method static Utilisateurs|Proxy createOne(array $attributes = [])
 * @method static Utilisateurs|Proxy find(object|array|mixed $criteria)
 * @method static Utilisateurs|Proxy findOrCreate(array $attributes)
 * @method static Utilisateurs|Proxy first(string $sortedField = 'id')
 * @method static Utilisateurs|Proxy last(string $sortedField = 'id')
 * @method static Utilisateurs|Proxy random(array $attributes = [])
 * @method static Utilisateurs|Proxy randomOrCreate(array $attributes = [])
 * @method static UtilisateursRepository|RepositoryProxy repository()
 * @method static Utilisateurs[]|Proxy[] all()
 * @method static Utilisateurs[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Utilisateurs[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Utilisateurs[]|Proxy[] findBy(array $attributes)
 * @method static Utilisateurs[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Utilisateurs[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class UtilisateursFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'email' => 'admin@fac.ma',
            'roles' => ['ROLE_ADMIN'],
            'password' => '$2y$13$w7usfxJhm1MP8qjT8TDNzOq.UuYWFuZszfwqX/agMwG8JeqWgacZ.',
            'nom_utilisateur' => 'Admin',
            'langue' => 'FR',
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Utilisateurs $utilisateurs): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Utilisateurs::class;
    }
}
