<?php
/**
 * Short description
 *
 * PHP version 7.2
 *
 * @category
 * @package
 * @author Christophe PERROTIN
 * @copyright 2018
 * @license MIT License
 * @link http://wwww.perrotin.eu
 */

namespace App\Command;


use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdmin extends Command
{
    protected static $defaultName = 'app:create-user';
    private $user;
    private $encoder;
    private $manager;


    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, ObjectManager $manager)
    {
        $this->user = new User();
        $this->encoder = $userPasswordEncoder;
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Création d\'un nouvel administrateur')
            ->setHelp('Va permettre les interraction avec la base de donnée')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Admin Creator',
            '=============',
            '',
        ]);
        $section1 = $output->section();
        $section2 = $output->section();
        $section1->writeln('Hello');
        $section2->writeln('World!');
        $output->writeln('Username: '.$input->getArgument('username'));
        $this->user->setName($input->getArgument('username'))
            ->setPassword($this->encoder->encodePassword($this->user, '123456'))
            ->setEmail('toto@gmail.com');

        $this->manager->persist($this->user);
        $this->manager->flush();
    }

}