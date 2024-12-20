<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:send-test-email')]
class SendTestEmailCommand extends Command
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('test@example.com')
            ->to('recipient@example.com')
            ->subject('Test email')
            ->text('This is a test email.')
            ->html('<p>This is a test email.</p>');

        $this->mailer->send($email);
        $output->writeln('Test email sent successfully.');

        return Command::SUCCESS;
    }
}
