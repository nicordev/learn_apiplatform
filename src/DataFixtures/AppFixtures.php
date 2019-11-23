<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    private $users = [];
    private $customers = [];

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = Factory::create('fr_FR');

        $this->loadUsers(10);
        $this->loadCustomers(5, 20);
        $this->loadInvoices(3, 10);

        $this->manager->flush();
    }

    public function loadUsers(int $userCount)
    {
        for ($i = 0; $i < $userCount; $i++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, "password");
            $user->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName)
                ->setEmail($this->faker->email)
                ->setPassword($hash);
            $this->users[] = $user;

            $this->manager->persist($user);
        }
    }

    public function loadCustomers(int $minimumPerUser, int $maximumPerUser)
    {
        foreach ($this->users as &$user) {
            for ($i = 0; $i < mt_rand($minimumPerUser, $maximumPerUser); $i++) {
                $customer = new Customer();
                $customer->setFirstName($this->faker->firstName())
                    ->setLastName($this->faker->lastName)
                    ->setCompany($this->faker->company)
                    ->setEmail($this->faker->email)
                    ->setUser($user);
                $user->addCustomer($customer); // Useless for the database, just better for my brain.
                $this->customers[] = $customer;

                $this->manager->persist($customer);
            }
        }
    }
    
    public function loadInvoices(int $minimumPerCustomer, int $maximumPerCustomer)
    {
        $chronos = array_fill(0, count($this->users), 1); // We need as many chronos as users
        $currentChrono = 0;

        for ($c = 0, $customerCount = count($this->customers); $c < $customerCount; $c++) {

            if ($c > 0 && $this->customers[$c]->getUser() != $this->customers[$c - 1]->getUser()) {
                $currentChrono++;
            }

            for ($i = 0; $i < mt_rand($minimumPerCustomer, $maximumPerCustomer); $i++) {

                $invoice = new Invoice();
                $invoice->setAmount($this->faker->randomFloat(2, 250, 5000))
                    ->setSentAt($this->faker->dateTimeBetween('-6 months'))
                    ->setStatus($this->faker->randomElement([
                        'SENT', 'PAID', 'CANCELLED'
                    ]))
                    ->setCustomer($this->customers[$c])
                    ->setChrono($chronos[$currentChrono]++);
                $this->customers[$c]->addInvoice($invoice); // Useless for the database, just better for my brain.

                $this->manager->persist($invoice);
            }
        }
    }
}

/*
    public function loadOld(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $chrono = 1;

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword("password");

            $manager->persist($user);

            for ($i = 0; $i < mt_rand(5, 20); $i++) {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setCompany($faker->company)
                    ->setEmail($faker->email);

                $manager->persist($customer);

                for ($j = 0; $j < mt_rand(3, 10); $j++) {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement([
                            'SENT', 'PAID', 'CANCELLED'
                        ]))
                        ->setCustomer($customer)
                        ->setChrono($chrono++);

                    $manager->persist($invoice);
                }
            }
        }

        $manager->flush();
    }
 */
