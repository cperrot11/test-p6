<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateBossCommand extends Command
{
    protected static $defaultName = 'app:create-boss';
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
        $this
            ->setDescription('Permet de créer un compte administrateur')
            ->addArgument('name', InputArgument::REQUIRED, 'Nom de contact : admin par défaut')
            ->addOption('email', 'm', InputOption::VALUE_OPTIONAL, 'Adresse e-mail', 'admin@trick.com')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'Password','admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');
        $email = $input->getOption('email');
        $password =  $input->getOption('password');
        $helper = $this->getHelper('');


        if (!$name) {
            $name = 'admin';
        }
        $io->note(sprintf('Nom de l\'administrateur: %s', $name));

        $output->writeln('Adresse mail = '.$email);
        $output->writeln('Mot de passe = '.$password);

        $creation = new ConfirmationQuestion('Valider la création de l\'administrateur ?', false);

        if ($helper->ask($input, $output, $creation)){
            $this->user->setName($name)
                        ->setEmail($email)
                        ->setPassword($this->encoder->encodePassword($this->user, $password))
                        ->setRoles(['ROLE_ADMIN']);
            $this->manager->persist($this->user);
            $this->manager->flush();

            $io->success('L\'utilsateur a été créé.');
        }
        else
        {
            $io->caution('Création annulée.');
        }

    }
}
