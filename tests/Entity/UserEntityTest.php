<?php


namespace App\Tests\Entity;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{
    public function testIsInvalidDefaultEntity()
    {
        $this->assertHasErrors($this->getEntity(), 8);
    }

    public function testValidEntity()
    {
        $entity = $this->getEntity();
        $entity->setPassword('password')
            ->setLastname('lastname')
            ->setFirstname('firstname')
            ->setEmail('email@email.com')
            ->setBirthday(new \DateTime('2005-01-01'));

        $this->assertHasErrors($entity, 0);
    }

    public function testInvalidEntityWithData()
    {
        $entity = $this->getEntity();
        $entity->setPassword('')
            ->setLastname('')
            ->setFirstname('')
            ->setEmail('badEmail')
            ->setBirthday(new \DateTime('2005-01-01'));
        
        $this->assertHasErrors($entity, 5);
    }

    public function testInvalidEntityCauseOfBirthdayDate()
    {
        $entity = $this->getEntity();
        $entity->setPassword('password')
            ->setLastname('lastname')
            ->setFirstname('firstname')
            ->setEmail('email@email.com')
            ->setBirthday(new \DateTime('2020-01-01'));

        $this->assertHasErrors($entity, 1);
    }

    /**
     * @return User
     */
    private function getEntity(): User
    {
        return new User();
    }

    /**
     * @param User $user
     * @param int $numbers
     */
    public function assertHasErrors(User $user, int $numbers)
    {
        self::bootKernel();
        $validator = self::$container->get(ValidatorInterface::class);
        $errors = $validator->validate($user);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($numbers, $errors, implode(', ', $messages));
    }
}