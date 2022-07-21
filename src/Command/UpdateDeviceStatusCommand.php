<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDeviceStatusCommand extends Command
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('CustomCommand:Update-Device-Status-Command')->
        setDescription('Checks whether more than 24 hours have passed since taking the device. 
        If yes, sends the user an email with a request to return the device');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $logRepository = $this->entityManager->getRepository(\App\Entity\Device\DeviceUsingLog::class);


        $now = new \DateTime(date("Y-m-d H:i:s"));

        foreach ($logRepository as $log) {

            $pickUpDate = \DateTime::createFromFormat('Y-m-d H:i:s', $log->pickUpDate);
            $diff = $now->diff($pickUpDate);
            $timeCriteria = \DateTime::createFromFormat("Y-m-d H:i:s", '0-0-1 0:0:0');

            if ($diff->format('%Y-%m-%d %H:%i:%s') >= $timeCriteria) {

                $device = $log->getDevice();
                $device->takenАway = 1;
                $device->returnedIn24Hours = 0;

                $this->entityManager->persist($device);
                $this->entityManager->flush();
                $this->sendEmail('example@example.com')

            } else {

                $device = $log->getDevice();
                $device->takenАway = 1;
                $device->returnedIn24Hours = 1;

                $this->entityManager->persist($device);
                $this->entityManager->flush();
            }


        }
        $output->writeln('all is fine ');

    }

    public function sendEmail(string $email)
    {
        $text = "Отдай девайс, поганец!!!! Если не вернешь его в течении 2 (двух) дней, я наложу на тебя проклятие!!!";

        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'afterhattt@gmail.com';
        $mail->Password = 'something';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;


        $mail->CharSet = 'UTF-8';
        $mail->setLanguage('ru', 'phpmailer/language/');
        $mail->IsHTML(true);

        $mail->setFrom('mail@yandex.ru', 'anonymous');
        $mail->addAddress($email);

        $mail->Subject = 'Верни девайс!!!';


        $mail->AddAttachment($_FILES['file']['tmp_name'], $_FILES['file']['name']);


        $body = '<p>' . $text . '</p>';

        $mail->Body = $body;

        $mail->send();


    }