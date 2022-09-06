<?php

namespace App\DataFixtures;

//use DateTime;
use App\Entity\User;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFixtures extends Fixture
{

    private $clientPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $clientPasswordHasher)
    {
        $this->clientPasswordHasher = $clientPasswordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        //$createdAt  = new DateTime();
        // $product = new Product();
        // $manager->persist($product);
        //dd(new \DateTime());
        // Création d'un client "normal"
        $clientOne = new client();
        $clientOne->setEmail("user@apiclientone.com");
        $clientOne->setRoles(["ROLE_USER"]);
        $clientOne->setCompanyName("companyone");
        $clientOne->setPassword($this->clientPasswordHasher->hashPassword($clientOne, "userpassword"));
        //$clientOne->setCreatedAt(new \DateTime());
        $manager->persist($clientOne);
        
        // Création d'un client admin
        $clientAdmin = new client();
        
        $clientAdmin->setEmail("admin@apiclientone.com");
        $clientAdmin->setPassword($this->clientPasswordHasher->hashPassword($clientAdmin, "adminpassword"));
        $clientAdmin->setRoles(["ROLE_ADMIN"]);
        $clientAdmin->setCompanyName("companyone");
        //$clientAdmin->setCompanyName("client");
        //$clientAdmin->setCreatedAt(new \DateTime());
        $manager->persist($clientAdmin);


        // Création des client.
        $listClient = [];
        for ($i = 0; $i < 10; $i++) {
            // Création du client.
            $client = new Client(); 
            $client->setEmail("client". $i."@apiclient". $i.".com");
            $client->setRoles(["ROLE_USER"]);
            $client->setPassword($this->clientPasswordHasher->hashPassword($client, "clientpassword"));
            $client->setCompanyName("company".$i);
            //$client->setCreatedAt(new \DateTime());
            $manager->persist($client);
            // On sauvegarde l'auteur créé dans un tableau.
            $listClient[] = $client;
        }

        // Création de cinq utilisateur relié au clientOne'
        

        for ($i = 0; $i < 5; $i++) {
            $user = new User;
            $user->setEmail("user".$i."@apiuser".$i.".com")
                ->setCreatedAt(new \DateTime())
                ->setFirstName("user".$i."first")
                ->setLastName("user".$i."last")
                ->setComment("commentaire nuéro : ".$i);
                $user->setclient($clientOne);

            $manager->persist($user);
        }

        // Création d'une vingtaine d'utilisateur'
        
        for ($i = 5; $i < 20; $i++) {
            $user = new User;
            $user->setEmail("user".$i."@apiuser".$i.".com");
            $user->setCreatedAt(new \DateTime());
            $user->setFirstName("user".$i."first");
            $user->setLastName("user".$i."last");
            $user->setComment("commentaire nuéro : ".$i);
            /* $user->setTitle('Livre ' . $i);
            $user->setCoverText('Quatrième de couverture numéro : ' . $i); */
             // On lie le livre à un auteur pris au hasard dans le tableau des auteurs.
         
            $user->setclient($listClient[array_rand($listClient)]);

            $manager->persist($user);
        }

        
        
        $manager->flush();
    }
}
