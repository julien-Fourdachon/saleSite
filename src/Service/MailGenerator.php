<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 7/23/18
 * Time: 10:05 AM
 */

namespace App\Service;

use App\Entity\User;


class MailGenerator
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig_Environment;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig_Environment)
    {

        $this->mailer = $mailer;
        $this->twig_Environment = $twig_Environment;
    }


    public function index(User $user)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom ('julien.fourdachon@gmail.com')
            ->setTo ($user->getMail())
            ->setBody (
                $this->twig_Environment->render(
                    'emails/registration.html.twig',
                    array('firstname' => $user->getFirstname())
                ),
                'text/html'
            );
        $this->mailer->send($message);

        return $this->twig_Environment->render ('emails/registration.html.twig', array(
            'firstname' => $user->getFirstname (),
        ));

    }
}