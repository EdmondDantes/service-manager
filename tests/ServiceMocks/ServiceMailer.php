<?php
declare(strict_types=1);

namespace IfCastle\ServiceManager\ServiceMocks;

use IfCastle\ServiceManager\ServiceMethod;

class ServiceMailer
{
    public array $sendLog = [];
    
    #[ServiceMethod]
    public function sendMail(string $to, string $subject, string $message): void
    {
        $this->sendLog[] = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message
        ];
    }
    
    #[ServiceMethod]
    public function isMailSent(string $to, string $subject, string $message): bool
    {
        return in_array([
            'to' => $to,
            'subject' => $subject,
            'message' => $message
        ], $this->sendLog);
    }
    
    #[ServiceMethod]
    public function getSendLog(): array
    {
        return $this->sendLog;
    }
}