<?php

namespace App\DataFixtures;

use App\Entity\Level;
use App\Entity\Parameters;
use App\Entity\Professor;
use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Parameters
        $params = new Parameters();
        $params->setStudentEmailDomain('@lycee-faure.fr');
        $params->setProfessorEmailDomain('@ac-grenoble.fr');
        $params->setProvisorName('Catherine Deleurence');
        $params->setProvisorEmail('catherine.deleurence@ac-grenoble.fr');
        $params->setDdfptName('Sabine Trupin');
        $params->setDdfptEmail('sabine.trupin@ac-grenoble.fr');
        $params->setSchoolAddress('73 Bd de la Prairie au Duc, 44200 Nantes');
        $manager->persist($params);

        // 2. Levels
        $levelsData = [
            ['code' => 'SIO1', 'name' => 'BTS SIO1'],
            ['code' => 'SIO2', 'name' => 'BTS SIO2'],
            ['code' => 'GPME1', 'name' => 'BTS GPME 1'],
            ['code' => 'GPME2', 'name' => 'BTS GPME2'],
        ];

        $levels = [];
        foreach ($levelsData as $data) {
            $level = new Level();
            $level->setLevelCode($data['code']);
            $level->setLevelName($data['name']);
            $manager->persist($level);
            $levels[] = $level;
        }

        // 3. Professors
        $professorsData = [
            ['email' => 'jean.dupont@ac-grenoble.fr', 'fname' => 'Jean', 'lname' => 'Dupont', 'levels' => [0, 1]],
            ['email' => 'marie.durand@ac-grenoble.fr', 'fname' => 'Marie', 'lname' => 'Durand', 'levels' => [1, 2]],
            ['email' => 'pierre.martin@ac-grenoble.fr', 'fname' => 'Pierre', 'lname' => 'Martin', 'levels' => [3]],
        ];

        foreach ($professorsData as $data) {
            $prof = new Professor();
            $prof->setEmail($data['email']);
            $prof->setFirstname($data['fname']);
            $prof->setLastname($data['lname']);
            $prof->setRoles(['ROLE_PROFESSOR']);
            $prof->setPassword($this->passwordHasher->hashPassword($prof, 'password'));
            $prof->setIsVerified(true);
            
            foreach ($data['levels'] as $idx) {
                $prof->addLevel($levels[$idx]);
                $levels[$idx]->addReferentProfessor($prof); // Also set as referent for simplicity
            }
            
            $manager->persist($prof);
        }

        // 4. Students
        $studentsData = [
            ['email' => 'alice.bernard@lycee-faure.fr', 'fname' => 'Alice', 'lname' => 'Bernard', 'level' => 0],
            ['email' => 'bob.petit@lycee-faure.fr', 'fname' => 'Bob', 'lname' => 'Petit', 'level' => 1],
            ['email' => 'charlie.roux@lycee-faure.fr', 'fname' => 'Charlie', 'lname' => 'Roux', 'level' => 1],
            ['email' => 'david.moreau@lycee-faure.fr', 'fname' => 'David', 'lname' => 'Moreau', 'level' => 2],
            ['email' => 'eve.legrand@lycee-faure.fr', 'fname' => 'Eve', 'lname' => 'Legrand', 'level' => 3],
        ];

        foreach ($studentsData as $data) {
            $student = new Student();
            $student->setEmail($data['email']);
            $student->setFirstname($data['fname']);
            $student->setLastname($data['lname']);
            $student->setRoles(['ROLE_STUDENT']);
            $student->setLevel($levels[$data['level']]);
            $student->setPassword($this->passwordHasher->hashPassword($student, 'password'));
            $student->setIsVerified(true);
            $student->setPersonalEmail(str_replace('@lycee-faure.fr', '@gmail.com', $data['email']));
            
            $manager->persist($student);
        }

        $manager->flush();
    }
}
